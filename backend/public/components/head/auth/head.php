<?php

/** auth */

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
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/validate_form.css">
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
        <span class="navbar-brand"><a href="<?= Common::h(TOP_PAGE_URL) ?>" id="a-conf">TODOリスト</a></span>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item <?= $active === 'top' ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= Common::h(TOP_PAGE_URL) ?>">作業一覧 <span
                            class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item <?= $active === 'entry' ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= Common::h(ENTRY_PAGE_URL) ?>">作業登録</a>
                </li>
                <li class="nav-item dropdown <?= $active === 'show' ? 'active' : '' ?>">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php if ($login['is_admin'] === 1) : ?>
                        <span>管理者</span>
                        <?php endif ?>
                        <?= Common::h($login['user_name']) ?>さん
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item"
                                href="<?= Common::h(SHOW_PAGE_URL) ?>?login_id=<?= Common::h($login['id']) ?>">マイページ</a>
                        </li>
                        <li><a class="dropdown-item"
                                href="<?= Common::h(MEMO_PAGE_URL) ?>?login_id=<?= Common::h($login['id']) ?>">メモ</a>
                        </li>
                        <li>
                            <form action="<?= Common::h(LOGOUT_PAGE_URL) ?>" method="post"
                                onsubmit="return checkLogout()" style="display: inline;">
                                <button type="submit" class="btn btn-danger dropdown-item">ログアウト</button>
                            </form>
                        </li>
                        <li><a class="dropdown-item"
                                href="<?= Common::h(CANCEL_PAGE_URL) ?>?login_id=<?= Common::h($login['id']) ?>">退会</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0" action="<?= Common::h(TOP_PAGE_URL) ?>" method="get">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="search"
                    value="<?= Common::h($search) ?>">
                <button class="btn btn-outline-light my-2 my-sm-0" type="submit">検索</button>
            </form>
        </div>
    </nav>
    <!-- ナビゲーション ここまで -->