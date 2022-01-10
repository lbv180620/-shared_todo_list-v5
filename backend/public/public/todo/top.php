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

# 成功メッセージの初期化
$success_msg = isset($_SESSION['success']) ? $_SESSION['success']['msg'] : null;
unset($_SESSION['success']);

# 失敗メーセージの初期化
$err_msg = isset($_SESSION['err']) ? $_SESSION['err'] : null;
unset($_SESSION['err']);


// 検索キーワード
// ここで初期化される
$search = "";
$isSearch = false;

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
    $_SESSION['err']['msg'] = $e->getMessage();
    Logger::errorLog(Config::MSG_PDOEXCEPTION_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
    header('Location: ../error/error.php', true, 301);
    exit;
} catch (\Exception $e) {

    $_SESSION['err']['msg'] = Config::MSG_EXCEPTION_ERROR;
    Logger::errorLog(Config::MSG_EXCEPTION_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
    header('Location: ../error/error.php', true, 301);
    exit;
}

// ワンタイムトークン生成
$token = Common::generateToken();

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>作業一覧</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
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

        #a-conf {
            color: inherit;
            text-decoration: none;
        }

        #item-conf {
            text-decoration: none;
        }
    </style>
</head>

<body>
    <!-- ナビゲーション -->
    <nav class="navbar navbar-expand-md navbar-dark bg-primary">
        <span class="navbar-brand"><a href="./top.php" id="a-conf">TODOリスト</a></span>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="./top.php">作業一覧 <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./entry.php">作業登録</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php if ($login['is_admin'] === 1) : ?>
                            <span>管理者</span>
                        <?php endif ?>
                        <?= Common::h($login['user_name']) ?>さん
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="./show.php?login_id=<?= Common::h($login['id']) ?>">マイページ</a></li>
                        <li>
                            <form action="../login/logout.php" method="post" onsubmit="return checkLogout()" style="display: inline;">
                                <button type="submit" class="btn btn-danger dropdown-item">ログアウト</button>
                            </form>
                        </li>
                        <li><a class="dropdown-item" href="./cancel.php?login_id=<?= Common::h($login['id']) ?>">退会</a></li>
                        <li>

                        </li>
                    </ul>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0" action="./top.php" method="get">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="search" value="<?= Common::h($search) ?>">
                <button class="btn btn-outline-light my-2 my-sm-0" type="submit">検索</button>
            </form>
        </div>
    </nav>
    <!-- ナビゲーション ここまで -->

    <!-- コンテナ -->
    <div class="container">
        <!-- エラメッセージアラート -->
        <?php if (isset($err_msg)) : ?>
            <div class="row my-2">
                <div class="col-sm-3"></div>
                <div class="col-sm-6 alert alert-danger alert-dismissble fade show">
                    <button class="close" data-dismiss="alert">&times;</button>
                    <?php foreach ($err_msg as $v) : ?>
                        <p>・<?= Common::h($v) ?></p>
                    <?php endforeach ?>
                </div>
                <div class="col-sm-3"></div>
            </div>
        <?php endif ?>
        <!-- サクセスメッセージアラート -->
        <?php if (isset($success_msg)) : ?>
            <div class="row my-2">
                <div class="col-sm-3"></div>
                <div class="col-sm-6 alert alert-success alert-dismissble fade show">
                    <button class="close" data-dismiss="alert">&times;</button>
                    <p><?= Common::h($success_msg) ?></p>
                    <?php if (Common::checkStringForLogout($success_msg)) : ?>
                        <p><?= Config::DEFAULT_DELAY_TIME ?>秒後ログアウトします。</p>
                        <?= Config::LOGOUT_SCRIPT ?>
                    <?php endif ?>
                </div>
                <div class="col-sm-3"></div>
            </div>
        <?php endif ?>

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
                                <input type="button" value="もどる" class="btn btn-primary my-0" onclick="location.href='./top.php'">
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



    <!-- 必要なJavascriptを読み込む -->
    <script src="../js/jquery-3.4.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>

</body>

</html>
