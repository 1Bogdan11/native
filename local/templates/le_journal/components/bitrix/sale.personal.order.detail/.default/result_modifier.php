<?php

use Bitrix\Catalog\Product\PropertyCatalogFeature;

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

foreach ($arResult['BASKET'] as &$arItem) {
    $productId = $arItem['PRODUCT_ID'];
    $productInfo = \CCatalogSku::GetProductInfo($productId);
    if (empty($productInfo)) {
        $resElements = CIBlockElement::GetList(
            ['ID' => 'ASC'],
            ['ID' => $productId],
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

            $arParent['PRODUCT_ID'] = $arParent['ID'];
            unset($arParent['ID']);

            $arParent['PROPERTIES'] = $objParentElement->GetProperties();
            $arParent['OFFER_SELECTED_ID'] = $productId;

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
                [],
                ['ID', 'IBLOCK_ID', 'NAME', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'],
                ['CODE' => $arOfferPropertyCodes]
            )[$arParent['PRODUCT_ID']];

            $arItem = array_merge($arItem, $arParent);
        }
    }
}
