<?php

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Models\Users;
use App\Models\Base;
use App\Config\Config;
use App\Utils\Common;
use App\Utils\SessionUtil;
use App\Utils\Validation;
use App\Utils\Logger;

// セッション開始
SessionUtil::sessionStart();
// サニタイズ
$post = Common::sanitize($_POST);

// ワンタイムトークンチェック
// 「フォームからトークンから送信されていない」または「トークンが一致しない」場合
// ログインフォームにリダイレクト
if (!isset($post['token']) || !Common::isValidToken($post['token'])) {
    $_SESSION['err']['msg'] = Config::MSG_INVALID_PROCESS;
    Logger::errorLog(Config::MSG_INVALID_PROCESS, ['file' => __FILE__, 'line' => __LINE__]);
    header('Location: ./login_form.php', true, 301);
    exit;
}

// バリデーション
$result = Validation::validateLoginFormRequest($_POST);

// 記入情報をサニタイズしてセッションに保存する
$fill = $result['fill'];
if (!empty($fill)) {
    $_SESSION['fill'] = Common::sanitize($fill);
}

// エラーメッセージの処理
/**
 * エラーメッセージがある場合、
 * エラーメッセージをセッションに登録し、
 * 元のフォームへリダイレクト
 */
$err = $result['err'];
if (count($err) > 0) {
    $_SESSION['err'] = $err;
    header('Location: ./login_form.php', true, 301);
    exit;
}


/**
 * エラーメッセージがない場合、
 * ログイン処理を行う
 */
try {
    // DB接続処理
    $base = Base::getPDOInstance();
    $dbh = new Users($base);

    // ログイン処理の前に念のためログイン情報を削除
    unset($_SESSION['login']);
    /** ログイン処理 @param array $post @return bool */
    $ret = $dbh->loginAfterAccountRockConfirmation($post);

    // ログインに成功したかの確認
    if (!$ret) {
        /**
         * ログインに失敗した場合、ログインフォームにリダイレクトして、
         * エラーメッセージを表示させる
         */
        $_SESSION['err']['msg'] = Config::MSG_FAILURE_TO_LOGIN;
        Logger::errorLog(Config::MSG_FAILURE_TO_LOGIN, ['file' => __FILE__, 'line' => __LINE__]);
        header('Location: ./login_form.php', true, 301);
        exit;
    }

    /**
     * 正常終了したときは、記入情報とエラーメッセージを削除して、ログイン画面にリダイレクトする。
     */
    unset($_SESSION['fill']);
    unset($_SESSION['err']);

    // ログインに成功した旨のメッセージをtop.phpにセッションで渡して、リダイレクト
    $_SESSION['success']['msg'] = Config::MSG_LOGIN_SUCCESSFUL;
    header('Location: ../todo/top.php', true, 301);
    exit;
} catch (\PDOException $e) {
    $_SESSION['err']['msg'] = Config::MSG_PDOEXCEPTION_ERROR;
    Logger::errorLog(Config::MSG_PDOEXCEPTION_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
    header('Location: ../error/error.php', true, 301);
    exit;
} catch (\Exception $e) {
    $_SESSION['err']['msg'] = Config::MSG_EXCEPTION_ERROR;
    Logger::errorLog(Config::MSG_EXCEPTION_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
    header('Location: ../error/error.php', true, 301);
    exit;
}
