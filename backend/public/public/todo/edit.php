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

    // 担当者（ユーザー）のレコードを全件取得
    $users_table = new Users($base);
    $users = $users_table->getUserAll();

    // ログインユーザのIDと依頼者のIDが一致しない場合リダイレクトでアクセス制限
    if ($login['is_admin'] === 0 && $login['id'] !== $item['client_id']) {
        header('Location: ./top.php', true, 301);
        exit;
    }
} catch (\PDOException $e) {

    // $_SESSION['err']['msg'] = Config::MSG_PDOEXCEPTION_ERROR;
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

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>作業修正</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/validate_form.css">
    <style>
        #a-conf {
            color: inherit;
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
                    <a class="nav-link dropdown-toggle" href="./detail.php?id=<?= Common::h($login['id']) ?>" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                    </ul>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0" action="./" method="get">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="search" value="">
                <button class="btn btn-outline-light my-2 my-sm-0" type="submit">検索</button>
            </form>
        </div>
    </nav>
    <!-- ナビゲーション ここまで -->

    <!-- コンテナ -->
    <div class="container">
        <div class="row my-2">
            <div class="col-sm-3"></div>
            <div class="col-sm-6 alert alert-info">
                作業内容を修正してください
            </div>
            <div class="col-sm-3"></div>
        </div>

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
        <!-- エラーメッセージ ここまで -->

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

    <script>
        function checkSubmit() {
            if (window.confirm('更新しますか?')) {
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

    <!-- JSのフォームバリデーション処理 -->
    <?php
    $php_array = Config::JS_TODO_FORM_VALIDATION_ERROR_MSG_LIST;
    $json_array = json_encode($php_array);
    ?>
    <script type="text/javascript">
        const js_array = JSON.parse('<?= $json_array ?>');
    </script>
    <script type="text/javascript" src="../js/validate_edit_form.js"></script>

    <!-- 必要なJavascriptを読み込む -->
    <script src="../js/jquery-3.4.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>

</body>

</html>
