<?php

use App\Utils\Common;

define('ERROR_PAGE_URL', Common::getUrl('error/error.php'));

define('SUCCESS_MSG_DISPLAY_URL_FOR_GUEST', Common::getUrl('login/login.php'));
define('SUCCESS_MSG_DISPLAY_URL_FOR_AUTH', Common::getUrl('todo/top.php'));

define('SIGNUP_PAGE_URL', Common::getUrl('register/signup_form.php'));
define('LOGIN_PAGE_URL', Common::getUrl('login/login_form.php'));

define('TOP_PAGE_URL', Common::getUrl('todo/top.php'));
