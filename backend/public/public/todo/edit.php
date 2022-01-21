<?php

/** auth */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

/** URL */
require_once dirname(__FILE__, 3) . '/App/Config/url_list.php';

/** DB操作関連で使用 */

use App\Models\Base;
use App\Models\TodoItems;
use App\Models\Users;

/** メッセージ関連で使用 */

use App\Config\Config;
use App\Utils\Common;
use App\Utils\Logger;
use App\Utils\SessionUtil;

SessionUtil::sessionStart();

// ログインチェック
if (!Common::isAuthUser()) {
    header("Location: " . LOGIN_PAGE_URL, true, 301);
    exit;
}

// ログインユーザ情報取得
$login = isset($_SESSION['login']) ? $_SESSION['login'] : null;

// GET送信の値を取得
$item_id = $_GET['item_id'];

try {
    // DB接続
    $base = Base::getPDOInstance();

    // GET送信で送られてきたIDに合致するtodo_itemsのレコードを1件取得
    $todoItems_table = new TodoItems($base);
    $item = $todoItems_table->getTodoItemByID($item_id);

    // 担当者（ユーザー）のレコードを全件取得
    $users_table = new Users($base);
    $users = $users_table->getUserAll();

    // ログインユーザのIDと依頼者のIDが一致しない場合リダイレクトでアクセス制限
    if ($login['is_admin'] === 0 && $login['id'] !== $item['client_id']) {
        header("Locatio: " . TOP_PAGE_URL, true, 301);
        exit;
    }
} catch (\PDOException $e) {

    $_SESSION['err']['msg'] = Config::MSG_PDOEXCEPTION_ERROR;
    Logger::errorLog(Config::MSG_PDOEXCEPTION_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
    header("Location: " . ERROR_PAGE_URL, true, 301);
    exit;
} catch (\Exception $e) {

    $_SESSION['err']['msg'] = Config::MSG_EXCEPTION_ERROR;
    header("Location: " . ERROR_PAGE_URL, true, 301);
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

<?php

/**
 * headとヘッダー(ナビバー)部分
 *
 */

$title = "修正確認";
$active = "top";
$search = "";
include_once dirname(__FILE__, 3) . '/components/head/auth/head.php';

?>

<!-- コンテナ -->
<div class="container">

    <?php

    /**
     * インフォメーション部分
     */

    $message = "作業を修正してください";
    include_once dirname(__FILE__, 3) . '/components/info/auth/info.php';

    ?>

    <!-- エラメッセージアラート -->
    <?php include_once dirname(__FILE__, 3) . '/components/alert/auth/alert_err_msg.php' ?>

    <!-- 入力フォーム -->
    <div class="row my-2">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <!-- フォーム -->
            <form action="./edit_action.php" method="post" onsubmit="return checkSubmit()" id="form">
                <!-- トークン送信 -->
                <input type="hidden" name="token" value="<?= Common::h($token) ?>">
                <!-- 作業IDを送信 -->
                <input type="hidden" name="item_id" value="<?= Common::h($item['id']) ?>">
                <!-- 作成者IDを送信 -->
                <input type="hidden" name="client_id" value="<?= Common::h($login['id']) ?>">
                <div class="form-group">
                    <label for="item_name">項目名</label>
                    <input type="text" name="item_name" id="item_name" class="form-control" value="<?= isset($fill['item_name']) ? Common::h($fill['item_name']) : Common::h($item['item_name']) ?>">
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
                            <?php if (!empty($fill)) : ?>
                                <option value="<?= Common::h($user['id']) ?>" <?php if ((int)$fill['staff_id'] === $user['id']) echo 'selected' ?>><?= Common::h($user['family_name'] . " " . $user['first_name']) ?></option>
                            <?php else : ?>
                                <option value="<?= Common::h($user['id']) ?>" <?php if ($item['staff_id'] === $user['id']) echo 'selected' ?>><?= Common::h($user['family_name'] . " " . $user['first_name']) ?></option>
                            <?php endif ?>
                        <?php endforeach ?>
                    </select>
                    <div class="err-msg-staff"></div>
                </div>
                <div class="form-group">
                    <label for="content">作業内容</label>
                    <textarea name="content" id="content" cols="30" rows="10" class="form-control"><?= isset($fill['content']) ? Common::h($fill['content']) : Common::h($item['content']) ?></textarea>
                    <div class="err-msg-content"></div>
                </div>
                <div class="form-group">
                    <label for="expiration_date">期限</label>
                    <input type="date" class="form-control" id="expiration_date" name="expiration_date" value="<?= isset($fill['expiration_date']) ? Common::h($fill['expiration_date']) : Common::h($item['expiration_date']) ?>">
                    <div class="err-msg-expiration_date"></div>
                </div>
                <div class="form-group form-check">
                    <?php if (!empty($fill)) : ?>
                        <input type="checkbox" class="form-check-input" id="finished" name="finished" value="1" <?php if (isset($fill['finished']) && (int)$fill['finished'] === 1) echo 'checked' ?>>
                    <?php else : ?>
                        <input type="checkbox" class="form-check-input" id="finished" name="finished" value="1" <?php if (!is_null($item['finished_date'])) echo 'checked' ?>>
                    <?php endif ?>
                    <label for="finished">完了</label>
                </div>

                <input type="submit" value="更新" class="btn btn-primary" id="btn">
                <input type="button" value="キャンセル" class="btn btn-outline-primary" onclick="location.href='./top.php';">
            </form>
        </div>
        <div class="col-sm-3"></div>
    </div>
    <!-- 入力フォーム ここまで -->

</div>
<!-- コンテナ ここまで -->

<?php

/**
 * 確認ダイアログ部分
 */

$message = '作業を修正しますか?';
include_once dirname(__FILE__, 3) . '/components/confirm/auth/js_confirm.php';

?>

<?php

/**
 * JSでのバリデーション部分
 */

$js_validation_path = "/validate_edit_form.js";
$validation_list = Config::JS_TODO_FORM_VALIDATION_ERROR_MSG_LIST;
include_once dirname(__FILE__, 3) . '/components/validator/auth/js_validation.php';

?>

<?php

/**
 * フッター部分
 */

include_once dirname(__FILE__, 3) . '/components/foot/auth/foot.php';

?>
