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

	/** ワンタイムトークン */
}
