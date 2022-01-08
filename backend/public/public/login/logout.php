<?php

/** auth */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Utils\SessionUtil;
use App\Models\Users;
use App\Config\Config;
use App\Utils\Common;

SessionUtil::sessionStart();

// ログインチェック
if (!Common::isAuthUser()) {
    header('Location: ../login/login_form.php', true, 301);
    exit;
}
$result = Users::logout();

if (!$result) {
    $_SESSION['err']['msg'] = Config::MSG_LOGOUT_FAILURE;
    $_SESSION['err']['flg'] = 1;
    header('Location: ../error/error.php', true, 301);
    exit;
}

header('Location: ./login_form.php', true, 301);
exit;
