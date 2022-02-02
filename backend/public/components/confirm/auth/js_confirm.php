<?php

use App\Utils\Common;

?>

<script>
    // 'use strict';
    // 厳格モードにすると呼び出し元のファイルに以下の関数が定義されていないので引っかかる

    {
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
    }
</script>
