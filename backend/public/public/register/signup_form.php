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

$title = "新規登録";
$page = "ログイン";
$login_form_dir = "../login";
$page_transition_path = "../login/login_form.php";
include_once dirname(__FILE__, 2) . '/components/head/guest/head.php';

?>

<!-- エラメッセージアラート -->
<?php include_once dirname(__FILE__, 2) . '/components/alert/guest/alert_err_msg.php' ?>

<!-- コンテンツ -->
<div class="row my-2">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
        <!-- フォーム -->
        <form action="./register.php" method="post" onsubmit="return checkSubmit()" id="form">
            <!-- トークン送信 -->
            <input type="hidden" name="token" value="<?= $token ?>">
            <div class="form-group">
                <label for="user_name">ユーザー名</label>
                <input type="text" class="form-control" id="user_name" name="user_name" value="<?php if (isset($fill['user_name'])) echo Common::h($fill['user_name']) ?>" placeholder="太郎">
                <div class="err-msg-user_name"></div>
            </div>
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="text" class="form-control" id="email" name="email" value="<?php if (isset($fill['email'])) echo Common::h($fill['email']) ?>" placeholder="メールアドレス">
                <div class="err-msg-email"></div>
            </div>
            <div class="form-group">
                <label for="family_name">お名前(姓)</label>
                <input type="text" class="form-control" id="family_name" name="family_name" value="<?php if (isset($fill['family_name'])) echo Common::h($fill['family_name']) ?>" placeholder="山田">
                <div class="err-msg-family_name"></div>
            </div>
            <div class="form-group">
                <label for="first_name">お名前(名)</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php if (isset($fill['first_name'])) echo Common::h($fill['first_name']) ?>" placeholder="太郎">
                <div class="err-msg-first_name"></div>
            </div>
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="8文字以上の英小文字数字">
                <div class="err-msg-password"></div>
            </div>
            <div class="form-group">
                <label for="password_confirm">パスワード確認</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="8文字以上の英小文字数字">
                <div class="err-msg-password_confirm"></div>
            </div>
            <button type="submit" class="btn btn-primary" id="btn">新規登録</button>
        </form>

    </div>
    <div class="col-sm-3"></div>
</div>

<!-- foot箇所 -->
<?php

$title = "新規登録";
$js_validation_path = "/validate_signup_form.js";
$validation_list = Config::JS_SIGNUP_FORM_VALIDATION_ERROR_MSG_LIST;
include_once dirname(__FILE__, 2) . '/components/foot/guest/foot.php';

?>
