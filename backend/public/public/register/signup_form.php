<?php

/** guest */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

use App\Utils\SessionUtil;
use App\Utils\Common;
use App\Config\Config;

// セッション開始
SessionUtil::sessionStart();

// ログインチェック
if (!Common::isGuestUser()) {
	header('Location: ../todo/top.php', true, 301);
	exit;
}

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
	<link rel="stylesheet" href="../css/validate_form.css">
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
	<nav class="navbar navbar-expand-md navbar-dark bg-primary">
		<span class="navbar-brand"><a href="../login/login_form.php" id="a-conf">TODOリスト</a></span>
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
				<!-- フォーム -->
				<form action="./register.php" method="post" onsubmit="return checkSubmit()" id="form">
					<!-- トークン送信 -->
					<input type="hidden" name="token" value="<?= $token ?>">
					<div class="form-group">
						<label for="user_name">ユーザー名</label>
						<input type="text" class="form-control" id="user_name" name="user_name" value="<?php if (isset($fill['user_name'])) echo Common::h($fill['user_name']) ?>" placeholder="太郎">
						<div class="err-msg-user_name"></div>
					</div>
					<div class="form-group">
						<label for="email">メールアドレス</label>
						<input type="text" class="form-control" id="email" name="email" value="<?php if (isset($fill['email'])) echo Common::h($fill['email']) ?>" placeholder="メールアドレス">
						<div class="err-msg-email"></div>
					</div>
					<div class="form-group">
						<label for="family_name">お名前(姓)</label>
						<input type="text" class="form-control" id="family_name" name="family_name" value="<?php if (isset($fill['family_name'])) echo Common::h($fill['family_name']) ?>" placeholder="山田">
						<div class="err-msg-family_name"></div>
					</div>
					<div class="form-group">
						<label for="first_name">お名前(名)</label>
						<input type="text" class="form-control" id="first_name" name="first_name" value="<?php if (isset($fill['first_name'])) echo Common::h($fill['first_name']) ?>" placeholder="太郎">
						<div class="err-msg-first_name"></div>
					</div>
					<div class="form-group">
						<label for="password">パスワード</label>
						<input type="password" class="form-control" id="password" name="password" placeholder="8文字以上の英小文字数字">
						<div class="err-msg-password"></div>
					</div>
					<div class="form-group">
						<label for="password_confirm">パスワード確認</label>
						<input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="8文字以上の英小文字数字">
						<div class="err-msg-password_confirm"></div>
					</div>
					<button type="submit" class="btn btn-primary" id="btn">新規登録</button>
				</form>

			</div>
			<div class="col-sm-3"></div>
		</div>

	</div>

	<script>
		function checkSubmit() {
			if (window.confirm('新規登録しますか?')) {
				return true;
			} else {
				return false;
			}
		}
	</script>

	<!-- JSのフォームバリデーション処理 -->
	<?php
	$php_array = [
		'MSG_USER_NAME_ERROR' => Config::MSG_USER_NAME_ERROR,
		'MSG_EMAIL_ERROR' => Config::MSG_EMAIL_ERROR,
		'MSG_EMAIL_INCORRECT_ERROR' => Config::MSG_EMAIL_INCORRECT_ERROR,
		'MSG_FAMILY_NAME_ERROR' => Config::MSG_FAMILY_NAME_ERROR,
		'MSG_FIRST_NAME_ERROR' => Config::MSG_FIRST_NAME_ERROR,
		'JS_DEFAULT_PASSWORD_REGEXP' => Config::JS_DEFAULT_PASSWORD_REGEXP,
		'JS_DEFAULT_PASSWORD_REGEXFLG' => Config::JS_DEFAULT_PASSWORD_REGEXFLG,
		'MSG_PASSWORD_ERROR' => Config::MSG_PASSWORD_ERROR,
		'MSG_PASSWORD_REGEX_ERROR' => Config::MSG_PASSWORD_REGEX_ERROR,
		'MSG_PASSWORD_CONFIRM_ERROR' => Config::MSG_PASSWORD_CONFIRM_ERROR,
		'MSG_PASSWORD_CONFIRM_MISMATCH_ERROR' => Config::MSG_PASSWORD_CONFIRM_MISMATCH_ERROR,
	];
	$json_array = json_encode(Common::sanitize($php_array));
	?>
	<script type="text/javascript">
		const js_array = JSON.parse('<?= $json_array ?>');
	</script>
	<script type="text/javascript" src="../js/validate_signup_form.js"></script>

	<!-- 必要なJavascriptを読み込む -->
	<script src="../js/jquery-3.4.1.min.js"></script>
	<script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>
