<?php

/** auth */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Utils\SessionUtil;
use App\Utils\Common;
use App\Models\Base;
use App\Models\TodoItems;
use App\Models\Users;
use App\Config\Config;
use App\Utils\Logger;

SessionUtil::sessionStart();

// ログインチェック
if (!Common::isAuthUser()) {
	header('Location: ../login/login_form.php', true, 301);
	exit;
}

// ログイン情報取得
$login = isset($_SESSION['login']) ? $_SESSION['login'] : null;

// GET送信の値を取得
$item_id = $_GET['item_id'];

try {

	$base = Base::getPDOInstance();

	// GET送信で送られてきたIDに合致するtodo_itemsのレコードを1件取得
	$todoItems_table = new TodoItems($base);
	$item = $todoItems_table->getTodoItemByID($item_id);

	// 担当者（ユーザー）のレコードを取得
	$staff_id = $item['staff_id'];
	$users_table = new Users($base);
	$user = $users_table->getUserById($staff_id);
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
	<title>詳細確認</title>
	<link rel="stylesheet" href="../css/bootstrap.min.css">
	<style>
		/* ボタンを横並びにする */
		form {
			display: inline-block;
		}
	</style>
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
				<li class="nav-item">
					<a class="nav-link" href="./entry.php">作業登録 <span class="sr-only">(current)</span></a>
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
						<li><a class="dropdown-item" href="./cancel.php?login_id=<?= Common::h($login['id']) ?>">退会</a></li>
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

	<!-- コンテナ -->
	<div class="container">
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

		<!-- 入力フォーム -->
		<div class="row my-2">
			<div class="col-sm-3"></div>
			<div class="col-sm-6">
				<div class="form-group">
					<label for="item_name">項目名</label>
					<p name="item_name" id="item_name" class="form-control"><?= Common::h($item['item_name']) ?></p>
				</div>
				<div class="form-group">
					<label for="staff_id">担当者</label>
					<p name="staff_id" id="staff_id" class="form-control"><?= Common::h($user['family_name'] . " " . $user['first_name']) ?></p>
				</div>
				<div class="form-group">
					<label for="content">作業内容</label>
					<textarea name="content" id="content" class="form-control" cols="30" rows="10" disabled style="background-color: white;"><?= Common::h($item['content']) ?></textarea>
				</div>
				<div class="form-group">
					<label for="expiration_date">期限</label>
					<p class="form-control" id="expiration_date" name="expiration_date"><?= Common::h($item['expiration_date']) ?></p>
				</div>
				<div class="form-group form-check">
					<input type="checkbox" class="form-check-input" id="finished" name="finished" value="1" <?php if (!is_null($item['finished_date'])) echo 'checked' ?> disabled>
					<label for="finished">完了</label>
				</div>
				<!-- フォーム -->
				<form action="./complete_action.php" method="post" class="my-sm-1">
					<!-- トークン送信 -->
					<input type="hidden" name="token" value="<?= Common::h($token) ?>">
					<!-- 作業ID送信 -->
					<input type="hidden" name="item_id" value="<?= Common::h($item['id']) ?>">
					<button class="btn btn-primary my-0" type="submit">完了</button>
				</form>
				<a href="./edit.php?item_id=<?= Common::h($item['id']) ?>" class="btn btn-success">修正</a>
				<a href="./delete.php?item_id=<?= Common::h($item['id']) ?>" class="btn btn-danger">削除</a>
				<a href="./top.php" class="btn btn-outline-primary">もどる</a>
			</div>
			<div class="col-sm-3"></div>
		</div>
		<!-- 入力フォーム ここまで -->

	</div>
	<!-- コンテナ ここまで -->

	<script>
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
