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

?>
<div class="basket-modal__empty">
    <p class="t-h4 basket-modal__empty-title">
        <?=Loc::getMessage('LJ_SBB_EMPTY_BASKET_TITLE')?>
    </p>
    <p class="basket-modal__empty-text">
        <?= Loc::getMessage('LJ_SBB_EMPTY_BASKET_TEXT')?>
    </p>
    <a href="<?=SITE_DIR . 'catalog/'?>" class="btn btn--lines basket-modal__empty-button" type="button">
        <span class="btn__bg"></span>
        <span class="btn__text">
            <?=Loc::getMessage('LJ_SBB_EMPTY_BASKET_CATALOG')?>
        </span>
    </a>
</div>
