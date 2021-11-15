<?php

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Models\Users;
use App\Config\Config;
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
	}
	/**
	 * 文字数制限
	 * varchar(50)
	 */
	if (!empty($user_name) && mb_strlen($user_name) > 50) {
		$err['user_name'] = Config::MSG_USER_NAME_STRLEN_ERROR;
	}

	// emailのバリデーション
	if (!$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
		$err['email'] = Config::MSG_EMAIL_ERROR;
	}
	// メールアドレスの形式チェック
	if (!empty($email) && !$email = filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$err['email'] = Config::MSG_EMAIL_INCORRECT_ERROR;
	}
	/**
	 * 文字数制限
	 * varchar(255)
	 */
	if (!empty($email) && mb_strlen($email) > 255) {
		$err['email'] = Config::MSG_EMAIL_STRLEN_ERROR;
	}

	// family_nameのバリデーション
	if (!$family_name = filter_input(INPUT_POST, 'family_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
		$err['family_name'] = Config::MSG_FAMILY_NAME_ERROR;
	}
	/**
	 * 文字数制限
	 * varchar(50)
	 */
	if (!empty($family_name) && mb_strlen($family_name) > 50) {
		$err['family_name'] = Config::MSG_NAME_STRLEN_ERROR;
	}

	// first_nameのバリデーション
	if (!$first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
		$err['first_name'] = Config::MSG_FIRST_NAME_ERROR;
	}
	/**
	 * 文字数制限
	 * varchar(50)
	 */
	if (!empty($first_name) && mb_strlen($first_name) > 50) {
		$err['first_name'] = Config::MSG_NAME_STRLEN_ERROR;
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
}

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
// if (count($err) === 0) {
// 	// ユーザ情報を登録する処理
// 	Users::addUser();

// 	// 登録に成功したかの確認
// }

echo $user_name . PHP_EOL;
echo $email . PHP_EOL;
echo $family_name . PHP_EOL;
echo $first_name . PHP_EOL;
echo $password . PHP_EOL;
