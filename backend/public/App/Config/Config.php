<?php

declare(strict_types=1);

namespace App\Config;

class Config
{
	/** DB関連 */

	/** @var array ドライバーオプション */
	// 「PDO::ERRMODE_EXCEPTION」を指定すると、エラー発生時に例外がスローされる
	const DRIVER_OPTS = [
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::ATTR_EMULATE_PREPARES => false,
		\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
	];

	/** メッセージ関連 */
	const MSG_EXCEPTION_ERROR = "申し訳ございません。エラーが発生しました。";
	const MSG_PDOEXCEPTION_ERROR = "データベース接続に失敗しました。";

	// register.php login.php
	const MSG_POST_SENDING_FAILURE_ERROR = "送信に失敗しました。";

	const MSG_USER_NAME_ERROR = "ユーザー名を記入してください。";
	const MSG_EMAIL_ERROR = "メールアドレスを記入してください。";
	const MSG_FAMILY_NAME_ERROR = "お名前(姓)を記入してください。";
	const MSG_FIRST_NAME_ERROR = "お名前(名)を記入してください。";
	const MSG_PASSWORD_ERROR = "パスワードを記入してください。";
	const MSG_PASSWORD_CONFIRM_ERROR = "確認用パスワードを記入してください。";

	const MSG_USER_NAME_STRLEN_ERROR = "ユーザー名は50文字以下にしてください。";
	const MSG_EMAIL_INCORRECT_ERROR = "メールアドレスの形式が正しくありません。";
	const MSG_EMAIL_STRLEN_ERROR = "メールアドレスは255文字以下にしてください。";
	const MSG_FAMILY_NAME_STRLEN_ERROR = "お名前(姓)は50文字以下にして下さい。";
	const MSG_FIRST_NAME_STRLEN_ERROR = "お名前(名)は50文字以下にして下さい。";
	const MSG_PASSWORD_REGEX_ERROR = "パスワードは英数字8文字以上255文字以下にして記入してください。";
	const MSG_PASSWORD_CONFIRM_MISMATCH_ERROR = "確認用パスワードと異なっています。";

	const MSG_USER_DUPLICATE = "既に同じメールアドレスが登録されています。";

	const MSG_NEW_REGISTRATIONW_REGISTRATION_SUCCESSFUL = "新規登録しました。ログインしてください。";

	const MSG_FAILURE_TO_LOGIN = "ログインに失敗しました。";
	const MSG_LOGIN_SUCCESSFUL = "ログインに成功しました。";

	const MSG_LOGOUT_FAILURE = 'ログアウトに失敗しました。';

	/** ワンタイムトークン */
	/** @var int openssl_random_pseudo_bytes()で使用する文字列の長さ */
	const RAMDOM_PSEUDO_STRING_LENGTH = 32;

	/** @var string ワンタイムトークンが一致しないとき */
	const MSG_INVALID_PROCESS = '不正な処理が行われました。';
}
