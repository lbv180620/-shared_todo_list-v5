document.addEventListener(
  "DOMContentLoaded",
  () => {
    /** Element取得 */

    // form
    const form = document.querySelector("#form");

    // form element
    const userName = document.querySelector("#user_name");
    const email = document.querySelector("#email");
    const familyName = document.querySelector("#family_name");
    const firstName = document.querySelector("#first_name");
    const password = document.querySelector("#password");
    const passwordConfirm = document.querySelector("#password_confirm");

    // error message
    const errMsgUserName = document.querySelector(".err-msg-user_name");
    const errMsgEmail = document.querySelector(".err-msg-email");
    const errMsgFamilyName = document.querySelector(".err-msg-family_name");
    const errMsgFirstName = document.querySelector(".err-msg-first_name");
    const errMsgPassword = document.querySelector(".err-msg-password");
    const errMsgPasswordConfirm = document.querySelector(
      ".err-msg-password_confirm"
    );

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

    // user_name
    userName.addEventListener(
      "keyup",
      (e) => {
        if (!userName.value) {
          errMsgUserName.classList.add("form-invalid");
          errMsgUserName.textContent = js_array.MSG_USER_NAME_ERROR;
          userName.classList.add("input-invalid");
          return;
        } else {
          if (userName.value.length > 50) {
            errMsgUserName.classList.add("form-invalid");
            errMsgUserName.textContent = js_array.MSG_USER_NAME_STRLEN_ERROR;
            userName.classList.add("input-invalid");
            return;
          } else {
            errMsgUserName.textContent = "";
            userName.classList.remove("input-invalid");
          }
        }
      },
      false
    );

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

    // family_name
    familyName.addEventListener(
      "keyup",
      (e) => {
        if (!familyName.value) {
          errMsgFamilyName.classList.add("form-invalid");
          errMsgFamilyName.textContent = js_array.MSG_FAMILY_NAME_ERROR;
          familyName.classList.add("input-invalid");
          return;
        } else {
          if (familyName.value.length > 50) {
            errMsgFamilyName.classList.add("form-invalid");
            errMsgFamilyName.textContent =
              js_array.MSG_FAMILY_NAME_STRLEN_ERROR;
            familyName.classList.add("input-invalid");
            return;
          } else {
            errMsgFamilyName.textContent = "";
            familyName.classList.remove("input-invalid");
          }
        }
      },
      false
    );

    // first_name
    firstName.addEventListener(
      "keyup",
      (e) => {
        if (!firstName.value) {
          errMsgFirstName.classList.add("form-invalid");
          errMsgFirstName.textContent = js_array.MSG_FIRST_NAME_ERROR;
          firstName.classList.add("input-invalid");
          return;
        } else {
          if (firstName.value.length > 50) {
            errMsgFirstName.classList.add("form-invalid");
            errMsgFirstName.textContent = js_array.MSG_FIRST_NAME_STRLEN_ERROR;
            firstName.classList.add("input-invalid");
            return;
          } else {
            errMsgFirstName.textContent = "";
            firstName.classList.remove("input-invalid");
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

    // password_confirm
    passwordConfirm.addEventListener(
      "keyup",
      (e) => {
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

    // buttonのdisabled制御関数

    // submit
    btn.addEventListener(
      "click",
      (e) => {
        e.preventDefault();
        form.method = "post";
        form.action = "./register.php";
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
