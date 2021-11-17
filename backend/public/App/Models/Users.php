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

	/**
	 * ユーザを新規登録する
	 * @param string $user_name
	 * @param string $email
	 * @param string $family_name
	 * @param string $first_name
	 * @param string $password
	 * @return bool
	 */
	public static function addUser(string $user_name, string $email, string $family_name, string $first_name, string $password): bool
	{
		$result = false;

		// 同じメールアドレスのユーザーがいないか調べる
		if (!empty(Users::findUserByEmail($email))) {
			// すでに同じメールアドレスをもつユーザがいる場合、falseを返す
			$result = false;
		}

		// パスワードをハッシュ化する

		// ユーザ登録情報をDBにインサートする

		return $result;
	}

	/**
	 * 同一のメールアドレスのユーザーを探す
	 * @param string $email
	 * @return array ユーザーの連想配列
	 */
	public static function findUserByEmail(string $email): array
	{
	}
}
