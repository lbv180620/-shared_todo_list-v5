<?php

use App\Utils\Common;

?>

<!-- エラメッセージアラート -->
<?php if (isset($err_msg)) : ?>
    <div class="row my-2">
        <div class="col-sm-3"></div>
        <div class="col-sm-6 alert alert-danger alert-dismissble fade show">
            <button class="close" data-dismiss="alert">&times;</button>
            <?php foreach ($err_msg as $v) : ?>
                <p>・<?= Common::h($v) ?></p>
            <?php endforeach ?>
        </div>
        <div class="col-sm-3"></div>
    </div>
<?php endif ?>
