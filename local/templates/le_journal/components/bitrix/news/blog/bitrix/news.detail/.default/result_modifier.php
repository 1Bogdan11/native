<?php

use Bitrix\Iblock\Model\Section;

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

if ($arResult['IBLOCK_SECTION_ID']) {
    $resource = Section::compileEntityByIblock($arResult['IBLOCK_ID'])::getList([
        'order' => ['SORT' => 'ASC'],
        'filter' => [
            '=ID' => $arResult['IBLOCK_SECTION_ID'] ?: false,
            '=ACTIVE' => 'Y',
            '=GLOBAL_ACTIVE' => 'Y',
        ],
        'select' => [
            'ID',
            'NAME',
            'UF_IS_NEWS',
            'IBLOCK_SECTION_PAGE_URL' => 'IBLOCK.SECTION_PAGE_URL',
        ],
    ]);
    while ($sectionData = $resource->fetch()) {
        $arResult['ADD_DATA']['SECTION'][$sectionData['ID']] = $sectionData;
    }
}
