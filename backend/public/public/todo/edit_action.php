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
$url = "./edit.php?item_id=" . $post['id'];

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
 * 作業登録情報をDBに登録する
 */
