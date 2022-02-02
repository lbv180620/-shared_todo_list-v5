'use strict';

{
    document.addEventListener(
        "DOMContentLoaded",
        () => {
            /** Element取得 */

            // form
            const form = document.querySelector("#form");

            // form element
            const itemName = document.querySelector("#item_name");
            const staff = document.querySelector("#staff");
            const content = document.querySelector("#content");
            const expirationDate = document.querySelector("#expiration_date");

            // error message
            const errMsgItemName = document.querySelector(".err-msg-item_name");
            const errMsgStaff = document.querySelector(".err-msg-staff");
            const errMsgContent = document.querySelector(".err-msg-content");
            const errMsgExpirationDate = document.querySelector(
                ".err-msg-expiration_date"
            );

            // button
            const btn = document.querySelector("#btn");

            // 初期状態設定
            // btn.disabled = true;

            /** event */
            // item_name
            itemName.addEventListener(
                "keyup",
                (e) => {
                    if (!itemName.value) {
                        errMsgItemName.classList.add("form-invalid");
                        errMsgItemName.textContent = js_array.MSG_ITEM_NAME_ERROR;
                        itemName.classList.add("input-invalid");
                        return;
                    } else {
                        if (itemName.value.length > 100) {
                            errMsgItemName.classList.add("form-invalid");
                            errMsgItemName.textContent = js_array.MSG_ITEM_NAME_STRLEN_ERROR;
                            itemName.classList.add("input-invalid");
                            return;
                        } else {
                            errMsgItemName.textContent = "";
                            itemName.classList.remove("input-invalid");
                        }
                    }
                },
                false
            );

            // staff
            staff.addEventListener(
                "change",
                (e) => {
                    if (staff.options[0].selected === true) {
                        errMsgStaff.classList.add("form-invalid");
                        errMsgStaff.textContent = js_array.MSG_STAFF_ID_ERROR;
                        staff.classList.add("input-invalid");
                        return;
                    } else {
                        errMsgStaff.textContent = "";
                        staff.classList.remove("input-invalid");
                    }
                },
                false
            );

            staff.addEventListener(
                "change",
                (e) => {
                    if (staff.options[0].selected === false) {
                        errMsgStaff.textContent = "";
                        staff.classList.remove("input-invalid");
                    } else {
                        errMsgStaff.classList.add("form-invalid");
                        errMsgStaff.textContent = js_array.MSG_STAFF_ID_ERROR;
                        staff.classList.add("input-invalid");
                        return;
                    }
                },
                false
            );

            // content
            content.addEventListener(
                "keyup",
                (e) => {
                    if (!content.value) {
                        errMsgContent.classList.add("form-invalid");
                        errMsgContent.textContent = js_array.MSG_CONTENT_ERROR;
                        content.classList.add("input-invalid");
                        return;
                    } else {
                        errMsgContent.textContent = "";
                        content.classList.remove("input-invalid");
                    }
                },
                false
            );

            // expiration_date
            expirationDate.addEventListener(
                "keyup",
                (e) => {
                    if (!expirationDate.value) {
                        errMsgExpirationDate.classList.add("form-invalid");
                        errMsgExpirationDate.textContent = js_array.MSG_EXPIRATION_DATE_ERROR;
                        expirationDate.classList.add("input-invalid");
                        return;
                    } else {
                        errMsgExpirationDate.textContent = "";
                        expirationDate.classList.remove("input-invalid");
                    }
                },
                false
            );

            expirationDate.addEventListener(
                "click",
                (e) => {
                    if (!expirationDate.value) {
                        errMsgExpirationDate.classList.add("form-invalid");
                        errMsgExpirationDate.textContent = js_array.MSG_EXPIRATION_DATE_ERROR;
                        expirationDate.classList.add("input-invalid");
                        return;
                    } else {
                        errMsgExpirationDate.textContent = "";
                        expirationDate.classList.remove("input-invalid");
                    }
                },
                false
            );

            expirationDate.addEventListener(
                "change",
                (e) => {
                    if (expirationDate.value) {
                        errMsgExpirationDate.textContent = "";
                        expirationDate.classList.remove("input-invalid");
                    } else {
                        errMsgExpirationDate.classList.add("form-invalid");
                        errMsgExpirationDate.textContent = js_array.MSG_EXPIRATION_DATE_ERROR;
                        expirationDate.classList.add("input-invalid");
                        return;
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
                    form.action = "./edit_action.php";
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
}
