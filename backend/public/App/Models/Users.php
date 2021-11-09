<?php

declare(strict_types=1);

namespace App\Models;

/**
 * ユーザーテーブルクラス
 * ユーザテーブルのCUID処理
 */
class Users
{
	/** @var \PDO $pdo PDOクラスインスタンス*/
	private $pdo;

	/**
	 * コンストラクタ
	 * @param \PDO $pdo PDOクラスインスタンス
	 */
	public function __construct(\PDO $pdo)
	{
		// 引数に指定されたPDOクラスのインスタンスをプロパティに代入
		// クラスのインスタンスは別の変数に代入されても同じものとして扱われる(複製されるわけではない)
		$this->pdo = $pdo;
	}

	public function getPDO()
	{
		return $this->pdo;
	}
}
