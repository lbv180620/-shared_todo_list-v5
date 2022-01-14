<?php

/**
 * auth
 *
 * top.php
 * $title = "作業一覧";
 * $active = "top";
 * $message = "作業一覧";
 *
 * entry.php
 * $title = "作業登録";
 * $active = "entry";
 * $message = "作業を登録してください";
 *
 * edit.php
 * $title = "修正確認";
 * $active = "top";
 * $message = "作業を修正してください";
 *
 * delete.php
 * $title = "削除確認";
 * $active = "top";
 * $message = "下記の項目を削除します。よろしいですか？";
 *
 * show.php
 * $title = "マイページ";
 * $active = "show";
 * $message = $login['user_name'] . "さんが担当の作業一覧";
 *
 * detail.php
 * $title = "詳細確認";
 * $active = "top";
 * $message = "作業の詳細";
 *
 * cancel.php
 * $title = "退会確認";
 * $active = "show";
 * $message = "";
 *
 */

use App\Utils\Common;

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title><?= Common::h($title) ?></title>
    <link rel="stylesheet" href="../views/css/bootstrap.min.css">
    <link rel="stylesheet" href="../views/css/validate_form.css">
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
                <li class="nav-item <?= $active === 'top' ? 'active' : '' ?>">
                    <a class="nav-link" href="./top.php">作業一覧 <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item <?= $active === 'entry' ? 'active' : '' ?>">
                    <a class="nav-link" href="./entry.php">作業登録</a>
                </li>
                <li class="nav-item dropdown <?= $active === 'show' ? 'active' : '' ?>">
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
        <div class="row my-2">
            <div class="col-sm-3"></div>
            <div class="col-sm-6 alert alert-info">
                <?= Common::h($message) ?>
            </div>
            <div class="col-sm-3"></div>
        </div>
