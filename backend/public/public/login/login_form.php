<?php

/** guest */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Utils\SessionUtil;
use App\Utils\Common;

SessionUtil::sessionStart();

// ログインチェック
if (!Common::isGuestUser()) {
	header('Location: ../todo/top.php', true, 301);
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

// var_dump($_SESSION['login']);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>ログイン</title>
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
		<a href="../register/signup_form.php" class="btn btn-success">新規登録へ</a>
	</nav>

	<div class="container">
		<div class="row my-2">
			<div class="col-sm-3"></div>
			<div class="col-sm-6">
				<h1>ログインしてください</h1>
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
		<!-- サクセスメッセージアラート -->
		<?php if (isset($success_msg)) : ?>
			<div class="row my-2">
				<div class="col-sm-3"></div>
				<div class="col-sm-6 alert alert-success alert-dismissble fade show">
					<button class="close" data-dismiss="alert">&times;</button>
					<p><?= Common::h($success_msg) ?></p>
				</div>
				<div class="col-sm-3"></div>
			</div>
		<?php endif ?>

		<div class="row my-2">
			<div class="col-sm-3"></div>
			<div class="col-sm-6">
				<!-- フォーム -->
				<form action="./login.php" method="post" onsubmit="return checkSubmit() ">
					<!-- トークン送信 -->
					<input type="hidden" name="token" value="<?= Common::h($token) ?>">
					<div class="form-group">
						<label for="email">メールアドレス</label>
						<input type="text" class="form-control" id="email" name="email" value="<?php if (isset($fill['email'])) echo Common::h($fill['email']) ?>">
					</div>
					<div class="form-group">
						<label for="password">パスワード</label>
						<input type="password" class="form-control" id="password" name="password">
					</div>
					<button type="submit" class="btn btn-primary">ログイン</button>
				</form>
			</div>
			<div class="col-sm-3"></div>
		</div>

	</div>

	<script>
		function checkSubmit() {
			if (window.confirm('ログインしますかしますか?')) {
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
