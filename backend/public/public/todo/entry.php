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

// ログインユーザ情報取得
$login = isset($_SESSION['login']) ? $_SESSION['login'] : null;

// 担当者（ユーザー）のレコードを全件取得
try {
	// DB接続
	$base = Base::getPDOInstance();
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
					<a class="nav-link" href="./top.php">作業一覧</a>
				</li>
				<li class="nav-item active">
					<a class="nav-link" href="./entry.php">作業登録 <span class="sr-only">(current)</span></a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<?= Common::h($login['user_name']) ?>さん
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="../login/logout.php">ログアウト</a>
					</div>
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

			<!-- エラーメッセージ -->
			<div class="row my-2">
				<div class="col-sm-3"></div>
				<div class="col-sm-6 alert alert-danger alert-dismissble fade show">
					担当者を選択してください。 <button class="close" data-dismiss="alert">&times;</button>
				</div>
				<div class="col-sm-3"></div>
			</div>
			<!-- エラーメッセージ ここまで -->

			<!-- 入力フォーム -->
			<div class="row my-2">
				<div class="col-sm-3"></div>
				<div class="col-sm-6">
					<!-- フォーム -->
					<form action="./entry_action.php" method="post" onsubmit="return checkSubmit() ">
						<!-- トークン送信 -->
						<input type="hidden" name="token" value="<?= Common::h($token) ?>">
						<div class="form-group">
							<label for="item_name">項目名</label>
							<input type="text" class="form-control" id="item_name" name="item_name">
						</div>
						<div class="form-group">
							<label for="user_id">担当者</label>
							<select name="user_id" id="user_id" class="form-control">
								<option value="">--選択してください--</option>
								<?php foreach ($users as $user) : ?>
									<!-- selected問題 -->
									<option value="<?= Common::h($user['id']) ?>"><?= Common::h($user['family_name'] . " " . $user['first_name']) ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<div class="form-group">
							<label for="expiration_date">期限</label>
							<input type="date" class="form-control" id="expiration_date" name="expiration_date">
						</div>
						<div class="form-group form-check">
							<!-- checked問題 -->
							<input type="checkbox" class="form-check-input" id="finished" name="finished" value="1">
							<label for="finished">完了</label>
						</div>

						<input type="submit" value="登録" class="btn btn-primary">
						<input type="reset" value="入力内容の初期化" class="btn btn-outline-primary">
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
		</script>

		<!-- 必要なJavascriptを読み込む -->
		<script src="../js/jquery-3.4.1.min.js"></script>
		<script src="../js/bootstrap.bundle.min.js"></script>

</body>

</html>
