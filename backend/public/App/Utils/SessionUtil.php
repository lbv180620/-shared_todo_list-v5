<?php

declare(strict_types=1);

namespace App\Utils;

class SessionUtil
{
	/**
	 * セッションをスタートし、セッションIDを更新する
	 * @param void
	 * @return void
	 */
	public static function sessionStart(): void
	{
		// php.iniのセッション項目の変更

		session_start();
		session_regenerate_id(true);

		// 最終表示時間をセッションに登録
	}

	/**
	 * セッション有効期限を過ぎればセッション削除
	 * 処理系のファイルには不要、画面系のファイルに要
	 * 非同期処理？
	 */
}
