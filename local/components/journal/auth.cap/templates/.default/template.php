<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

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

$this->setFrameMode(false);

?>
<div class="container section__wrapper not-found">
    <div class="not-found__head">
        <h1 class="star-title">
            <svg class="i-subtract"><use xlink:href="#i-subtract"></use></svg>
            <span>
                <?=Loc::getMessage('AUTH_CAP_TITLE')?>
            </span>
        </h1>
        <div class="not-found__head_description">
            <?=Loc::getMessage('AUTH_CAP_MESSAGE')?>
        </div>
    </div>
</div>
