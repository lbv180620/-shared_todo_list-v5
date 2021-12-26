<?php

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Models\Base;
use App\Config\Config;
use App\Models\Users;
use App\Utils\Common;
use App\Utils\SessionUtil;
use App\Utils\Logger;

// セッション開始
SessionUtil::sessionStart();
// サニタイズ
$post = Common::sanitize($_POST);
$post['finished'] = !empty($post['finished']) ? 1 : null; // "1" -> 1 に変換


// リダイレクト先のURL
$url = "./cancel.php?login_id=" . $post['login_id'];

// ワンタイムトークンチェック
// 「フォームからトークンから送信されていない」または「トークンが一致しない」場合
// 元のフォームにリダイレクト
if (!isset($post['token']) || !Common::isValidToken($post['token'])) {
	$_SESSION['err']['msg'] = Config::MSG_INVALID_PROCESS;
	Logger::errorLog(Config::MSG_INVALID_PROCESS, ['file' => __FILE__, 'line' => __LINE__]);
	header("Location: {$url}", true, 301);
	exit;
}


try {
	// DB接続処理
	$base = Base::getPDOInstance();
	$dbh = new Users($base);

	$login_id = $post['login_id'];
	/** 削除処理 @param int $login_id @return bool */
	$ret = $dbh->deleteUserById($login_id);

	// 削除に成功したかの確認
	if (!$ret) {

		$_SESSION['err']['msg'] = Config::MSG_USER_DELETE_FAILURE;
		Logger::errorLog(Config::MSG_USER_DELETE_FAILURE, ['file' => __FILE__, 'line' => __LINE__]);
		header('Location: ./top.php', true, 301);
		exit;
	}

	/**
	 * 念のため削除
	 */
	unset($_SESSION['fill']);
	unset($_SESSION['err']);

	// 修正に成功した旨のメッセージをTOP画面にセッションで渡して、リダイレクト
	$_SESSION['success']['msg'] = Config::MSG_USER_DELETE_SUCCESSFUL;

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
