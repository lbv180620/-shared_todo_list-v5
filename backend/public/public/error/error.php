<?php

/** guest | auth */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Utils\SessionUtil;

// セッション開始
SessionUtil::sessionStart();

// エラーメッセージの初期化
$err_msg = $_SESSION['err']['msg'];
$err_flg = isset($_SESSION['err']['flg']) ? $_SESSION['err']['flg'] : null;
unset($_SESSION['err']);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>エラーメッセージ</title>
    <link rel="stylesheet" href="../views/css/bootstrap.min.css">
    <style>
        .navbar {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-primary">
        <span class="navbar-brand">TODOリスト</span>
        <?php if (isset($_SESSION['login'])) : ?>
            <a href="../login/login_form.php" class="btn btn-success">ログインへ</a>
        <?php else : ?>
            <a href="../register/signup_form.php" class="btn btn-success">新規登録へ</a>
        <?php endif ?>
    </nav>

    <div class="container">
        <div class="row my-2">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <h1>エラーが発生しました。</h1>
            </div>
            <div class="col-sm-3"></div>
        </div>

        <div class="row my-2">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <!-- エラーメッセージ -->
                <div class="row my-2">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6 alert alert-danger alert-dismissble fade show">
                        <p><?= $err_msg ?></p>
                        <?php if (!isset($err_flg) || $err_flg !== 1) : ?>
                            <form class="mt-4">
                                <input type="button" class="btn btn-danger" value="ログアウト" onclick="location.href='../login/logout.php';">
                            </form>
                        <?php endif ?>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <!-- エラーメッセージ ここまで -->

            </div>
            <div class="col-sm-3"></div>
        </div>

    </div>

    <!-- 必要なJavascriptを読み込む -->
    <script src="../views/js/jquery-3.4.1.min.js"></script>
    <script src="../views/js/bootstrap.bundle.min.js"></script>

</body>

</html>
