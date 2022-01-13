<?php

/**
 * auth
 *
 * login_form.php
 * $message = '作業登録しますか?';
 * $js_validation_path = "/validate_entry_form.js";
 * $validation_list = Config::JS_TODO_FORM_VALIDATION_ERROR_MSG_LIST;
 *
 * edit_form.php
 * $message = '作業を修正しますか?';
 * $js_validation_path = "/validate_edit_form.js";
 * $validation_list = Config::JS_TODO_FORM_VALIDATION_ERROR_MSG_LIST;
 *
 */

use App\Utils\Common;

?>

</div>
<!-- コンテナ ここまで -->

<script>
    function checkSubmit() {
        if (window.confirm('<?= Common::h($message) ?>')) {
            return true;
        } else {
            return false;
        }
    }

    function checkLogout() {
        if (window.confirm('ログアウトしますか?')) {
            return true;
        } else {
            return false;
        }
    }
</script>

<!-- JSのフォームバリデーション処理 -->
<?php
$php_array = $validation_list;
$json_array = json_encode($php_array);
?>
<script type="text/javascript">
    const js_array = JSON.parse('<?= $json_array ?>');
</script>
<script type="text/javascript" src="../js<?= Common::h($js_validation_path) ?>"></script>

<!-- 必要なJavascriptを読み込む -->
<script src="../js/jquery-3.4.1.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>

</body>

</html>
