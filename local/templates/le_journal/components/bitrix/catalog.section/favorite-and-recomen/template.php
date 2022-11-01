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

if (empty($arResult['ITEMS'])) {
    return;
}

?>
<ul class="basket__list">
    <?php
    foreach ($arResult['ITEMS'] as $arItem) {
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $editParams);
        ?>
        <li class="basket__list_item">
            <? $APPLICATION->IncludeComponent(
                'journal:catalog.item',
                'recomen',
                [
                    'ITEM' => $arItem,
                    'AREA_ID' => $this->GetEditAreaId($arItem['ID']),
                ],
                $component,
                ['HIDE_ICONS' => 'Y']
            ); ?>
        </li>
    <?
    }
    ?>
</ul>
