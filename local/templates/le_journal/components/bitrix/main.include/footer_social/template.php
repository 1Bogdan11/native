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

$text = file_get_contents($arResult['FILE']);
$link = preg_replace('~[^a-zA-Z0-9?=&%:/#_.-]~', '', $text);

if (empty($link)) {
    return;
}

?>
<a class="t-link--m" href="<?=$link?>">
    <?php
    switch ($arParams['SOCIAL_TYPE']) {
        case 'INSTAGRAM':
            echo Loc::getMessage('FOOTER_SOCIAL_NAME_INSTAGRAM');
            break;

        case 'FACEBOOK':
            echo Loc::getMessage('FOOTER_SOCIAL_NAME_FACEBOOK');
            break;

        case 'YOUTUBE':
            echo Loc::getMessage('FOOTER_SOCIAL_NAME_YOUTUBE');
            break;

        case 'TELEGRAM':
            echo Loc::getMessage('FOOTER_SOCIAL_NAME_TELEGRAM');
            break;

        case 'WHATSAPP':
            echo Loc::getMessage('FOOTER_SOCIAL_NAME_WHATSAPP');
            break;

        case 'VK':
            echo Loc::getMessage('FOOTER_SOCIAL_NAME_VK');
            break;
    }
    ?>
</a>




