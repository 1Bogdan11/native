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

Loc::loadLanguageFile(__DIR__ . '/template.php');

$details = [];
foreach ($arResult['ERRORS'] as &$arError) {
    $details[] = $arError['detail'];
    $arError = str_replace('#FIELD#', Loc::getMessage("REVIEWS_ADD_FIELD_{$arError['name']}"), $arError['message']);
}

echo json_encode([
    'status' => empty($arResult['ERRORS']),
    'message' => implode('<br>', $arResult['ERRORS']),
    'detail' => $details,
]);
