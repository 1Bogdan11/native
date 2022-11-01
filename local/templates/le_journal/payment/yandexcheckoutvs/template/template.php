<?php

use Bitrix\Main\Localization\Loc;

if (defined('PAY_SYSTEM_TEMPLATE_PERSONAL') && PAY_SYSTEM_TEMPLATE_PERSONAL === 'Y') {
    ?>
    <a class="button-black" target="_blank" href="<?=htmlspecialchars($params['URL'])?>">
        <?=Loc::getMessage('YOU_MONEY_PAYMENT_BUTTON')?>
    </a>
    <?php
} else {
    ?>
    <div class="ordering-success__description">
        <?=Loc::getMessage(
            'YOU_MONEY_PAYMENT_REDIRECT_MESSAGE',
            ['#LINK#' => htmlspecialchars($params['URL'])]
        )?>
    </div>
    <script>
        setTimeout(function () {
            document.location.href = <?=json_encode($params['URL'])?>;
        }, 10000);
    </script>
    <?php
}
