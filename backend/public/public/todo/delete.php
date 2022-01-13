<?php

/** auth */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Utils\SessionUtil;
use App\Utils\Common;
use App\Models\Base;
use App\Models\TodoItems;
use App\Models\Users;
use App\Config\Config;
use App\Utils\Logger;

SessionUtil::sessionStart();

// ログインチェック
if (!Common::isAuthUser()) {
    header('Location: ../login/login_form.php', true, 301);
    exit;
}

// ログイン情報取得
$login = isset($_SESSION['login']) ? $_SESSION['login'] : null;

// GET送信の値を取得
$item_id = $_GET['item_id'];

try {

    $base = Base::getPDOInstance();

    // GET送信で送られてきたIDに合致するtodo_itemsのレコードを1件取得
    $todoItems_table = new TodoItems($base);
    $item = $todoItems_table->getTodoItemByID($item_id);

    // ログインユーザのIDと依頼者のIDが一致しない場合リダイレクトでアクセス制限
    if ($login['is_admin'] === 0 && $login['id'] !== $item['client_id']) {
        header('Location: ./top.php', true, 301);
        exit;
    }

    // 担当者（ユーザー）のレコードを取得
    $staff_id = $item['staff_id'];
    $users_table = new Users($base);
    $user = $users_table->getUserById($staff_id);
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

# 失敗メーセージの初期化
$err_msg = isset($_SESSION['err']) ? $_SESSION['err'] : null;
unset($_SESSION['err']);

// ワンタイムトークン生成
$token = Common::generateToken();


?>

<!-- head箇所 -->
<?php

$title = "削除確認";
$active = "top";
$message = "下記の項目を削除します。よろしいですか？";
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
        <form action="./delete_action.php" method="post">
            <!-- トークン送信 -->
            <input type="hidden" name="token" value="<?= Common::h($token) ?>">
            <!-- 作業IDを送信 -->
            <input type="hidden" name="item_id" value="<?= Common::h($item['id']) ?>">
            <div class="form-group">
                <label for="item_name">項目名</label>
                <p name="item_name" id="item_name" class="form-control"><?= Common::h($item['item_name']) ?></p>
            </div>
            <div class="form-group">
                <label for="staff_id">担当者</label>
                <p name="staff_id" id="staff_id" class="form-control"><?= Common::h($user['family_name'] . " " . $user['first_name']) ?></p>
            </div>
            <div class="form-group">
                <label for="content">作業内容</label>
                <textarea name="content" id="content" class="form-control" cols="30" rows="10" disabled style="background-color: white;"><?= Common::h($item['content']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="expiration_date">期限</label>
                <p class="form-control" id="expiration_date" name="expiration_date"><?= Common::h($item['expiration_date']) ?></p>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="finished" name="finished" value="1" <?php if (!is_null($item['finished_date'])) echo 'checked' ?> disabled>
                <label for="finished">完了</label>
            </div>

            <input type="submit" value="削除" class="btn btn-danger">
            <input type="button" value="キャンセル" class="btn btn-outline-primary" onclick="location.href='./top.php';">
        </form>
    </div>
    <div class="col-sm-3"></div>
</div>
<!-- 入力フォーム ここまで -->

</div>
<!-- コンテナ ここまで -->

<script>
    function checkSubmit() {
        if (window.confirm('作業削除しますか?')) {
            return true;
        } else {
            return false;
        }
    }

    function checkLogout() {
        if (window.confirm('ログアウトしますか?')) {
            return true;
        } else {
            return false;
        }
    }
</script>

<!-- 必要なJavascriptを読み込む -->
<script src="../js/jquery-3.4.1.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>

</body>

</html>
