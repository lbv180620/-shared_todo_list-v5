<?php

/** auth */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

/** URL */
require_once dirname(__FILE__, 3) . '/App/Config/url_list.php';

/** DB操作関連で使用 */

use App\Models\Base;
use App\Models\TodoItems;

/** メッセージ関連で使用 */

use App\Config\Config;

use App\Utils\Common;
use App\Utils\Logger;
use App\Utils\SessionUtil;
use App\Utils\Validation;

// セッション開始
SessionUtil::sessionStart();
// サニタイズ
$post = Common::sanitize($_POST);
$post['finished'] = !empty($post['finished']) ? 1 : null; // "1" -> 1 に変換

// リダイレクト先のURL
$url = ENTRY_PAGE_URL;

// ワンタイムトークンチェック
// 「フォームからトークンから送信されていない」または「トークンが一致しない」場合
// 元のフォームにリダイレクト
if (!isset($post['token']) || !Common::isValidToken($post['token'])) {
    $_SESSION['err']['msg'] = Config::MSG_INVALID_PROCESS;
    Logger::errorLog(Config::MSG_INVALID_PROCESS, ['file' => __FILE__, 'line' => __LINE__]);
    header("Location: {$url}", true, 301);
    exit;
}

// バリデーション
$result = Validation::validateTodoFormRequest($post);
['err' => $err, 'fill' => $fill] = $result;

// 記入情報をサニタイズしてセッションに保存する
$fill['finished'] = !empty($fill['finished']) ? "1" : null; // 1 -> "1" に変換
if (!empty($fill)) {
    $_SESSION['fill'] = Common::sanitize($fill);
}

// エラーメッセージの処理
/**
 * エラーメッセージがある場合、
 * エラーメッセージをセッションに登録し、
 * 元のフォームへリダイレクト
 */
if (count($err) > 0) {
    $_SESSION['err'] = $err;
    header("Location: {$url}", true, 301);
    exit;
}

/**
 * エラーメッセージがない場合、
 * 作業登録情報をDBに登録する
 */
try {
    // DB接続処理
    $base = Base::getPDOInstance();
    $dbh = new TodoItems($base);

    // データベースに登録する内容を連想配列にする。
    $data = [
        'staff_id' => $post['staff_id'],
        'client_id' => $post['client_id'],
        'item_name' => $post['item_name'],
        'content' => $post['content'],
        'registration_date' => date('Y-m-d'),
        'expiration_date' => $post['expiration_date'],
        'finished_date' => isset($post['finished']) && $post['finished'] === 1 ? date('Y-m-d') : null,
    ];

    /** 作業登録処理 @param array $data @return bool */
    $ret = $dbh->registerTodoItem($data);

    // 登録に成功したかの確認
    if (!$ret) {

        $_SESSION['err']['msg'] = Config::MSG_TASK_REGISTRATION_FAILURE;
        Logger::errorLog(Config::MSG_TASK_REGISTRATION_FAILURE, ['file' => __FILE__, 'line' => __LINE__]);
        header("Location: " . TOP_PAGE_URL, true, 301);
        exit;
    }

    /**
     * 正常終了したときは、記入情報とエラーメッセージを削除して、ログイン画面にリダイレクトする。
     */
    unset($_SESSION['fill']);
    unset($_SESSION['err']);

    // 作業登録に成功した旨のメッセージをTOP画面にセッションで渡して、リダイレクト
    $_SESSION['success']['msg'] = Config::MSG_TASK_REGISTRATION_SUCCESSFUL . $dbh->getLastInsertedItemName();
    $_SESSION['success']['updated_item_id'] = $dbh->getLastInsertedItemId();

    header("Location: " . SUCCESS_MSG_DISPLAY_URL_FOR_AUTH, true, 301);
    exit;
} catch (\PDOException $e) {
    $_SESSION['err']['msg'] = Config::MSG_PDOEXCEPTION_ERROR;
    Logger::errorLog(Config::MSG_PDOEXCEPTION_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
    header("Location: " . ERROR_PAGE_URL, true, 301);
    exit;
} catch (\Exception $e) {
    $_SESSION['err']['msg'] = Config::MSG_EXCEPTION_ERROR;
    Logger::errorLog(Config::MSG_EXCEPTION_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
    header("Location: " . ERROR_PAGE_URL, true, 301);
    exit;
}
