<?php

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Models\Users;
use App\Models\Base;
use App\Config\Config;
use App\Utils\SessionUtil;

// セッション開始
SessionUtil::sessionStart();

try {
	// データベース接続
	$base = Base::getPDOInstance();
	$dbh = new Users($base);
	echo '接続成功' . PHP_EOL;
} catch (\PDOException $e) {
	$_SESSION['err']['msg'] = Config::MSG_PDOEXCEPTION_ERROR;
	header('Location: ../error/error.php', true, 301);
	exit;
}
