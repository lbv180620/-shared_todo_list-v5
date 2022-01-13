<?php

/** guest */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Utils\Common;
use App\Config\Config;

// 共通処理
require_once dirname(__FILE__, 2) . '/components/php/guest/overhead.php';

?>

<!-- head箇所 -->
<?php

$title = "ログイン";
$page = "新規登録";
$login_form_dir = ".";
$page_transition_path = "../register/signup_form.php";
include_once dirname(__FILE__, 2) . '/components/head/guest/head.php';

?>

<!-- エラメッセージアラート -->
<?php include_once dirname(__FILE__, 2) . '/components/alert/guest/alert_err_msg.php' ?>
<!-- サクセスメッセージアラート -->
<?php include_once dirname(__FILE__, 2) . '/components/alert/guest/alert_success_msg.php' ?>

<!-- コンテンツ -->
<div class="row my-2">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
        <!-- フォーム -->
        <form action="./login.php" method="post" onsubmit="return checkSubmit() " id="form">
            <!-- トークン送信 -->
            <input type="hidden" name="token" value="<?= Common::h($token) ?>">
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="text" class="form-control" id="email" name="email" value="<?php if (isset($fill['email'])) echo Common::h($fill['email']) ?>">
                <div class="err-msg-email"></div>
            </div>
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" class="form-control" id="password" name="password">
                <div class="err-msg-password"></div>
            </div>
            <button type="submit" class="btn btn-primary" id="btn">ログイン</button>
        </form>
    </div>
    <div class="col-sm-3"></div>
</div>

<!-- foot箇所 -->
<?php

$title = "ログイン";
$js_validation_path = "/validate_login_form.js";
$validation_list = Config::JS_LOGIN_FORM_VALIDATION_ERROR_MSG_LIST;
include_once dirname(__FILE__, 2) . '/components/foot/guest/foot.php';

?>
