<?php

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Models\Users;
use App\Models\Base;
use App\Config\Config;
use App\Utils\Common;
use App\Utils\SessionUtil;
use App\Utils\Validation;
use App\Utils\Logger;

// セッション開始
SessionUtil::sessionStart();
// サニタイズ
$post = Common::sanitize($_POST);

// リダイレクト先URL
// $usrl = "./entry.php?item_id=" . $post['id'];
// $usrl = "./entry.php";

// ワンタイムトークンチェック
// 「フォームからトークンから送信されていない」または「トークンが一致しない」場合
// ログインフォームにリダイレクト
if (!isset($post['token']) || !Common::isValidToken($post['token'])) {
	$_SESSION['err']['msg'] = Config::MSG_INVALID_PROCESS;
	Logger::errorLog(Config::MSG_INVALID_PROCESS, ['file' => __FILE__, 'line' => __LINE__]);
	header("Location: ./entry.php", true, 301);
	exit;
}

// バリデーション
$result = Validation::validateFormRequesut($post);


// POSTされてきた値をセッションに登録
$_SESSION['post'] = $post;
$_SESSION['post']['finished'] = !empty($post['finished']) ? 1 : null; // $_SESSION['post']['finished'] "1" -> 1 に上書き
