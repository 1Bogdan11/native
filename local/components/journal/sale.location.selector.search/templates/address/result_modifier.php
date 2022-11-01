<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Sale\Location\LocationTable;

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

$arResult['INPUT_VALUE'] = 0;
$arResult['INPUT_VALUE_DISPLAY'] = '';

if (strlen($arParams['INPUT_VALUE']) > 0) {
    $resLocation = LocationTable::getList([
        'filter' => [
            '=CODE' => htmlspecialchars($arParams['INPUT_VALUE']),
            'NAME.LANGUAGE_ID' => LANGUAGE_ID,
        ],
        'select' => [
            'ID', 'CODE', 'LOCATION_NAME' => 'NAME.NAME'
        ]
    ]);
    $arLocation = $resLocation->fetch();

    if ($arLocation) {
        $arResult['INPUT_VALUE'] = $arLocation['ID'];
        $arResult['INPUT_VALUE_DISPLAY'] = $arLocation['LOCATION_NAME'];
    }
}
