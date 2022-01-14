<?php

use App\Utils\Common;

?>

<!-- サクセスメッセージアラート -->
<?php if (isset($success_msg)) : ?>
    <div class="row my-2">
        <div class="col-sm-3"></div>
        <div class="col-sm-6 alert alert-success alert-dismissble fade show">
            <button class="close" data-dismiss="alert">&times;</button>
            <p><?= Common::h($success_msg) ?></p>
            <?php if (Common::checkStringForLogout($success_msg)) : ?>
                <p><?= Config::DEFAULT_DELAY_TIME ?>秒後ログアウトします。</p>
                <?= Config::LOGOUT_SCRIPT ?>
            <?php endif ?>
        </div>
        <div class="col-sm-3"></div>
    </div>
<?php endif ?>
