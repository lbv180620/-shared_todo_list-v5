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

// セッション開始
SessionUtil::sessionStart();
// サニタイズ
$post = Common::sanitize($_POST);
$post['finished'] = !empty($post['finished']) ? 1 : null; // "1" -> 1 に変換

// リダイレクト先のURL
$url = DELETE_PAGE_URL . "?item_id=" . $post['item_id'];

// ワンタイムトークンチェック
// 「フォームからトークンから送信されていない」または「トークンが一致しない」場合
// 元のフォームにリダイレクト
if (!isset($post['token']) || !Common::isValidToken($post['token'])) {
    $_SESSION['err']['msg'] = Config::MSG_INVALID_PROCESS;
    Logger::errorLog(Config::MSG_INVALID_PROCESS, ['file' => __FILE__, 'line' => __LINE__]);
    header("Location: {$url}", true, 301);
    exit;
}


try {
    // DB接続処理
    $base = Base::getPDOInstance();
    $dbh = new TodoItems($base);

    $item_id = $post['item_id'];
    /** 削除処理 @param int $item_id @return bool */
    $ret = $dbh->deleteTodoItemById($item_id);

    // 削除に成功したかの確認
    if (!$ret) {

        $_SESSION['err']['msg'] = Config::MSG_TASK_DELETE_FAILURE;
        Logger::errorLog(Config::MSG_TASK_DELETE_FAILURE, ['file' => __FILE__, 'line' => __LINE__]);
        header("Location: " . TOP_PAGE_URL, true, 301);
        exit;
    }

    /**
     * 念のため削除
     */
    unset($_SESSION['fill']);
    unset($_SESSION['err']);

    // 修正に成功した旨のメッセージをTOP画面にセッションで渡して、リダイレクト
    $_SESSION['success']['msg'] = Config::MSG_TASK_DELETE_SUCCESSFUL . $dbh->getItemNameById($item_id);

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
