<?php
/*
   新たにユーザ登録するためのページ
*/

// POSTによる送信があった
if (!empty($_POST)) {

	if (!$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS)) {
		$error['name'] = 'ユーザ名が空です';
	}
	if (strlen($name) > 26) {
		$error['name'] = '名前は25文字以内にしてください';
	}

	if (!$pass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_SPECIAL_CHARS)) {
		$error['pass'] = 'パスワードを入力してください';
	}
	if (!empty($pass) && strlen($pass) <= 4) {
		$error['pass'] = 'パスワードが短すぎます';
	}
	if (!(strlen($pass) === mb_strlen($pass))) {
		$error['pass'] = 'パスワードに全角が含まれています。すべて半角で入力してください';
	}

	echo 'ユーザ名' . $name . '<br>';
	echo 'パスワード' . $pass . '<br>';
}

/* サニタイジング用関数 */
function sani($str)
{
	return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>新規会員登録</title>
</head>

<body>
	<h1>新規登録</h1>
	<p>必要事項をご記入ください。パスワードは5文字以上、半角英数字で入力してください</p>
	<form action="./signup_form.php" method="post" enctype="multipart/form-data">
		<dl>
			<dt>ユーザー名<font color="red">　必須</font>
			</dt>
			<dd>
				<input type="text" name="name" size="35" maxlength="25" value="">
				<?php if (!empty($error['name'])) : ?>
					<p>
						<font color="red"><?= sani($error['name']) ?></font>
					</p>
				<?php endif; ?>
			</dd>
			<dt>パスワード<font color="red">　必須</font>
			</dt>
			<dd>
				<input type="password" name="pass" size="10" maxlength="20" value="">
				<?php if (!empty($error['pass'])) : ?>
					<p>
						<font color="red"><?= sani($error['pass']) ?></font>
					</p>
				<?php endif; ?>
			</dd>
		</dl>
		<div><input type="submit" value="入力内容を確認"></div>
	</form>
</body>

</html>
