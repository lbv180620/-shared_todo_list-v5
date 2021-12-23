<?php

declare(strict_types=1);

namespace App\Models;

/**
 * todo_itemsテーブルクラス
 * todo_itemsテーブルのCUID処理
 */
class TodoItems
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
	 * 作業項目を全件取得します。（削除済みの作業項目は含みません）
	 *
	 * @return array 作業項目の配列
	 */
	public function getTodoItemAll()
	{
		// 論理削除されている作業項目は表示対象外
		// 期限日の順番に並べる
		$sql = "SELECT
				t.id,
				t.user_id,
				t.auth_id,
				u.family_name,
				u.first_name,
				t.item_name,
				t.registration_date,
				t.expiration_date,
				t.finished_date
				FROM todo_items t
				INNER JOIN users u
				ON t.user_id = u.id
				WHERE t.is_deleted = 0
				ORDER BY t.expiration_date ASC";

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/**
	 * 作業項目を検索条件で抽出して取得します。（削除済みの作業項目は含みません）
	 *
	 * @param mixed $search 検索キーワード
	 * @return array 作業項目の配列
	 */

	/**
	 * 指定IDの作業項目を1件取得します。（削除済みの作業項目は含みません）
	 * @param int $id 作業項目のID番号
	 * @return array 作業項目の配列
	 */
	public function getTodoItemById(int $id)
	{

		if (!is_numeric($id)) {
			return false;
		}

		if ($id <= 0) {
			return false;
		}

		$sql = "SELECT
				t.id,
				t.user_id,
				t.auth_id,
				u.family_name,
				u.first_name,
				t.item_name,
				t.registration_date,
				t.expiration_date,
				t.finished_date
				FROM todo_items t
				INNER JOIN users u
				ON t.user_id = u.id
				WHERE u.is_deleted = 0
				AND t.id = :id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	/**
	 * 作業項目を1件登録します。
	 *
	 * @param array $data 作業項目の連想配列
	 * @return bool 成功した場合:TRUE、失敗した場合:FALSE
	 */
	public function registerTodoItem(array $data): bool
	{

		$user_id = $data['user_id'];
		$auth_id = $data['auth_id'];
		$item_name = $data['item_name'];
		$registration_date = $data['registration_date'];
		$expiration_date = $data['expiration_date'];
		$finished_date = $data['finished_date'];

		$sql = "INSERT INTO todo_items (
				user_id,
				auth_id,
				item_name,
				registration_date,
				expiration_date,
				finished_date
				) VALUES (
				:user_id,
				:auth_id,
				:item_name,
				:registration_date,
				:expiration_date,
				:finished_date
				)";

		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
		$stmt->bindValue(':auth_id', $auth_id, \PDO::PARAM_INT);
		$stmt->bindValue(':item_name', $item_name, \PDO::PARAM_STR);
		$stmt->bindValue(':registration_date', $registration_date, \PDO::PARAM_STR);
		$stmt->bindValue(':expiration_date', $expiration_date, \PDO::PARAM_STR);
		$stmt->bindValue(':finished_date', $finished_date, \PDO::PARAM_STR);

		$this->pdo->beginTransaction();
		try {
			$stmt->execute();
			return $this->pdo->commit();
		} catch (\PDOException $e) {
			$this->pdo->rollBack();
			return false;
		}
	}

	/**
	 * 指定IDの1件の作業項目を更新ます。
	 *
	 * @param array $data 更新する作業項目の連想配列
	 * @return bool 成功した場合:TRUE、失敗した場合:FALSE
	 */


	/**
	 * 指定IDの1件の作業項目を完了にします。
	 *
	 * @param int $id 作業項目ID
	 * @param string $date 完了日（NULLの場合は今日の日付）
	 * @return bool 成功した場合:TRUE、失敗した場合:FALSE
	 */

	/**
	 * 指定IDの1件の作業項目を論理削除します。
	 *
	 * @param int $id 作業項目ID
	 * @return bool 成功した場合:TRUE、失敗した場合:FALSE
	 */
}
