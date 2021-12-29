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

    /** 正規表現パターン */
    const emailRegexp =
      /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]{1,}.[A-Za-z0-9]{1,}$/;
    const regexp = js_array.JS_DEFAULT_PASSWORD_REGEXP;
    const regexFlg = js_array.JS_DEFAULT_PASSWORD_REGEXFLG;
    const passwordRegexp = new RegExp(regexp, regexFlg);

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
          if (!emailRegexp.test(email.value)) {
            errMsgEmail.classList.add("form-invalid");
            errMsgEmail.textContent = js_array.MSG_EMAIL_INCORRECT_ERROR;
            email.classList.add("input-invalid");
            return;
          } else if (email.value.length > 255) {
            errMsgEmail.classList.add("form-invalid");
            errMsgEmail.textContent = js_array.MSG_EMAIL_STRLEN_ERROR;
            email.classList.add("input-invalid");
            return;
          } else {
            errMsgEmail.textContent = "";
            email.classList.remove("input-invalid");
          }
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
          if (!passwordRegexp.test(password.value)) {
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
