<?php

use Bitrix\Catalog\Product\PropertyCatalogFeature;
use Bitrix\Main\Loader;
use Its\Maxma\Order\Discount;

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

if (Loader::includeModule('its.maxma')) {
    $arResult['MAXMA'] = Discount::getLastResult();
}

$arResult['ITEMS'] = [];
$arResult['GA4_ITEMS'] = [];

foreach ($arResult['GRID']['ROWS'] as $arItem) {
    if ($arItem['CAN_BUY'] !== 'Y') {
        continue;
    }
    $arItem['REAL_PRODUCT_ID'] = $arItem['PRODUCT_ID'];
    unset($arItem['PROPS'], $arItem['PROPS_ALL'], $arItem['SKU_DATA']);
    $productInfo = \CCatalogSku::GetProductInfo($arItem['PRODUCT_ID']);

    $arResult['GA4_ITEMS'][] = \Journal\Analytics::getGa4Item(
        intval($arItem['PRODUCT_ID']),
        intval($arItem['QUANTITY']),
        null,
        floatval($arItem['PRICE']),
        floatval($arItem['DISCOUNT_PRICE']),
    );

    if (empty($productInfo)) {
        $resElements = CIBlockElement::GetList(
            ['ID' => 'ASC'],
            ['ID' => $arItem['PRODUCT_ID']],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'DETAIL_PICTURE']
        );

        if ($objElement = $resElements->GetNextElement()) {
            $arElement = $objElement->GetFields();
            $arElement['PROPERTIES'] = $objElement->GetProperties();
            unset($arElement['ID']);
            $arItem = array_merge($arItem, $arElement);
        }
    } else {
        $resParentElements = CIBlockElement::GetList(
            ['ID' => 'ASC'],
            ['ID' => $productInfo['ID'], 'IBLOCK_ID' => $productInfo['IBLOCK_ID']],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'DETAIL_PICTURE']
        );

        if ($objParentElement = $resParentElements->GetNextElement()) {
            $arParent = $objParentElement->GetFields();
            $arParent['PROPERTIES'] = $objParentElement->GetProperties();
            $arParent['PRODUCT_ID'] = $arParent['ID'];
            unset($arParent['ID']);

            $arParent['OFFER_SELECTED_ID'] = $arItem['PRODUCT_ID'];
            $arOfferPropertyCodes = PropertyCatalogFeature::getOfferTreePropertyCodes(
                $productInfo['OFFER_IBLOCK_ID'],
                ['CODE' => 'Y']
            );
            if (is_array($arOfferPropertyCodes)) {
                $arOfferPropertyCodes = array_merge(['MORE_PHOTO'], $arOfferPropertyCodes);
            } else {
                $arOfferPropertyCodes = false;
            }
            $arParent['OFFERS'] = \CCatalogSKU::getOffersList(
                [$arParent['PRODUCT_ID']],
                0,
                ['ACTIVE' => 'Y'],
                ['ID', 'IBLOCK_ID', 'NAME', 'CATALOG_AVAILABLE', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'],
                ['CODE' => $arOfferPropertyCodes]
            )[$arParent['PRODUCT_ID']];
            if ($arParent['OFFERS'][$arParent['OFFER_SELECTED_ID']]) {
                $arItem = array_merge($arItem, $arParent);
            }
        }
    }

    $arResult['ITEMS'][] = $arItem;
}

unset($arResult['GRID']);
unset($arResult['CURRENCIES']);
