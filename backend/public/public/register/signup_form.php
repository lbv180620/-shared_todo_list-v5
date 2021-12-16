<?php

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use \App\Utils\SessionUtil;
use \App\Utils\Common;

// セッション開始
SessionUtil::sessionStart();

// 2回目以降はエラーメッセージを初期化
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
	<title>新規登録</title>
	<link rel="stylesheet" href="../css/bootstrap.min.css">
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
		<a href="../login/login_form.php" class="btn btn-success">ログインへ</a>
	</nav>

	<div class="container">
		<div class="row my-2">
			<div class="col-sm-3"></div>
			<div class="col-sm-6">
				<h1>新規登録してください</h1>
			</div>
			<div class="col-sm-3"></div>
		</div>

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

		<div class="row my-2">
			<div class="col-sm-3"></div>
			<div class="col-sm-6">
				<form action="./register.php" method="post">
					<!-- トークン送信 -->
					<input type="hidden" name="token" value="<?= $token ?>">
					<div class="form-group">
						<label for="user_name">ユーザー名</label>
						<input type="text" class="form-control" id="user_name" name="user_name" value="<?php if (isset($fill['user_name'])) echo Common::h($fill['user_name']) ?>">
					</div>
					<div class="form-group">
						<label for="email">メールアドレス</label>
						<input type="text" class="form-control" id="email" name="email" value="<?php if (isset($fill['email'])) echo Common::h($fill['email']) ?>">

					</div>
					<div class="form-group">
						<label for="family_name">お名前(姓)</label>
						<input type="text" class="form-control" id="family_name" name="family_name" value="<?php if (isset($fill['family_name'])) echo Common::h($fill['family_name']) ?>">
					</div>
					<div class="form-group">
						<label for="first_name">お名前(名)</label>
						<input type="text" class="form-control" id="first_name" name="first_name" value="<?php if (isset($fill['first_name'])) echo Common::h($fill['first_name']) ?>">
					</div>
					<div class="form-group">
						<label for="password">パスワード</label>
						<input type="password" class="form-control" id="password" name="password">
					</div>
					<div class="form-group">
						<label for="password_confirm">パスワード確認</label>
						<input type="password" class="form-control" id="password_confirm" name="password_confirm">
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
