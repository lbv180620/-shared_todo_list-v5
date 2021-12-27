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
				t.staff_id,
				t.client_id,
				u.family_name,
				u.first_name,
				t.item_name,
				t.registration_date,
				t.expiration_date,
				t.finished_date
				FROM todo_items t
				INNER JOIN users u
				ON t.staff_id = u.id
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
	public function getTodoItemBySearch($search)
	{
		if (empty($search)) {
			return [];
		}

		$sql = "SELECT
				t.id,
				t.staff_id,
				t.client_id,
				u.family_name,
				u.first_name,
				t.item_name,
				t.registration_date,
				t.expiration_date,
				t.finished_date
				FROM todo_items t
				INNER JOIN users u
				ON t.staff_id = u.id
				WHERE t.is_deleted = 0
				AND (
					t.item_name LIKE :item_name
					OR u.family_name LIKE :family_name
					OR u.first_name LIKE :first_name
					OR t.registration_date = :registration_date
					OR t.expiration_date = :expiration_date
					OR t.finished_date = :finished_date
				)
				ORDER BY t.expiration_date ASC";

		// bindValue()の第2引数には値を直接入れることができないので
		// 下記のようにして、検索ワードを変数に入れる。
		$likeWord = "%$search%";

		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(':item_name', $likeWord, \PDO::PARAM_STR);
		$stmt->bindValue(':family_name', $likeWord, \PDO::PARAM_STR);
		$stmt->bindValue(':first_name', $likeWord, \PDO::PARAM_STR);
		$stmt->bindValue(':registration_date', $search, \PDO::PARAM_STR);
		$stmt->bindValue(':expiration_date', $search, \PDO::PARAM_STR);
		$stmt->bindValue(':finished_date', $search, \PDO::PARAM_STR);

		$stmt->execute();
		$result = $stmt->fetchAll();

		if ($result === false) {
			return [];
		}

		return $result;
	}

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
				t.staff_id,
				t.client_id,
				u.family_name,
				u.first_name,
				t.item_name,
				t.content,
				t.registration_date,
				t.expiration_date,
				t.finished_date
				FROM todo_items t
				INNER JOIN users u
				ON t.staff_id = u.id
				WHERE t.is_deleted = 0
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

		$staff_id = $data['staff_id'];
		$client_id = $data['client_id'];
		$item_name = $data['item_name'];
		$content = $data['content'];
		$registration_date = $data['registration_date'];
		$expiration_date = $data['expiration_date'];
		$finished_date = $data['finished_date'];

		$sql = "INSERT INTO todo_items (
				staff_id,
				client_id,
				item_name,
				content,
				registration_date,
				expiration_date,
				finished_date
				) VALUES (
				:staff_id,
				:client_id,
				:item_name,
				:content,
				:registration_date,
				:expiration_date,
				:finished_date
				)";

		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(':staff_id', $staff_id, \PDO::PARAM_INT);
		$stmt->bindValue(':client_id', $client_id, \PDO::PARAM_INT);
		$stmt->bindValue(':item_name', $item_name, \PDO::PARAM_STR);
		$stmt->bindValue(':content', $content, \PDO::PARAM_STR);
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
	public function updateTodoItemById(array $data): bool
	{
		$staff_id = $data['staff_id']; // 担当者ID
		$item_id = $data['item_id']; // 作業ID
		$client_id = $data['client_id']; // 作成者ID
		$item_name = $data['item_name']; // 作業項目名
		$content = $data['content']; // 作業内容
		$registration_date = $data['registration_date']; // 登録日
		$expiration_date = $data['expiration_date']; // 期限日
		$finished_date = $data['finished_date']; // 完了日
		$is_deleted = $data['is_deleted'];

		// $item_idが存在しなかったら、falseを返却
		if (!isset($item_id)) {
			return false;
		}

		// $item_idが数字でなかったら、falseを返却する。
		if (!is_numeric($item_id)) {
			return false;
		}

		// $item_idが0以下はありえないので、falseを返却
		if ($item_id <= 0) {
			return false;
		}

		// 現状の仕様では「削除フラグ」をアップデートする必要はないが、今後の仕様追加のために実装しておく。
		$sql = "UPDATE todo_items SET
				staff_id = :staff_id,
				client_id = :client_id,
				item_name = :item_name,
				content = :content,
				registration_date = :registration_date,
				expiration_date = :expiration_date,
				finished_date = :finished_date,
				is_deleted = :is_deleted
				WHERE id = :id";

		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(':staff_id', $staff_id, \PDO::PARAM_INT);
		$stmt->bindValue(':client_id', $client_id, \PDO::PARAM_INT);
		$stmt->bindValue(':item_name', $item_name, \PDO::PARAM_STR);
		$stmt->bindValue(':content', $content, \PDO::PARAM_STR);
		$stmt->bindValue(':registration_date', $registration_date, \PDO::PARAM_STR);
		$stmt->bindValue(':expiration_date', $expiration_date, \PDO::PARAM_STR);
		$stmt->bindValue(':finished_date', $finished_date, \PDO::PARAM_STR);
		$stmt->bindValue(':is_deleted', $is_deleted, \PDO::PARAM_INT);
		$stmt->bindValue(':id', $item_id, \PDO::PARAM_INT);

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
	 * 指定IDの1件の作業項目を完了にします。
	 *
	 * @param int $id 作業項目ID
	 * @param string $date 完了日（NULLの場合は今日の日付）
	 * @return bool 成功した場合:TRUE、失敗した場合:FALSE
	 */
	public function makeTodoItemComplete(int $id, ?string $date = null): bool
	{
		if (!is_numeric($id)) {
			return false;
		}

		if ($id <= 0) {
			return false;
		}

		// $dateがnullだったら、今日の日付を設定する。
		if (is_null($date)) {
			$date = date('Y-m-d');
		}

		$sql = "UPDATE todo_items SET
				finished_date = :finished_date
				WHERE id = :id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, \PDO::PARAM_INT);
		$stmt->bindValue(':finished_date', $date, \PDO::PARAM_STR);

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
	 * 指定IDの1件の作業項目を論理削除します。
	 * todo_itemsテーブルのis_deletedフラグを1に更新する
	 * is_deleted=1の場合、画面に表示されない
	 *
	 * @param int $id 作業項目ID
	 * @return bool 成功した場合:TRUE、失敗した場合:FALSE
	 */
	public function deleteTodoItemById(int $id): bool
	{

		if (!is_numeric($id)) {
			return false;
		}

		if ($id <= 0) {
			return false;
		}

		$sql = "UPDATE todo_items SET
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

	/**
	 * 担当者IDと一致する作業項目を全件取得します。（削除済みの作業項目は含みません）
	 *
	 * @param int $staff_id 担当者ID番号
	 * @return array 作業項目の配列
	 */
	public function getTodoItemAllByStaffId($staff_id)
	{
		// $staff_idが存在しなかったら、falseを返却
		if (!isset($staff_id)) {
			return [];
		}

		// $staff_idが数字でなかったら、falseを返却する。
		if (!is_numeric($staff_id)) {
			return [];
		}

		// $staff_idが0以下はありえないので、falseを返却
		if ($staff_id <= 0) {
			return [];
		}

		// 論理削除されている作業項目は表示対象外
		// 期限日の順番に並べる
		$sql = "SELECT
				t.id,
				t.staff_id,
				t.client_id,
				u.family_name,
				u.first_name,
				t.item_name,
				t.registration_date,
				t.expiration_date,
				t.finished_date
				FROM todo_items t
				INNER JOIN users u
				ON t.staff_id = u.id
				WHERE t.is_deleted = 0
				AND t.staff_id = :staff_id
				ORDER BY t.expiration_date ASC";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':staff_id', $staff_id, \PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}
}
