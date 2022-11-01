<?php

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

if (strlen($arResult['ERROR_MESSAGE']) > 0) {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showMessage({
                title: <?=json_encode(Loc::getMessage('ORDER_CANCEL_ERROR'))?>,
                text: <?=json_encode($arResult['ERROR_MESSAGE'])?>,
                time: 10000,
            });
        });
    </script>
    <?php
    return;
}
?>


<div class="profile__content-title">
    <p><?=Loc::getMessage('ORDER_CANCEL_LABEL')?></p>
</div>
<form class="profile__cancel" method="post" action="<?=POST_FORM_ACTION_URI?>">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="CANCEL" value="Y">
    <input type="hidden" name="ID" value="<?=$arResult['ID']?>">

    <textarea class="profile__cancel_area" name="REASON_CANCELED" id="REASON_CANCELED"></textarea>

    <div class="profile__cancel_footer">
        <button class="button-black" name="action" value="CANCEL" type="submit">
            <?=Loc::getMessage('ORDER_CANCEL_BUTTON')?>
        </button>
        <a class="button-black" href="<?=$arResult['URL_TO_DETAIL']?>">
            <?=Loc::getMessage('ORDER_CANCEL_REJECT')?>
        </a>
    </div>
</form>
