<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * 共通関数クラスです。
 * セキュリティ対策用の関数など定義。
 */
class Common
{
	/**
	 * XSS対策：エスケープ処理
	 * @static@method h
	 * @param string $str 対象の文字列
	 * @return string 処理された文字列
	 */
	public static function h(string $str): string
	{
		return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
	}

	/**
	 * サニタイズ：POSTまたはGETで送信されて来た連想配列の要素の値をサニタイズする(1次配列のみ)
	 * @static@method sanitize
	 * @param array $post POSTまたはGETで送信されて来た連想配列(1次配列)
	 * @return array $post エスケープ処理が完了した連想配列
	 */
	public static function sanitize(array $post): array
	{
		foreach ($post as $k => $v) {
			$post[$k] = htmlspecialchars($v, ENT_QUOTES, "UTF-8");
		}
		return $post;
	}
}
