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

# 成功メッセージの初期化
$success_msg = isset($_SESSION['success']) ? $_SESSION['success']['msg'] : null;
unset($_SESSION['success']);

# 失敗メーセージの初期化
$err_msg = isset($_SESSION['err']) ? $_SESSION['err'] : null;
unset($_SESSION['err']);

try {
	// DB接続
	$base = Base::getPDOInstance();
	$todoItems_table = new TodoItems($base);
	$items = $todoItems_table->getTodoItemAll();
} catch (\PDOException $e) {

	$_SESSION['err']['msg'] = Config::MSG_PDOEXCEPTION_ERROR;
	// $_SESSION['err']['msg'] = $e->getMessage();
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
	<title>作業一覧</title>
	<link rel="stylesheet" href="../css/bootstrap.min.css">
	<style>
		/* ボタンを横並びにする */
		form {
			display: inline-block;
		}

		/* 打消し線を入れる */
		tr.del>td {
			text-decoration: line-through;
		}

		/* ボタンのセルは打消し線を入れない */
		tr.del>td.button {
			text-decoration: none;
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
				<li class="nav-item active">
					<a class="nav-link" href="./top.php">作業一覧 <span class="sr-only">(current)</span></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="./entry.php">作業登録</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<?= Common::h($login['user_name']) ?>さん
					</a>
					<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
						<li>
							<form action="../login/logout.php" method="post" onsubmit="return checkSubmit()" style="display: inline;">
								<button type="submit" class="btn btn-danger dropdown-item">ログアウト</button>
							</form>
						</li>
						<li><a class="dropdown-item" href="#">退会</a></li>
						<li><a class="dropdown-item" href="#">Another action</a></li>
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

		<?php if (!empty($items)) : ?>

			<table class="table table-striped table-hover table-bordered table-sm my-2">
				<thead>
					<tr>
						<!-- item_name -->
						<th scope="col">項目名</th>
						<!-- family_name + first_name -->
						<th scope="col">担当者</th>
						<th scope="col">作成者</th>
						<!-- registration_date -->
						<th scope="col">登録日</th>
						<!-- expiration_date -->
						<th scope="col">期限日</th>
						<!-- finished_date -->
						<th scope="col">完了日</th>
						<!-- ボタン -->
						<th scope="col">操作</th>
					</tr>
				</thead>

				<tbody>
					<?php foreach ($items as $item) : ?>
						<?php if ($item['expiration_date'] < date('Y-m-d') && is_null($item['finished_date'])) : ?>
							<!-- 期限日が今日を過ぎていて、かつ、完了日がnullのとき、期限日を過ぎたレコードの背景色を変える -->
							<?php $class = 'class="text-danger"' ?>
						<?php elseif (!is_null($item['finished_date'])) : ?>
							<!-- 完了日に値があるときは、完了したレコードの文字に打消し線を入れる -->
							<?php $class = 'class="del"' ?>
						<?php else : ?>
							<?php $class = '' ?>
						<?php endif ?>
						<?php
						// 作成者名の取得
						$auth_id = $item['auth_id'];
						$users_table = new Users($base);
						$author = $users_table->getUserByAuthId($auth_id);
						if (!$author) {
							$author = [];
						}
						?>
						<tr <?= $class ?>>
							<!-- 作業項目名 -->
							<td class="align-middle">
								<?= Common::h($item['item_name']) ?>
							</td>
							<!-- 担当者 -->
							<td class="align-middle">
								<?= Common::h($item['family_name']) . " " . Common::h($item['first_name']) ?>
							</td>
							<!-- 作成者 -->
							<td class="align-middle">
								<?= isset($author['user_name']) ? Common::h($author['user_name']) : "" ?>
							</td>
							<!-- 登録日 -->
							<td class="align-middle">
								<?= Common::h($item['registration_date']) ?>
							</td>
							<!-- 期限日 -->
							<td class="align-middle">
								<?= Common::h($item['expiration_date']) ?>
							</td>
							<td class="align-middle">
								<?php if (empty($item['finished_date'])) : ?>
									未
								<?php else : ?>
									<?= Common::h($item['finished_date']) ?>
								<?php endif ?>
							</td>
							<td class="align-middle button">
								<!-- フォーム -->
								<form action="./complete_action.php" method="post" class="my-sm-1">
									<!-- トークン送信 -->
									<input type="hidden" name="token" value="<?= Common::h($token) ?>">
									<input type="hidden" name="item_id" value="<?= Common::h($item['id']) ?>">
									<button class="btn btn-primary my-0" type="submit">完了</button>
								</form>
								<a href="./edit.php?item_id=<?= Common::h($item['id']) ?>" class="btn btn-success">修正</a>
								<a href="./delete.php?item_id=<?= Common::h($item['id']) ?>" class="btn btn-danger">削除</a>
							</td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		<?php endif ?>


	</div>
	<!-- コンテナ ここまで -->

	<script>
		function checkSubmit() {
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
