<?php

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use \App\Utils\SessionUtil;

// セッション開始
SessionUtil::sessionStart();

// 2回目以降はエラーメッセージを初期化
$err_msg = isset($_SESSION['err']) ? $_SESSION['err'] : null;
unset($_SESSION['err']);


?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>新規登録</title>
	<link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body>
	<nav class="navbar navbar-expand-md navbar-dark bg-primary">
		<span class="navbar-brand">TODOリスト</span>
	</nav>

	<div class="container">
		<div class="row my-2">
			<div class="col-sm-3"></div>
			<div class="col-sm-3">
				<h1></h1>
			</div>
			<div class="col-sm-3"></div>
		</div>

		<div class="row my-2">
			<div class="col-sm-3"></div>
			<div class="col-sm-6 alert alert-danger alert-dismissble fade show">
				ユーザー名またはパスワードが違います。 <button class="close" data-dismiss="alert">&times;</button>
			</div>
			<div class="col-sm-3"></div>
		</div>

		<div class="row my-2">
			<div class="col-sm-3"></div>
			<div class="col-sm-6">
				<form action="./register.php" method="post">
					<div class="form-group">
						<label for="user_name">ユーザー名</label>
						<input type="text" class="form-control" id="user_name" name="user_name">
						<?php if (isset($err_msg['user_name'])) : ?>
							<div class="alert alert-danger" role="alert">
								<p><?= $err_msg['user_name'] ?></p>
							</div>
						<?php endif ?>
					</div>
					<div class="form-group">
						<label for="email">メールアドレス</label>
						<input type="text" class="form-control" id="email" name="email">
						<?php if (isset($err_msg['email'])) : ?>
							<div class="alert alert-danger" role="alert">
								<p><?= $err_msg['email'] ?></p>
							</div>
						<?php endif ?>
					</div>
					<div class="form-group">
						<label for="family_name">お名前(姓)</label>
						<input type="text" class="form-control" id="family_name" name="family_name">
						<?php if (isset($err_msg['family_name'])) : ?>
							<div class="alert alert-danger" role="alert">
								<p><?= $err_msg['family_name'] ?></p>
							</div>
						<?php endif ?>
					</div>
					<div class="form-group">
						<label for="first_name">お名前(名)</label>
						<input type="text" class="form-control" id="first_name" name="first_name">
						<?php if (isset($err_msg['first_name'])) : ?>
							<div class="alert alert-danger" role="alert">
								<p><?= $err_msg['first_name'] ?></p>
							</div>
						<?php endif ?>
					</div>
					<div class="form-group">
						<label for="password">パスワード</label>
						<input type="password" class="form-control" id="password" name="password">
						<?php if (isset($err_msg['password'])) : ?>
							<div class="alert alert-danger" role="alert">
								<p><?= $err_msg['password'] ?></p>
							</div>
						<?php endif ?>
					</div>
					<div class="form-group">
						<label for="password_confirm">パスワード確認</label>
						<input type="password" class="form-control" id="password_comfirm" name="password_comfirm">
						<?php if (isset($err_msg['password_confirm'])) : ?>
							<div class="alert alert-danger" role="alert">
								<p><?= $err_msg['password_confirm'] ?></p>
							</div>
						<?php endif ?>
					</div>
					<button type="submit" class="btn btn-primary">新規登録</button>
				</form>

			</div>
			<div class="col-sm-3"></div>
		</div>

	</div>

	<!-- 必要なJavascriptを読み込む -->
	<script src="../js/jquery-3.4.1.min.js"></script>
	<script src="../js/bootstrap.bundle.min.js"></script>

</body>

</html>
