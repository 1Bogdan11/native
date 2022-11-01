<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
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

$arResult['SECTIONS'] = [];
$resSections = \CIBlockSection::GetList(
    ['SORT' => 'ASC'],
    [
        'DEPTH_LEVEL' => 1,
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'ACTIVE' => 'Y',
    ],
    false
);
while ($arSection = $resSections->GetNext()) {
    $arResult['SECTIONS'][$arSection['ID']] = $arSection;
}
