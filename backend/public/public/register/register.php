<?php

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Models\Users;
use App\Config\Config;
use App\Utils\SessionUtil;

SessionUtil::sessionStart();

// エラーメッセージ
$err = [];

// バリデーション
if (!$user_name = filter_input(INPUT_POST, 'user_name')) {
	$err['user_name'] = Config::MSG_USER_NAME_ERROR;
}

if (!$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
	$err['email'] = Config::MSG_EMAIL_ERROR;
}

if (!$family_name = filter_input(INPUT_POST, 'family_name')) {
	$err['family_name'] = Config::MSG_FAMILY_NAME_ERROR;
}

if (!$first_name = filter_input(INPUT_POST, 'first_name')) {
	$err['first_name'] = Config::MSG_FIRST_NAME_ERROR;
}

$password = filter_input(INPUT_POST, 'user_name');
// 正規表現
/**
 *"/\A[a-z\d]{8,100}+\z/i"
 *英小文字数字で8文字以上100文字以下の範囲で1回続く(大文字小文字は区別しない)パスワード
 */
if (!preg_match("/\A[a-z\d]{8,100}+\z/i", $password)) {
	$err['password'] = Config::MSG_PASSWORD_ERROR;
}

$password_confirm = filter_input(INPUT_POST, 'password_confirm');
if ($password !== $password_confirm) {
	$err['password_confirm'] = Config::MSG_PASSWORD_CONFIRM_ERROR;
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
