document.addEventListener(
  "DOMContentLoaded",
  () => {
    /** Element取得 */
    // form
    const form = document.querySelector("#form");
    // form element
    const email = document.querySelector("#email");
    const password = document.querySelector("#password");
    // error message
    const errMsgEmail = document.querySelector(".err-msg-email");
    const errMsgPassword = document.querySelector(".err-msg-password");
    // button
    const btn = document.querySelector("#btn");
    // バリデーションパターン
    // const emailExp =
    //   /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:.[a-zA-Z0-9-]+)*$/;
    const paaswordExp = /^([a-z\d]{8,255})$/;
    // 初期状態設定
    // btn.disabled = true;

    /** event */
    // email
    email.addEventListener(
      "keyup",
      (e) => {
        if (!email.value) {
          errMsgEmail.classList.add("form-invalid");
          errMsgEmail.textContent = js_array.MSG_EMAIL_ERROR;
          email.classList.add("input-invalid");
          return;
        } else {
          errMsgEmail.textContent = "";
          email.classList.remove("input-invalid");
        }
      },
      false
    );
    // password
    password.addEventListener(
      "keyup",
      (e) => {
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
      },
      false
    );
    // buttonのdisabled制御関数
    // submit
    btn.addEventListener(
      "click",
      (e) => {
        e.preventDefault();
        form.method = "post";
        form.action = "./login.php";
        if ((form.onsubmit = checkSubmit())) {
          form.submit();
        } else {
          return false;
        }
      },
      false
    );
  },
  false
);
