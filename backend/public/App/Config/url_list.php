<?php

use App\Utils\Common;

define('ERROR_PAGE_URL', Common::getUrl('error/error.php'));

define('SUCCESS_MSG_DISPLAY_URL_FOR_GUEST', Common::getUrl('login/login.php'));
define('SUCCESS_MSG_DISPLAY_URL_FOR_AUTH', Common::getUrl('todo/top.php'));

define('SIGNUP_PAGE_URL', Common::getUrl('register/signup_form.php'));

define('LOGIN_PAGE_URL', Common::getUrl('login/login_form.php'));
define('LOGOUT_PAGE_URL', Common::getUrl('login/logout.php'));


define('TOP_PAGE_URL', Common::getUrl('todo/top.php'));
define('ENTRY_PAGE_URL', Common::getUrl('todo/entry.php'));
define('SHOW_PAGE_URL', Common::getUrl('todo/show.php'));
define('CANCEL_PAGE_URL', Common::getUrl('todo/cancel.php'));
define('DETAIL_PAGE_URL', Common::getUrl('todo/detail.php'));
define('EDIT_PAGE_URL', Common::getUrl('todo/edit.php'));
define('DELETE_PAGE_URL', Common::getUrl('todo/delete.php'));

define('MEMO_PAGE_URL', Common::getUrl('memo/memo.php'));
