<?php

/** guest */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

/** DB操作関連で使用 */

use App\Models\Base;
use App\Models\Users;

/** エラーメッセージ関連で使用 */

use App\Config\Config;

/** セッション処理・サニタイズ処理で使用 */

use App\Utils\SessionUtil;
use App\Utils\Common;
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
	header('Location: ../login/login_form.php', true, 301);
	exit;
}

// バリデーション
$result = Validation::validateFormRequesut($post);

// 記入情報をサニタイズしてセッションに保存する
$fill = $result['fill'];
if (count($fill)) {
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
	header('Location: ./signup_form.php', true, 301);
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
		Logger::errorLog(Config::MSG_USER_DUPLICATE, ['file' => __FILE__, 'line' => __LINE__]);
		header('Location: ./signup_form.php', true, 301);
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
	$_SESSION['success']['msg'] = Config::MSG_NEW_REGISTRATIONW_REGISTRATION_SUCCESSFUL;

	header('Location: ../login/login_form.php', true, 301);
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
