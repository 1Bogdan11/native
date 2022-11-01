<?php

use Bitrix\Main\Context;

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
$request = Context::getCurrent()->getRequest();
$editParams = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
?>
<?php
foreach ($arResult['ITEMS'] as $arItem) { ?>
    <div class="aside-tabs__level aside-tabs__level--card" data-modal-level="product-<?= $arItem['ID'] ?>">
        <?
        $APPLICATION->IncludeComponent(
            'journal:catalog.item',
            'intopmenu',
            [
                'ITEM' => $arItem,
                'AREA_ID' => $this->GetEditAreaId($arItem['ID']),
            ],
            $component,
            ['HIDE_ICONS' => 'Y']
        );
        ?>
    </div>
<?
}

