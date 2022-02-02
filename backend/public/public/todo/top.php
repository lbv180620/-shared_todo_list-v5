<?php

/** auth */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

/** URL */
require_once dirname(__FILE__, 3) . '/App/Config/url_list.php';

/** DB操作関連で使用 */

use App\Models\Base;
use App\Models\Users;
use App\Models\TodoItems;

/** メッセージ関連で使用 */

use App\Config\Config;

use App\Utils\Common;
use App\Utils\Logger;
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

try {
    // DB接続
    $base = Base::getPDOInstance();
    $todoItems_table = new TodoItems($base);

    if (isset($_GET['search'])) {
        // GETに項目があるときは、検索
        $get = Common::sanitize($_GET);
        $search = $get['search'];
        $isSearch = true;
        $items = $todoItems_table->getTodoItemBySearch($search);
    } else {
        // GETに項目が無いときは、作業項目を全件取得
        $items = $todoItems_table->getTodoItemAll();
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
$updated_item_id = isset($_SESSION['success']['updated_item_id']) ? (int) $_SESSION['success']['updated_item_id'] : null;
unset($_SESSION['success']);

# 失敗メーセージの初期化
$err_msg = isset($_SESSION['err']) ? $_SESSION['err'] : null;
unset($_SESSION['err']);

// 検索キーワード
// ここで初期化される
$search = "";
$isSearch = false;

// ワンタイムトークン生成
$token = Common::generateToken();

?>

<?php

/**
 * headとヘッダー(ナビバー)部分
 *
 */

$title = "作業一覧";
$active = "top";
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
        text-decoration: line-through black;
    }

    /* ボタンのセルは打消し線を入れない */
    tr.del>td.button {
        text-decoration: none;
    }

    tr.spot>td {
        background-color: #FFFF66;
    }

    #item-conf {
        text-decoration: none;
    }
</style>

<!-- コンテナ -->
<div class="container">

    <?php

    /**
     * インフォメーション部分
     */

    $message = "作業一覧 " . count($items) . "件";
    include_once dirname(__FILE__, 3) . '/components/info/auth/info.php';

    ?>

    <!-- エラメッセージアラート -->
    <?php include_once dirname(__FILE__, 3) . '/components/alert/auth/alert_err_msg.php' ?>
    <!-- サクセスメッセージアラート -->
    <?php include_once dirname(__FILE__, 3) . '/components/alert/auth/alert_success_msg.php' ?>

    <?php if (!empty($items)) : ?>
        <?php if ($isSearch) : ?>
            <div class="row my-2">
                <div class="col-sm-3"></div>
                <div class="col-sm-6 alert alert-info">
                    検索結果：<?= count($items) ?>件
                </div>
                <div class="col-sm-3"></div>
            </div>
        <?php endif ?>
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

                    <?php if (!is_null($updated_item_id) && ($updated_item_id === $item['id'])) : ?>
                        <?php if (!empty($class)) : ?>
                            <?php $class = preg_replace('/"\z/',  ' spot"', $class) ?>
                        <?php else : ?>
                            <?php $class = 'class="spot"' ?>
                        <?php endif ?>
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
                            <a id="item-conf" href="./detail.php?item_id=<?= Common::h($item['id']) ?>"><?= Common::h($item['item_name']) ?></a>
                        </td>
                        <!-- 担当者 -->
                        <td class="align-middle" <?= $user['is_deleted'] === 1 ? 'style="color: green;"' : '' ?>>
                            <?= Common::h($item['family_name']) . " " . Common::h($item['first_name']) ?>
                        </td>
                        <!-- 依頼者 -->
                        <td class="align-middle" <?= $client['is_deleted'] === 1 ? 'style="color: green;"' : '' ?>>
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
                            <?php if ($login['is_admin'] === 1 || $login['id'] === $item['staff_id']) : ?>
                                <!-- フォーム -->
                                <form action="./complete_action.php" method="post" class="my-sm-1">
                                    <!-- トークン送信 -->
                                    <input type="hidden" name="token" value="<?= Common::h($token) ?>">
                                    <!-- 作業ID送信 -->
                                    <input type="hidden" name="item_id" value="<?= Common::h($item['id']) ?>">
                                    <!-- すでに完了している場合、完了ボタンが押せないように修正 -->
                                    <button class="btn btn-primary my-0" type="submit" <?= !is_null($item['finished_date']) ? 'disabled' : '' ?>>完了</button>
                                </form>
                            <?php endif ?>
                            <?php if ($login['is_admin'] === 1 || $login['id'] === $client['id']) : ?>
                                <a href="./edit.php?item_id=<?= Common::h($item['id']) ?>" class="btn btn-success">修正</a>
                                <a href="./delete.php?item_id=<?= Common::h($item['id']) ?>" class="btn btn-danger">削除</a>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>

        <p>※<span style="color: green;">緑字</span>のユーザはすでに退会しています。</p>
    <?php elseif (empty($_GET['search']) || empty($items)) : ?>
        <div class="row my-2">
            <div class="col-sm-3"></div>
            <div class="col-sm-6 alert alert-info">
                検索結果がありません。
            </div>
            <div class="col-sm-3"></div>
        </div>
    <?php endif ?>

    <?php if ($isSearch) : ?>
        <!-- 検索のとき、戻るボタンを表示する -->
        <?php if (empty($_GET['search']) || empty($items)) : ?>
            <div class="row my-2">
                <div class="col-sm-3"></div>
                <div class="col-sm-6">
                    <form>
                        <div class="goback">
                            <input type="button" value="もどる" class="btn btn-primary my-0" onclick="location.href='<? Common::h(TOP_PAGE_URL) ?>'">
                        </div>
                    </form>
                </div>
                <div class="col-sm-3"></div>
            </div>
        <?php else : ?>
            <div class="row my-2">
                <div class="col">
                    <form>
                        <div class="goback">
                            <input type="button" value="もどる" class="btn btn-primary my-0" onclick="location.href='./top.php'">
                        </div>
                    </form>
                </div>
            </div>
        <?php endif ?>
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
