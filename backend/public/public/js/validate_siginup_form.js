window.addEventListener(
  "DOMContentLoaded",
  () => {
    // 送信ボタンの要素を取得
    const submit = document.querySelector(".submit");

    // 送信ボタンの要素にクリックイベントを設定する
    submit.addEventListener(
      "click",
      (event) => {
        // デフォルトアクションをキャンセル
        event.preventDefault();

        // ユーザ名
        const userName = document.querySelector("#user_name");
        const errMsgUserName = document.querySelector(".err-msg-user_name");
        if (!userName.value) {
          // // クラスを追加(エラーメッセージを表示する)
          errMsgUserName.classList.add("form-invalid");
          // エラーメッセージのテキスト
          errMsgUserName.textContent = js_array.MSG_USER_NAME_ERROR;
          // クラスを追加(フォームの枠線を赤くする)
          userName.classList.add("input-invalid");
          // 後続の処理を止める
          return;
        } else {
          // エラーメッセージのテキストに空文字を代入
          errMsgUserName.textContent = "";
          // クラスを削除
          userName.classList.remove("input-invalid");
        }

        // メールアドレス
        const email = document.querySelector("#email");
        const errMsgEmail = document.querySelector(".err-msg-email");
        if (!email.value) {
          errMsgEmail.classList.add("form-invalid");
          errMsgEmail.textContent = js_array.MSG_EMAIL_ERROR;
          email.classList.add("input-invalid");
          return;
        } else {
          errMsgEmail.textContent = "";
          email.classList.remove("input-invalid");
        }

        // お名前(姓)
        const familyName = document.querySelector("#family_name");
        const errMsgFamilyName = document.querySelector(".err-msg-family_name");
        if (!familyName.value) {
          errMsgFamilyName.classList.add("form-invalid");
          errMsgFamilyName.textContent = js_array.MSG_FAMILY_NAME_ERROR;
          familyName.classList.add("input-invalid");
          return;
        } else {
          errMsgFamilyName.textContent = "";
          familyName.classList.remove("input-invalid");
        }

        // お名前(名)
        const firstName = document.querySelector("#first_name");
        const errMsgFirstName = document.querySelector(".err-msg-first_name");
        if (!firstName.value) {
          errMsgFirstName.classList.add("form-invalid");
          errMsgFirstName.textContent = js_array.MSG_FIRST_NAME_ERROR;
          firstName.classList.add("input-invalid");
          return;
        } else {
          errMsgFirstName.textContent = "";
          firstName.classList.remove("input-invalid");
        }

        // パスワード
        const password = document.querySelector("#password");
        const errMsgPassword = document.querySelector(".err-msg-password");
        if (!password.value) {
          errMsgPassword.classList.add("form-invalid");
          errMsgPassword.textContent = js_array.MSG_PASSWORD_ERROR;
          password.classList.add("input-invalid");
          return;
        } else {
          if (!password.value.match(/^([a-z\d]{8,255})$/)) {
            errMsgPassword.classList.add("form-invalid");
            errMsgPassword.textContent = js_array.MSG_PASSWORD_REGEX_ERROR;
            password.classList.add("input-invalid");
            return;
          } else {
            errMsgPassword.textContent = "";
            password.classList.remove("input-invalid");
          }
        }

        // パスワード確認
        const passwordConfirm = document.querySelector("#password_confirm");
        const errMsgPasswordConfirm = document.querySelector(
          ".err-msg-password_confirm"
        );
        if (!passwordConfirm.value) {
          errMsgPasswordConfirm.classList.add("form-invalid");
          errMsgPasswordConfirm.textContent =
            js_array.MSG_PASSWORD_CONFIRM_ERROR;
          passwordConfirm.classList.add("input-invalid");
          return;
        } else {
          if (password.value !== passwordConfirm.value) {
            errMsgPasswordConfirm.classList.add("form-invalid");
            errMsgPasswordConfirm.textContent =
              js_array.MSG_PASSWORD_CONFIRM_MISMATCH_ERROR;
            passwordConfirm.classList.add("input-invalid");
            return;
          } else {
            errMsgPasswordConfirm.textContent = "";
            passwordConfirm.classList.remove("input-invalid");
          }
        }
      },
      false
    );
  },
  false
);
