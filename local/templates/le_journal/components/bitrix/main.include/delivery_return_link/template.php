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

$this->setFrameMode(true);

if (empty($arResult['FILE'])) {
    return;
}

$link = file_get_contents($arResult['FILE']);

if (empty($link)) {
    return;
}

?>
<a class="t-link" href="<?=htmlspecialchars($link)?>">
    <?=Loc::getMessage('DELIVERY_AND_PAYMENT_MODAL_RETURN_LINK')?>
</a>
