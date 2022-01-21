<?php

/** guest */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

/** URL */
require_once dirname(__FILE__, 3) . '/App/Config/url_list.php';

/** DB操作関連で使用 */

use App\Models\Base;
use App\Models\Users;

/** メッセージ関連で使用 */

use App\Config\Config;

use App\Utils\Common;
use App\Utils\Logger;
use App\Utils\SessionUtil;
use App\Utils\Validation;

// セッション開始
SessionUtil::sessionStart();

// 正しいリクエストかチェック
if (!Common::isValidRequest('POST')) {
    $_SESSION['err']['msg'] = Config::MSG_INVALID_REQUEST;
    header("Location: " . SIGNUP_PAGE_URL, true, 301);
    exit;
}

// サニタイズ
$post = Common::sanitize($_POST);

// ワンタイムトークンチェック
// 「フォームからトークンから送信されていない」または「トークンが一致しない」場合
// ログインフォームにリダイレクト
if (!isset($post['token']) || !Common::isValidToken($post['token'])) {
    $_SESSION['err']['msg'] = Config::MSG_INVALID_PROCESS;
    header("Location: " . SIGNUP_PAGE_URL, true, 301);
    exit;
}

// バリデーション
$result = Validation::validateSignUpFormRequest($post);
['err' => $err, 'fill' => $fill] = $result;

// 記入情報をサニタイズしてセッションに保存する
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
    header("Location: " . SIGNUP_PAGE_URL, true, 301);
    exit;
}

/**
 * エラーメッセージがない場合、
 * ユーザ情報をDBに登録する
 */
try {
    // DB接続処理
    $base = Base::getPDOInstance();
    $dbh = new Users($base);

    /** ユーザ登録処理 @param array $post @return bool */
    $ret = $dbh->addUser($post);

    // 登録に成功したかの確認
    if (!$ret) {
        /**
         * 同一のメールアドレスのユーザーがすでにいた場合、
         * エラーメッセージをセッションに登録して、新規登録画面にリダイレクト
         */
        $_SESSION['err']['msg'] = Config::MSG_USER_DUPLICATE;
        header("Location: " . SIGNUP_PAGE_URL, true, 301);
        exit;
    }

    /**
     * 正常終了したときは、記入情報とエラーメッセージを削除して、ログイン画面にリダイレクトする。
     */
    unset($_SESSION['fill']);
    unset($_SESSION['err']);
    // ログイン状態で新規登録に成功した場合、今のログイン情報は削除するようにする。
    unset($_SESSION['login']);

    // 新規登録に成功した旨のメッセージをログイン画面にセッションで渡して、リダイレクト
    $_SESSION['success']['msg'] = Config::MSG_NEW_REGISTRATION_SUCCESSFUL;
    header("Location: " . SUCCESS_MSG_DISPLAY_URL_FOR_GUEST, true, 301);
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
