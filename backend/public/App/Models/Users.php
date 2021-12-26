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

	/**
	 * ユーザを新規登録する
	 * @method addUser
	 * @param array $post
	 * @return bool
	 */
	public function addUser(array $post): bool
	{
		$user_name = $post['user_name'];
		$family_name = $post['family_name'];
		$first_name = $post['first_name'];
		$email = $post['email'];
		$password = $post['password'];

		// 同じメールアドレスのユーザーがいないか調べる
		if (!empty($this->findUserByEmail($email))) {
			// すでに同じメールアドレスをもつユーザがいる場合、falseを返す
			$result = false;
			return $result;
		}

		// パスワードをハッシュ化する
		$password = password_hash($password, PASSWORD_DEFAULT);

		// ユーザ登録情報をDBにインサートする
		$sql = "INSERT INTO users (user_name, email, password, family_name, first_name)";
		$sql .= "VALUES";
		$sql .= "(:user_name, :email, :password, :family_name, :first_name)";

		// データ変更ありなので、トランザクション処理
		$this->pdo->beginTransaction();
		try {
			$stmt = $this->pdo->prepare($sql);

			$stmt->bindValue(':user_name', $user_name, \PDO::PARAM_STR);
			$stmt->bindValue(':email', $email, \PDO::PARAM_STR);
			$stmt->bindValue(':password', $password, \PDO::PARAM_STR);
			$stmt->bindValue(':family_name', $family_name, \PDO::PARAM_STR);
			$stmt->bindValue(':first_name', $first_name, \PDO::PARAM_STR);

			$stmt->execute();
			$result = $this->pdo->commit();

			return $result;
		} catch (\PDOException $e) {
			$this->pdo->rollBack();
			return $result;
		}
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
	 * メールアドレスとパスワードが一致するユーザーを取得する
	 *
	 * @param string $email
	 * @param string $password
	 * @return array $user ユーザーの連想配列
	 */
	private function getUser(?string $email, ?string $password)
	{
		// 同一のメールアドレスのユーザーを探す
		$user = $this->findUserByEmail($email);
		// 同一のメールアドレスのユーザーが無かったら、空の配列を返す
		if (empty($user)) {
			return [];
		}

		// パスワードの照合
		if (password_verify($password, $user['password'])) {
			// 照合できたら、ユーザ情報を返す
			return $user;
		}

		// 照合できなかったら、空の配列を返す
		return [];
	}

	/**
	 * ログイン処理
	 * is_deleted=1で論理削除されたユーザはログインできない
	 *
	 * @param arrsy $post
	 * @return bool $result
	 */
	public function login(?array $post)
	{
		$email = $post['email'];
		$password = $post['password'];
		// メールアドレスとパスワードが一致するユーザーを取得する
		$user = $this->getUser($email, $password);
		// 論理削除されているユーザはログインできない
		if (!empty($user) && $user['is_deleted'] === 0) {
			// セッションにユーザ情報を登録
			$_SESSION['login'] = $user;
			return true;
		}

		// メールアドレスとパスワードが一致するユーザを取得できなかった場合、空の配列を返す
		return false;
	}

	/**
	 * ログアウト処理
	 *
	 * @return bool
	 */
	public static function logout(): bool
	{
		// ログインユーザー情報を削除して、ログアウト処理とする
		unset($_SESSION['login']);

		// 念のためにセッションに保存した他の情報も削除する
		unset($_SESSION['fill']);
		unset($_SESSION['err']);
		unset($_SESSION['success']);

		// さらに念のために全消し
		$_SESSION = array();
		return session_destroy();
	}

	/**
	 * 論理削除されていないすべてのユーザ情報を全件取得
	 *
	 * @return array ユーザのレコードの配列
	 */
	public function getUserAll()
	{
		// $sql = "SELECT id, user_name, password, family_name, first_name, is_admin
		// 		FROM users
		// 		WHERE is_deleted=0
		// 		ORDER BY id";
		$sql = "SELECT id, user_name, password, family_name, first_name, is_admin
				FROM users
				ORDER BY id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/**
	 * 論理削除されていない指定IDのユーザが存在するかどうか調べる
	 *
	 * @param int $id ユーザID
	 * @return bool ユーザが存在するとき：true、ユーザが存在しないとき：false
	 */
	public function isExistsUser($id)
	{
		// $idが数字でなかったら、falseを返却
		if (!is_numeric($id)) {
			return false;
		}

		// $idが0以下はありえないので、falseを返却
		if ($id <= 0) {
			return false;
		}

		// $sql = "SELECT COUNT(id) AS num FROM users WHERE is_deleted=0";
		$sql = "SELECT COUNT(id) AS num FROM users";

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$ret = $stmt->fetch();

		// レコードの数が0だったらfalseを返却
		if ($ret['num'] == 0) {
			return false;
		}

		return true;
	}

	/**
	 * 指定したIDに一致したユーザのレコードを1件取得
	 *
	 * todo_itemsテーブルのclient_idから、 作成者名を取得する
	 * staff_idから担当者のレコードを取得
	 *
	 * @param int $id 作成者ID
	 * @return array 作成者のレコード
	 */
	public function getUserById(int $id)
	{
		if (!is_numeric($id)) {
			return false;
		}

		if ($id <= 0) {
			return false;
		}

		// $sql = "SELECT * FROM users
		// 		WHERE id = :id
		// 		AND is_deleted = 0";
		$sql = "SELECT * FROM users
				WHERE id = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	/**
	 * 指定IDの1件のユーザを論理削除します。
	 * usersテーブルのis_deletedフラグを1に更新する
	 *
	 * @param int $id ユーザID
	 * @return bool 成功した場合:TRUE、失敗した場合:FALSE
	 */
	public function deleteUserById(int $id): bool
	{

		if (!is_numeric($id)) {
			return false;
		}

		if ($id <= 0) {
			return false;
		}

		$sql = "UPDATE users SET
				is_deleted = 1
				WHERE id = :id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, \PDO::PARAM_INT);

		$this->pdo->beginTransaction();
		try {
			$stmt->execute();
			return $this->pdo->commit();
		} catch (\PDOException $e) {
			$this->pdo->rollBack();
			return false;
		}
	}
}
