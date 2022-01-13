<?php

/** auth */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Utils\SessionUtil;
use App\Utils\Common;
use App\Models\Base;
use App\Models\Users;
use App\Config\Config;

SessionUtil::sessionStart();

// ログインチェック
if (!Common::isAuthUser()) {
    header('Location: ../login/login_form.php', true, 301);
    exit;
}

// ログインユーザ情報取得
$login = isset($_SESSION['login']) ? $_SESSION['login'] : null;

try {
    // DB接続
    $base = Base::getPDOInstance();

    // 担当者（ユーザー）のレコードを全件取得
    $dbh = new Users($base);
    $users = $dbh->getUserAll();
} catch (\PDOException $e) {

    $_SESSION['err']['msg'] = Config::MSG_PDOEXCEPTION_ERROR;
    Logger::errorLog(Config::MSG_PDOEXCEPTION_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
    header('Location: ../error/error.php', true, 301);
    exit;
} catch (\Exception $e) {

    $_SESSION['err']['msg'] = Config::MSG_EXCEPTION_ERROR;
    Logger::errorLog(Config::MSG_EXCEPTION_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
    header('Location: ../error/error.php', true, 301);
    exit;
}

# 成功メッセージの初期化
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

?>

<!-- head箇所 -->
<?php

$title = "作業登録";
$active = "entry";
$message = "作業を登録してください";
$search = "";
include_once dirname(__FILE__, 2) . '/components/head/auth/head.php';

?>

<!-- エラメッセージアラート -->
<?php include_once dirname(__FILE__, 2) . '/components/alert/auth/alert_err_msg.php' ?>

<!-- 入力フォーム -->
<div class="row my-2">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
        <!-- フォーム -->
        <form action="./entry_action.php" method="post" onsubmit="return checkSubmit()" id="form">
            <!-- トークン送信 -->
            <input type="hidden" name="token" value="<?= Common::h($token) ?>">
            <!-- 作成者IDを送信 -->
            <input type="hidden" name="client_id" value="<?= Common::h($login['id']) ?>">
            <div class="form-group">
                <label for="item_name">項目名</label>
                <input type="text" class="form-control" id="item_name" name="item_name" value="<?php if (isset($fill['item_name'])) echo Common::h($fill['item_name']) ?>">
                <div class="err-msg-item_name"></div>
            </div>
            <div class="form-group">
                <label for="staff_id">担当者</label>
                <select name="staff_id" id="staff" class="form-control">
                    <option value="0">--選択してください--</option>
                    <?php foreach ($users as $user) : ?>
                        <!-- 退会済みのユーザは選択しから消える -->
                        <?php if ($user['is_deleted'] === 1) : ?>
                            <?php continue ?>
                        <?php endif ?>
                        <option value="<?= Common::h($user['id']) ?>" <?= isset($fill['staff_id']) && (int)$fill['staff_id'] === $user['id'] ? 'selected' : '' ?>><?= Common::h($user['family_name'] . " " . $user['first_name']) ?></option>
                    <?php endforeach ?>
                </select>
                <div class="err-msg-staff"></div>
            </div>
            <div class="form-group">
                <label for="content">作業内容</label>
                <textarea name="content" id="content" cols="30" rows="10" class="form-control"><?php if (isset($fill['content'])) echo Common::h($fill['content']) ?></textarea>
                <div class="err-msg-content"></div>
            </div>
            <div class="form-group">
                <label for="expiration_date">期限</label>
                <input type="date" class="form-control" id="expiration_date" name="expiration_date" value="<?php if ($fill['expiration_date']) echo Common::h($fill['expiration_date']) ?>">
                <div class="err-msg-expiration_date"></div>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="finished" name="finished" value="1" <?= isset($fill['finished']) ? 'checked' : '' ?>>
                <label for="finished">完了</label>
            </div>

            <input type="submit" value="登録" class="btn btn-primary" id="btn">
            <input type="reset" value="リセット" class="btn btn-outline-primary">
            <input type="button" value="キャンセル" class="btn btn-outline-primary" onclick="location.href='./top.php';">
        </form>
    </div>
    <div class="col-sm-3"></div>
</div>
<!-- 入力フォーム ここまで -->

<?php

$message = '作業登録しますか?';
$js_validation_path = "/validate_entry_form.js";
$validation_list = Config::JS_TODO_FORM_VALIDATION_ERROR_MSG_LIST;
include_once dirname(__FILE__, 2) . '/components/foot/auth/foot.php';

?>
