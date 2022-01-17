<?php

use App\Utils\Common;

?>

<!-- JSのフォームバリデーション処理 -->
<?php
$php_array = $validation_list;
$json_array = json_encode($php_array);
?>
<script type="text/javascript">
    const js_array = JSON.parse('<?= $json_array ?>');
</script>
<script type="text/javascript" src="../views/js<?= Common::h($js_validation_path) ?>"></script>
