<?php

/** guest */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

/** URL */
require_once dirname(__FILE__, 3) . '/App/Config/url_list.php';

/** メッセージ関連で使用 */

use App\Config\Config;

use App\Utils\Common;

// 共通処理
require_once dirname(__FILE__, 3) . '/components/php/guest/overhead.php';

?>

<!-- head箇所 -->
<?php

$title = "ログイン";
$page = "新規登録";
$message = "ログインしてください";
$page_transition_url = SIGNUP_PAGE_URL;
include_once dirname(__FILE__, 3) . '/components/head/guest/head.php';

?>

<!-- エラメッセージアラート -->
<?php include_once dirname(__FILE__, 3) . '/components/alert/guest/alert_err_msg.php' ?>
<!-- サクセスメッセージアラート -->
<?php include_once dirname(__FILE__, 3) . '/components/alert/guest/alert_success_msg.php' ?>

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
include_once dirname(__FILE__, 3) . '/components/foot/guest/foot.php';

?>
