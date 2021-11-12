<?php

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Utils\SessionUtil;

// セッション開始
SessionUtil::sessionStart();

// エラーメッセージが無い場合、ログインフォームへリダイレクト
if (!isset($_SESSION['err']['msg'])) {
	header('Location: ../login/login_form.php');
	exit;
}

// エラーメッセージの初期化
$err_msg = $_SESSION['err']['msg'];
unset($_SESSION['err']['msg']);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>エラーメッセージ</title>
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
			<div class="col-sm-6">
				<!-- エラーメッセージ -->
				<div class="row my-2">
					<div class="col-sm-3"></div>
					<div class="col-sm-6 alert alert-danger alert-dismissble fade show">
						申し訳ございません。<br>エラーが発生しました。
						<p><?= $err_msg ?></p>
						<form class="mt-4">
							<input type="button" class="btn btn-danger" value="ログアウト" onclick="location.href='../login/index.html';">
						</form>

					</div>
					<div class="col-sm-3"></div>
				</div>
				<!-- エラーメッセージ ここまで -->

			</div>
			<div class="col-sm-3"></div>
		</div>

	</div>

	<!-- 必要なJavascriptを読み込む -->
	<script src="../js/jquery-3.4.1.min.js"></script>
	<script src="../js/bootstrap.bundle.min.js"></script>

</body>

</html>
