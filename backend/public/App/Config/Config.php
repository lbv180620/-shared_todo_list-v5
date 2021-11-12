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
	const MSG_PDOEXCEPTION_ERROR = "データベース接続に失敗しました。";

	const MSG_USER_NAME_ERROR = "ユーザー名を記入してください。";
	const MSG_EMAIL_ERROR = "メールアドレスを記入してください。";
	const MSG_FAMILY_NAME_ERROR = "お名前(姓)を記入してください。";
	const MSG_FIRST_NAME_ERROR = "お名前(名)を記入してください。";
	const MSG_PASSWORD_ERROR = "パスワードは英数字8文字以上100文字以下にして記入してください。";
	const MSG_PASSWORD_CONFIRM_ERROR = "確認用パスワードと異なっています。";

	/** ワンタイムトークン */
}
