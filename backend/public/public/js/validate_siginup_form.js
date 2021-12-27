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

        // // メールアドレス
        // const email = document.querySelector('#email');
        // const errMsgEmail = document.querySelector('.err-msg-email');

        // お名前(姓)
        const familyName = document.querySelector("#family_name");
        const errMsgFamilyName = document.querySelector(".err-msg-family_name");
        if (!familyName.value) {
          // // クラスを追加(エラーメッセージを表示する)
          errMsgFamilyName.classList.add("form-invalid");
          // エラーメッセージのテキスト
          errMsgFamilyName.textContent = js_array.MSG_FAMILY_NAME_ERROR;
          // クラスを追加(フォームの枠線を赤くする)
          familyName.classList.add("input-invalid");
          // 後続の処理を止める
          return;
        } else {
          // エラーメッセージのテキストに空文字を代入
          errMsgFamilyName.textContent = "";
          // クラスを削除
          familyName.classList.remove("input-invalid");
        }

        // お名前(名)
        const firstName = document.querySelector("#first_name");
        const errMsgFirstName = document.querySelector(".err-msg-first_name");
        if (!firstName.value) {
          // // クラスを追加(エラーメッセージを表示する)
          errMsgFirstName.classList.add("form-invalid");
          // エラーメッセージのテキスト
          errMsgFirstName.textContent = js_array.MSG_FIRST_NAME_ERROR;
          // クラスを追加(フォームの枠線を赤くする)
          firstName.classList.add("input-invalid");
          // 後続の処理を止める
          return;
        } else {
          // エラーメッセージのテキストに空文字を代入
          errMsgfirstName.textContent = "";
          // クラスを削除
          firstName.classList.remove("input-invalid");
        }

        // // パスワード
        const password = document.querySelector("#password");
        const errMsgPassword = document.querySelector(".err-msg-password");
        // 英小文字数字で8文字以上255文字以下の範囲で1回続く(大文字小文字は区別しない)パスワード
        if (!password.value.match(/^([a-zA-Z0-9]{5,})$/)) {
          errMsgPassword.classList.add("form-invalid");
          errMsgPassword.textContent = js_array.MSG_PASSWORD_REGEX_ERROR;
          password.classList.add("input-invalid");
          return;
        } else {
          errMsgPassword.textContent = "";
          password.classList.remove("input-invalid");
        }

        // パスワード確認
        const passwordConfirm = document.querySelector("#password_confirm");
        const errMsgPasswordConfirm = document.querySelector(
          ".err-msg-password_confirm"
        );
        if (password.value !== passwordConfirm.value) {
          errMsgPasswordConfirm.classList.add("form-invalid");
          errMsgPasswordConfirm.textContent =
            js_array.MSG_PASSWORD_CONFIRM_MISMATCH_ERROR;
          passwordConfirm.classList.add("input-invalid");
          return;
        } else {
          errMsgPasswordConfirm.textContent = "";
          passwordConfirm.remove("input-invalid");
        }
      },
      false
    );
  },
  false
);
