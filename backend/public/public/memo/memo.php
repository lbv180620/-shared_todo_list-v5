<?php

/** auth */

require_once dirname(__FILE__, 4) . '/vendor/autoload.php';

/** URL */
require_once dirname(__FILE__, 3) . '/App/Config/url_list.php';

/** DB操作関連で使用 */

use App\Models\Base;
use App\Models\Users;
use App\Models\TodoItems;

/** メッセージ関連で使用 */

use App\Config\Config;

use App\Utils\Common;
use App\Utils\Logger;
use App\Utils\SessionUtil;

// セッション開始
SessionUtil::sessionStart();

// ログインチェック
if (!Common::isAuthUser()) {
    header("Location: " . LOGIN_PAGE_URL, true, 301);
    exit;
}

// ログインユーザ情報取得
$login = isset($_SESSION['login']) ? $_SESSION['login'] : null;

?>

<?php

/**
 * headとヘッダー(ナビバー)部分
 *
 */

$title = "メモ";
$active = "show";
$search = "";
include_once dirname(__FILE__, 3) . '/components/head/auth/head.php';

?>

<?php

/**
 * フッター部分
 */

include_once dirname(__FILE__, 3) . '/components/foot/auth/foot.php';

?>
