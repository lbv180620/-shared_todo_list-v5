<?php

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Models\Users;
use App\Models\Base;
use App\Config\Config;
use App\Utils\Common;
use App\Utils\SessionUtil;

// セッション開始
SessionUtil::sessionStart();

// エラーメッセージ
$err = [];

// バリデーション
if (!empty($_POST)) {
	// user_nameのバリデーション
	if (!$user_name = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
		$err['user_name'] = Config::MSG_USER_NAME_ERROR;
		$_POST['user_name'] = "";
	}
	/**
	 * 文字制限
	 * varchar(50)
	 */
	if (!empty($user_name) && mb_strlen($user_name) > 50) {
		$err['user_name'] = Config::MSG_USER_NAME_STRLEN_ERROR;
		$_POST['user_name'] = "";
	}

	// passwordのバリデーション
	if (!$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
		$err['password'] = Config::MSG_PASSWORD_ERROR;
	}

	// 正規表現
	/**
	 *"/\A[a-z\d]{8,100}+\z/i"
	 *英小文字数字で8文字以上255文字以下の範囲で1回続く(大文字小文字は区別しない)パスワード
	 */
	if (!empty($password) && !preg_match("/\A[a-z\d]{8,255}+\z/i", $password)) {
		$err['password'] = Config::MSG_PASSWORD_REGEX_ERROR;
	}
}

// 記入情報をサニタイズして保存
$_SESSION['fill'] = Common::sanitize($_POST);

// エラーメッセージの処理
/**
 * エラーメッセージがある場合、
 * エラーメッセージをセッションに登録し、
 * 元のフォームへリダイレクト
 */
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
	/** ログイン処理 @return bool */
	$ret = $dbh->login();

	// ログインに成功したかの確認
	if (!$ret) {
		/**
		 * ログインに失敗した場合、ログインフォームにリダイレクトして、
		 * エラーメッセージを表示させる
		 */
		$_SESSION['err']['msg'] = Config::MSG_FAILURE_TO_LOGIN;
		header('Location: ./login_form.php', true, 301);
		exit;
	}

	/**
	 * 正常終了したときは、記入情報とエラーメッセージを削除して、ログイン画面にリダイレクトする。
	 */
	unset($_SESSION['fill']);
	unset($_SESSION['err']);

	// ログインに成功した旨のメッセージをマイページにセッションで渡して、リダイレクト
	$_SESSION['success']['msg'] = Config::MSG_LOGIN_SUCCESSFUL;
	header('Location: ../todo/mypage.php', true, 301);
	exit;
} catch (\PDOException $e) {
	$_SESSION['err']['msg'] = Config::MSG_PDOEXCEPTION_ERROR;
	header('Location: ../error/error.php', true, 301);
	exit;
} catch (\Exception $e) {
	$_SESSION['err']['msg'] = Config::MSG_EXCEPTION_ERROR;
	// $_SESSION['err']['msg'] = $e->getMessage();
	header('Location: ../error/error.php', true, 301);
	exit;
}
