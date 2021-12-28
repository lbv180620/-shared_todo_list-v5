<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Javascript Validation Test</title>
	<style>
		/* inputのborderの色 */
		.success {
			border: 2px solid green;
		}

		.error {
			border: 2px solid red;
		}

		/* エラーメッセージの非表示 */
		#name-error-message {
			display: none;
		}

		#email-error-message {
			display: none;
		}
	</style>
</head>

<body>
	<div>
		<h3>Test Form</h3>
		<form id="form">
			<div>
				<label>Name</label>
				<input type="name" name="name" id="name">
				<span id="name-error-message">名前は必須かつ3文字以上、6文字以下。</span>
			</div>
			<div>
				<label>Email</label>
				<input type="text" name="email" id="email">
				<span id="email-error-message">Emailの形式では無いようです。</span>
			</div>
			<div>
				<label>性別</label>
				男性：<input type="radio" name="gender" value="男性">
				女性：<input type="radio" name="gender" value="女性">
				<span id="gender-error-message" style="color:red;">どちらかを選択してください。</span>
			</div>
			<div>
				<button id="btn">送る</button>
			</div>
		</form>
	</div>
	<script>

		//Element取得

		//form
		const form = document.getElementById("form");
		//form element
		const name = document.getElementById("name");
		const email = document.getElementById("email");
		const gender = document.getElementsByName("gender");
		//error message
		const name_error_message = document.getElementById("name-error-message")
		const email_error_message = document.getElementById("email-error-message")
		const gender_error_message = document.getElementById("gender-error-message")
		//button
		const btn = document.getElementById("btn");

		//バリデーションパターン
		const nameExp = /^[a-z]{3,5}$/;
		const emailExp = /^[a-z]+@[a-z]+\.[a-z]+$/;

		//初期状態設定
		btn.disabled = true;

		//event

		//name
		name.addEventListener("keyup", e => {
			if (nameExp.test(name.value)) {
				name.setAttribute("class", "success");
				name_error_message.style.display = "none";
			} else {
				name.setAttribute("class", "error");
				name_error_message.style.display = "inline";
			}
			console.log(name.getAttribute("class").includes("success"));
			checkSuccess();
		})

		//email
		email.addEventListener("keyup", e => {
			if (emailExp.test(email.value)) {
				email.setAttribute("class", "success");
				email_error_message.style.display = "none";
			} else {
				email.setAttribute("class", "error");
				email_error_message.style.display = "inline";
			}
			checkSuccess();
		})

		//gender
		gender.forEach(e => {
			e.addEventListener("click", () => {
				// console.log(document.querySelector("input:checked[name=gender]").value)
				gender_error_message.style.display = "none";
				checkSuccess();
			})
		})


		//ボタンのdisabled制御
		const checkSuccess = () => {
			if (name.value && email.value && document.querySelector("input:checked[name=gender]")) {
				if (name.getAttribute("class").includes("success")
					&& email.getAttribute("class").includes("success")
					&& document.querySelector("input:checked[name=gender]").value) {
					btn.disabled = false;
				} else {
					btn.disabled = true;
				}
			}
		}

		//submit
		btn.addEventListener("click", e => {
			e.preventDefault();
			form.method = "post";
			form.action = "entry.php";
			form.submit();
		})

	</script>
</body>

</html>
