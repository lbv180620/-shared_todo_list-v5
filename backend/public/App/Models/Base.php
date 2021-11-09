<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Env;
use App\Config\Config;

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

/**
 * DB操作基底クラス
 */
class Base
{
	/** @var object PDOクラスインスタンス */
	private static $pdo;

	/**
	 * PDOクラスのインスタンスを生成して返却する
	 * @param void
	 * @return \PDO PDOクラスのインスタンス
	 */
	public static function getPDOInstance(): \PDO
	{
		$dsn = "mysql:dbname=" . Env::get('DB_NAME') . ";host=" . Env::get('DB_HOST') . ";charset=utf8mb4";

		// インスタンスが生成されていなかったら、新しく生成する
		// すでに生成済みであれば、生成済みのインスタンスを返す
		if (!isset(self::$pdo)) {
			self::$pdo = new \PDO($dsn, Env::get('DB_USER'), Env::get('DB_PASS'), Config::DRIVER_OPTS);
		}

		return self::$pdo;
	}

	// トランザクション処理
}
