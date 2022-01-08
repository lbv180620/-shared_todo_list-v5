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

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>作業登録</title>
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
                <li class="nav-item">
                    <a class="nav-link" href="./top.php">作業一覧</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="./entry.php">作業登録 <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
        <div class="container">
            <div class="row my-2">
                <div class="col-sm-3"></div>
                <div class="col-sm-6 alert alert-info">
                    作業を登録してください
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

        </div>
        <!-- コンテナ ここまで -->

        <script>
            function checkSubmit() {
                if (window.confirm('作業を登録しますか?')) {
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
        <script type="text/javascript" src="../js/validate_entry_form.js"></script>

        <!-- 必要なJavascriptを読み込む -->
        <script src="../js/jquery-3.4.1.min.js"></script>
        <script src="../js/bootstrap.bundle.min.js"></script>

</body>

</html>
