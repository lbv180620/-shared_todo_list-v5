<?php

/** guest */

use App\Utils\SessionUtil;
use App\Utils\Common;


// セッション開始
SessionUtil::sessionStart();

// ログインチェック
if (!Common::isGuestUser()) {
    header('Location: ../todo/top.php', true, 301);
    exit;
}

# 成功メッセージの初期化(signup_form.php不要)
$success_msg = isset($_SESSION['success']) ? $_SESSION['success']['msg'] : null;
unset($_SESSION['success']);

# 失敗メーセージの初期化
$err_msg = isset($_SESSION['err']) ? $_SESSION['err'] : null;
unset($_SESSION['err']);

// リロード後、記入情報を初期化
$fill = isset($_SESSION['fill']) ? $_SESSION['fill'] : null;
unset($_SESSION['fill']);

// ワンタイムトークン生成
$token = Common::generateToken();
