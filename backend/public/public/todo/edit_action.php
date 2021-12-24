<?php

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Models\Base;
use App\Config\Config;
use App\Models\TodoItems;
use App\Utils\Common;
use App\Utils\SessionUtil;
use App\Utils\Validation;
use App\Utils\Logger;

// セッション開始
SessionUtil::sessionStart();
// サニタイズ
$post = Common::sanitize($_POST);
$post['finished'] = !empty($post['finished']) ? 1 : null; // "1" -> 1 に変換


// リダイレクト先のURL
$url = "./edit.php?item_id=" . $post['item_id'];

// ワンタイムトークンチェック
// 「フォームからトークンから送信されていない」または「トークンが一致しない」場合
// 元のフォームにリダイレクト
if (!isset($post['token']) || !Common::isValidToken($post['token'])) {
	$_SESSION['err']['msg'] = Config::MSG_INVALID_PROCESS;
	Logger::errorLog(Config::MSG_INVALID_PROCESS, ['file' => __FILE__, 'line' => __LINE__]);
	header("Location: {$url}", true, 301);
	exit;
}

// バリデーション
$result = Validation::validateFormRequesut($post);


// 記入情報をサニタイズしてセッションに保存する
$fill = $result['fill'];
$fill['finished'] = !empty($fill['finished']) ? "1" : null; // 1 -> "1" に変換

if (!empty($fill)) {
	$_SESSION['fill'] = Common::sanitize($fill);
}

// エラーメッセージの処理
/**
 * エラーメッセージがある場合、
 * エラーメッセージをセッションに登録し、
 * 元のフォームへリダイレクト
 */
$err = $result['err'];
if (count($err) > 0) {
	$_SESSION['err'] = $err;
	header("Location: {$url}", true, 301);
	exit;
}

/**
 * エラーメッセージがない場合、
 * 修正情報をDBに登録する
 */

try {
	// DB接続処理
	$base = Base::getPDOInstance();
	$dbh = new TodoItems($base);

	// 更新する内容を連想配列に
	$data = [
		'user_id' => $post['user_id'],
		'item_id' => $post['item_id'],
		'client_id' => $post['client_id'],
		'item_name' => $post['item_name'],
		'registration_date' => date('Y-m-d'),
		'expiration_date' => $post['expiration_date'],
		'finished_date' => isset($post['finished']) && $post['finished'] === 1 ? date('Y-m-d') : null,
		'is_deleted' => 0,
	];

	/** 更新処理 @param array $data @return bool */
	$ret = $dbh->updateTodoItemById($data);

	// 更新に成功したかの確認
	if (!$ret) {

		$_SESSION['err']['msg'] = Config::MSG_TASK_UPDATE_FAILURE;
		Logger::errorLog(Config::MSG_TASK_UPDATE_FAILURE, ['file' => __FILE__, 'line' => __LINE__]);
		header('Location: ./top.php', true, 301);
		exit;
	}

	/**
	 * 正常終了したときは、記入情報とエラーメッセージを削除して、ログイン画面にリダイレクトする。
	 */
	unset($_SESSION['fill']);
	unset($_SESSION['err']);

	// 修正に成功した旨のメッセージをTOP画面にセッションで渡して、リダイレクト
	$_SESSION['success']['msg'] = Config::MSG_TASK_UPDATE_SUCCESSFUL;

	header('Location: ./top.php', true, 301);
	exit;
} catch (\PDOException $e) {
	$_SESSION['err']['msg'] = Config::MSG_PDOEXCEPTION_ERROR;
	// $_SESSION['err']['msg'] = $e->getMessage();
	Logger::errorLog(Config::MSG_PDOEXCEPTION_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
	header('Location: ../error/error.php', true, 301);
	exit;
} catch (\Exception $e) {
	$_SESSION['err']['msg'] = Config::MSG_EXCEPTION_ERROR;
	Logger::errorLog(Config::MSG_EXCEPTION_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
	header('Location: ../error/error.php', true, 301);
	exit;
}
