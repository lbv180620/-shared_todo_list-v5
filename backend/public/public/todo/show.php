<?php

/** auth */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

/** URL */
require_once dirname(__FILE__, 3) . '/App/Config/url_list.php';

/** DB操作関連で使用 */

use App\Models\Base;
use App\Models\TodoItems;
use App\Models\Users;

use App\Utils\Common;
use App\Utils\SessionUtil;

// セッション開始
SessionUtil::sessionStart();

// ログインチェック
if (!Common::isAuthUser()) {
    header("Location: " . LOGIN_PAGE_URL, true, 301);
    exit;
}

// ログインユーザ情報取得
$login = isset($_SESSION['login']) ? $_SESSION['login'] : null;

// GET送信の値を取得
$login_id = $_GET['login_id'];

try {
    // DB接続
    $base = Base::getPDOInstance();
    $todoItems_table = new TodoItems($base);
    $items = $todoItems_table->getTodoItemAllByStaffId($login_id);
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

// ワンタイムトークン生成
$token = Common::generateToken();

?>

<?php

/**
 * headとヘッダー(ナビバー)部分
 *
 */

$title = "マイページ";
$active = "show";
$search = "";
include_once dirname(__FILE__, 3) . '/components/head/auth/head.php';

?>

<style>
    /* ボタンを横並びにする */
    form {
        display: inline-block;
    }

    /* 打消し線を入れる */
    tr.del>td {
        text-decoration: line-through;
    }

    /* ボタンのセルは打消し線を入れない */
    tr.del>td.button {
        text-decoration: none;
    }
</style>

<!-- コンテナ -->
<div class="container">

    <?php

    /**
     * インフォメーション部分
     */

    $message = $login['user_name'] . "さんが担当の作業一覧";
    include_once dirname(__FILE__, 3) . '/components/info/auth/info.php';

    ?>



    <?php if (!empty($items)) : ?>

        <table class="table table-striped table-hover table-bordered table-sm my-2">
            <thead>
                <tr>
                    <!-- item_name -->
                    <th scope="col">項目名</th>
                    <!-- family_name + first_name -->
                    <th scope="col">担当者</th>
                    <th scope="col">依頼者</th>
                    <!-- registration_date -->
                    <th scope="col">登録日</th>
                    <!-- expiration_date -->
                    <th scope="col">期限日</th>
                    <!-- finished_date -->
                    <th scope="col">完了日</th>
                    <!-- ボタン -->
                    <th scope="col">操作</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($items as $item) : ?>
                    <?php if ($item['expiration_date'] < date('Y-m-d') && is_null($item['finished_date'])) : ?>
                        <!-- 期限日が今日を過ぎていて、かつ、完了日がnullのとき、期限日を過ぎたレコードの背景色を変える -->
                        <?php $class = 'class="text-danger"' ?>
                    <?php elseif (!is_null($item['finished_date'])) : ?>
                        <!-- 完了日に値があるときは、完了したレコードの文字に打消し線を入れる -->
                        <?php $class = 'class="del"' ?>
                    <?php else : ?>
                        <?php $class = '' ?>
                    <?php endif ?>
                    <?php
                    $users_table = new Users($base);
                    // 担当者のレコードを取得
                    $staff_id = $item['staff_id'];
                    $user = $users_table->getUserById($staff_id);
                    if (!$user) {
                        $user = [];
                    }
                    // 依頼者のレコードを取得
                    $client_id = $item['client_id'];
                    $client = $users_table->getUserById($client_id);
                    if (!$client) {
                        $client = [];
                    }
                    ?>
                    <tr <?= $class ?>>
                        <!-- 作業項目名 -->
                        <td class="align-middle">
                            <a href="./detail.php?item_id=<?= Common::h($item['id']) ?>"><?= Common::h($item['item_name']) ?></a>
                        </td>
                        <!-- 担当者 -->
                        <td class="align-middle" <?= $user['is_deleted'] === 1 ? 'style="color: red;"' : '' ?>>
                            <?= Common::h($item['family_name']) . " " . Common::h($item['first_name']) ?>
                        </td>
                        <!-- 依頼者 -->
                        <td class="align-middle" <?= $client['is_deleted'] === 1 ? 'style="color: red;"' : '' ?>>
                            <?= isset($client['user_name']) ? Common::h($client['user_name']) : "" ?>
                        </td>
                        <!-- 登録日 -->
                        <td class="align-middle">
                            <?= Common::h($item['registration_date']) ?>
                        </td>
                        <!-- 期限日 -->
                        <td class="align-middle">
                            <?= Common::h($item['expiration_date']) ?>
                        </td>
                        <td class="align-middle">
                            <?php if (empty($item['finished_date'])) : ?>
                                未
                            <?php else : ?>
                                <?= Common::h($item['finished_date']) ?>
                            <?php endif ?>
                        </td>
                        <td class="align-middle button">
                            <!-- フォーム -->
                            <form action="./complete_action.php" method="post" class="my-sm-1">
                                <!-- トークン送信 -->
                                <input type="hidden" name="token" value="<?= Common::h($token) ?>">
                                <!-- 作業ID送信 -->
                                <input type="hidden" name="item_id" value="<?= Common::h($item['id']) ?>">
                                <button class="btn btn-primary my-0" type="submit">完了</button>
                            </form>
                            <a href="./edit.php?item_id=<?= Common::h($item['id']) ?>" class="btn btn-success">修正</a>
                            <a href="./delete.php?item_id=<?= Common::h($item['id']) ?>" class="btn btn-danger">削除</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>

</div>
<!-- コンテナ ここまで -->

<script>
    function checkLogout() {
        if (window.confirm('ログアウトしますか?')) {
            return true;
        } else {
            return false;
        }
    }
</script>

<?php

/**
 * フッター部分
 */

include_once dirname(__FILE__, 3) . '/components/foot/auth/foot.php';

?>
