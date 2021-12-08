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
	 * @method addUser
	 * @param string $user_name
	 * @param string $email
	 * @param string $password
	 * @param string $family_name
	 * @param string $first_name
	 * @return bool
	 */
	public function addUser(string $user_name, string $email, string $password, string $family_name, string $first_name): bool
	{
		// 同じメールアドレスのユーザーがいないか調べる
		if (!empty($this->findUserByEmail($email))) {
			// すでに同じメールアドレスをもつユーザがいる場合、falseを返す
			$result = false;
		}

		// パスワードをハッシュ化する
		$password = password_hash($password, PASSWORD_DEFAULT);

		// ユーザ登録情報をDBにインサートする
		$sql = "INSERT INTO users (user_name, email, password, family_name, first_name)";
		$sql .= "VALUES";
		$sql .= "(:user_name, :email, :password, :family_name, :first_name)";

		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(':user_name', $user_name, \PDO::PARAM_STR);
		$stmt->bindValue(':email', $email, \PDO::PARAM_STR);
		$stmt->bindValue(':password', $password, \PDO::PARAM_STR);
		$stmt->bindValue(':family_name', $family_name, \PDO::PARAM_STR);
		$stmt->bindValue(':first_name', $first_name, \PDO::PARAM_STR);

		$result = $stmt->execute();

		return $result;
	}

	/**
	 * 同一のメールアドレスのユーザーを探す
	 * @method findUserByEmail
	 * @param string $email
	 * @return array ユーザーの連想配列
	 */
	private function findUserByEmail(string $email): array
	{
		// usersテーブルから同一のメールアドレスのユーザーを取得するクエリ
		$sql = "SELECT * FROM users WHERE email = :email";

		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(':email', $email, \PDO::PARAM_STR);

		$stmt->execute();

		// 該当するユーザーが1名でもいたらダメなので、fetchAllではなくfetchで十分
		$rec = $stmt->fetch();

		// fetchに失敗した場合、戻り値がfalseなので、空の配列を返すように修正
		if (empty($rec)) {
			return [];
		}

		return $rec;
	}

	/**
	 * ログイン処理
	 *
	 * @param
	 * @return bool $result
	 */
	public function login()
	{
	}
}
