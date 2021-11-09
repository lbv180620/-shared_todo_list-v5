<?php

declare(strict_types=1);

namespace App\Models;

use Dotenv\Dotenv;

/**
 * .envファイルに書かれている環境変数を取得するためのクラス
 */
class Env
{
	private static $dotenv;

	/**
	 * 環境変数の値を取得する
	 * @param string $key 取得した環境変数名
	 * @return string 環境変数の値
	 */
	public static function get(string $key): string
	{
		// $dotenvがDotenvのインスタンスじゃない場合、新たにDotenvインスタンスを生成し代入
		if ((self::$dotenv instanceof Dotenv) === false) {
			self::$dotenv = Dotenv::createImmutable(dirname(__FILE__, 5));
			self::$dotenv->load();
		}
		return array_key_exists($key, $_ENV) ? $_ENV[$key] : null;
	}
}
