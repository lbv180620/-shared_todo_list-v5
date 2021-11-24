<?php

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

/** DB操作関連で使用 */

use App\Models\Base;
use App\Models\Users;

/** エラーメッセージ関連で使用 */

use App\Config\Config;

/** セッション処理・サニタイズ処理で使用 */

use App\Utils\SessionUtil;
use App\Utils\Common;

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
	 * 文字数制限
	 * varchar(50)
	 */
	if (!empty($user_name) && mb_strlen($user_name) > 50) {
		$err['user_name'] = Config::MSG_USER_NAME_STRLEN_ERROR;
		$_POST['user_name'] = "";
	}

	// emailのバリデーション
	if (!$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
		$err['email'] = Config::MSG_EMAIL_ERROR;
		$_POST['email'] = "";
	}
	// メールアドレスの形式チェック
	if (!empty($email) && !$email = filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$err['email'] = Config::MSG_EMAIL_INCORRECT_ERROR;
		$_POST['email'] = "";
	}
	/**
	 * 文字数制限
	 * varchar(255)
	 */
	if (!empty($email) && mb_strlen($email) > 255) {
		$err['email'] = Config::MSG_EMAIL_STRLEN_ERROR;
		$_POST['email'] = "";
	}

	// family_nameのバリデーション
	if (!$family_name = filter_input(INPUT_POST, 'family_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
		$err['family_name'] = Config::MSG_FAMILY_NAME_ERROR;
		$_POST['family_name'] = "";
	}
	/**
	 * 文字数制限
	 * varchar(50)
	 */
	if (!empty($family_name) && mb_strlen($family_name) > 50) {
		$err['family_name'] = Config::MSG_FAMILY_NAME_STRLEN_ERROR;
		$_POST['family_name'] = "";
	}

	// first_nameのバリデーション
	if (!$first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
		$err['first_name'] = Config::MSG_FIRST_NAME_ERROR;
		$_POST['first_name'] = "";
	}
	/**
	 * 文字数制限
	 * varchar(50)
	 */
	if (!empty($first_name) && mb_strlen($first_name) > 50) {
		$err['first_name'] = Config::MSG_FIRST_NAME_STRLEN_ERROR;
		$_POST['first_name'] = "";
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

	// password_confirmのバリデーション
	if (!$password_confirm = filter_input(INPUT_POST, 'password_confirm', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
		$err['password_confirm'] = Config::MSG_PASSWORD_CONFIRM_ERROR;
	}
	if (!empty($password) && !empty($password_confirm) && $password !== $password_confirm) {
		$err['password_confirm'] = Config::MSG_PASSWORD_CONFIRM_MISMATCH_ERROR;
	}
} else {
	$err['msg'] = Config::MSG_POST_SENDING_FAILURE_ERROR;
}

// 新規登録情報をサニタイズしてセッションに保存する
$_SESSION['register'] = Common::sanitize($_POST);

// エラーメッセージの処理
/**
 * エラーメッセージがある場合、
 * エラーメッセージをセッションに登録し、
 * 元のフォームへリダイレクト
 */
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

	/** ユーザ登録処理 @return bool */
	$ret = $dbh->addUser($user_name, $email, $password, $family_name, $first_name);

	// 登録に成功したかの確認
	if (!$ret) {
		/**
		 * 同一のメールアドレスのユーザーがすでにいた場合、
		 * エラーメッセージをセッションに登録して、新規登録画面にリダイレクト
		 */
		$_SESSION['err']['msg'] = Config::MSG_USER_DUPLICATE;
		header('Location: ./signup_form.php', true, 301);
		exit;
	}

	/**
	 * 正常終了したときは、登録情報とエラーメッセージを削除して、ログイン画面にリダイレクトする。
	 */
	unset($_SESSION['register']);
	unset($_SESSION['err']);
	// ログイン状態で新規登録に成功した場合、今のログイン情報は削除するようにする。
	// unset($_SESSION['login']);

	header('Location: ../login/login_form.php', true, 301);
	exit;
} catch (\PDOException $e) {
	$_SESSION['err']['msg'] = Config::MSG_PDOEXCEPTION_ERROR;
	header('Location: ../error/error.php', true, 301);
	exit;
} catch (\Exception $e) {
	$_SESSION['err']['msg'] = Config::MSG_EXCEPTION_ERROR;
	header('Location: ../error/error.php', true, 301);
	exit;
}
