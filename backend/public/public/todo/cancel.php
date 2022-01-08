<?php

/** auth */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Utils\SessionUtil;
use App\Utils\Common;
use App\Models\Base;
use App\Models\Users;

SessionUtil::sessionStart();

// ログインチェック
if (!Common::isAuthUser()) {
    header('Location: ../login/login_form.php', true, 301);
    exit;
}

// ログイン情報取得
$login = isset($_SESSION['login']) ? $_SESSION['login'] : null;

// GET送信の値を取得
$login_id = $_GET['login_id'];

try {

    $base = Base::getPDOInstance();

    // ログインユーザーのレコードを1件取得
    $users_table = new Users($base);
    $user = $users_table->getUserById($login_id);
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

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>退会</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body>
    <!-- ナビゲーション -->
    <nav class="navbar navbar-expand-md navbar-dark bg-primary">
        <span class="navbar-brand">TODOリスト</span>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="./top.php">作業一覧 <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./entry.php">作業登録</a>
                </li>
                <li class="nav-item dropdown active">
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

    <!-- コンテナ -->
    <div class="container">
        <div class="row my-2">
            <div class="col-sm-3"></div>
            <div class="col-sm-6 alert alert-danger">
                <p><?= Common::h($user['user_name']) ?>さん本人であることを確認して、このまま退会しますか？</p>
                <form action="./cancel_action.php" method="post" onsubmit="return checkSubmit()">
                    <!-- トークン送信 -->
                    <input type="hidden" name="token" value="<?= Common::h($token) ?>">
                    <!-- ログインユーザのidを送信 -->
                    <input type="hidden" name="login_id" value="<?= Common::h($login_id) ?>">
                    <input type="submit" class="btn btn-danger" value="退会">
                    <input type="button" value="キャンセル" class="btn btn-success" onclick="location.href='./top.php';">
                </form>
            </div>
            <div class="col-sm-3"></div>
        </div>
    </div>
    <!-- コンテナ ここまで -->

    <script>
        function checkSubmit() {
            if (window.confirm('本当に退会しますか?')) {
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
