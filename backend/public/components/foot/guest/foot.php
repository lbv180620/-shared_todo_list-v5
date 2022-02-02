<?php

use App\Utils\Common;

?>

</div>

<script>
    'use strict';

    {
        function checkSubmit() {
            if (window.confirm('<?= Common::h($title) ?>しますか?')) {
                return true;
            } else {
                return false;
            }
        }
    }
</script>

<!-- JSのフォームバリデーション処理 -->
<?php
$php_array = $validation_list;
$json_array = json_encode($php_array);
?>
<script type="text/javascript">
    {
        const js_array = JSON.parse('<?= $json_array ?>');
    }
</script>
<script type="text/javascript" src="../views/js<?= Common::h($js_validation_path) ?>"></script>

<!-- 必要なJavascriptを読み込む -->
<script src="../views/js/jquery-3.4.1.min.js"></script>
<script src="../views/js/bootstrap.bundle.min.js"></script>

</body>

</html>
