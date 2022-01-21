<?php

/**
 * guest
 *
 * login_form.php
 * $title = "ログイン";
 * $page = "新規登録";
 * $message = "ログインしてください";
 * $$page_transition_path = "../register/signup_form.php";
 *
 * signup_form.php
 * $title = "新規登録";
 * $page = "ログイン";
 * $message = "新規登録してください";
 * $$page_transition_path = "../login/login_form.php";
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
        .navbar {
            display: flex;
            justify-content: space-between;
        }

        #a-conf {
            color: inherit;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <!-- ナビゲーション -->
    <nav class="navbar navbar-expand-md navbar-dark bg-primary">
        <span class="navbar-brand"><a href="<?= Common::h(LOGIN_PAGE_URL) ?>" id="a-conf">TODOリスト</a></span>
        <a href="<?= Common::h($page_transition_url) ?>" class="btn btn-success"><?= Common::h($page) ?>へ</a>
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
