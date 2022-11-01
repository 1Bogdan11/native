<?php

use Bitrix\Sale\Location\LocationTable;
use Bitrix\Main\Loader;
use Its\Maxma\Order\Discount;
use Bitrix\Sale\Order;
use Bitrix\Sale\BasketItem;
use Its\Maxma\Api\Maxma;

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
    $arResult['MAXMA_USER'] = Maxma::getInstance()->getUser(intval($USER->GetID()))->getData();
}

$arResult['BASKET'] = [];
$arResult['GA4_ITEMS'] = [];

foreach ($arResult['GRID']['ROWS'] as $arItem) {
    $arItem = $arItem['data'];
    $arResult['BASKET'][] = $arItem;

    $arResult['GA4_ITEMS'][] = \Journal\Analytics::getGa4Item(
        intval($arItem['PRODUCT_ID']),
        intval($arItem['QUANTITY']),
        null,
        floatval($arItem['PRICE']),
        floatval($arItem['DISCOUNT_PRICE']),
    );
}
unset($arResult['GRID']);

if (intval($arResult['ORDER']['ID']) > 0) {
    $order = Order::load($arResult['ORDER']['ID']);
    $basket = $order->getBasket();
    foreach ($basket->getBasketItems() as $basketItem) {
        /** @var BasketItem $basketItem */
        $arResult['GA4_ITEMS'][] = \Journal\Analytics::getGa4Item(
            $basketItem->getProductId(),
            $basketItem->getQuantity(),
            null,
            $basketItem->getPrice(),
            $basketItem->getBasePrice() - $basketItem->getPrice(),
        );
    }
}

\CBitrixComponent::includeComponentClass('journal:user.address');
$arResult['USER_FIELDS'] = \UserAddressComponent::getUserFields();

$arResult['ORDER_PROPS'] = [];
foreach (['RELATED', 'USER_PROPS_Y', 'USER_PROPS_N'] as $key) {
    foreach ($arResult['ORDER_PROP'][$key] as $arProperty) {
        /* Установка значений из профиля */
        if ($arProperty['CODE'] === 'LOCATION' && empty($_REQUEST[$arProperty['FIELD_NAME']]) && !empty($arResult['USER_FIELDS']['UF_CITY'])) {
            $arLocation = LocationTable::getList([
                'filter' => [
                    '=CODE' => htmlspecialchars($arResult['USER_FIELDS']['UF_CITY']),
                    'NAME.LANGUAGE_ID' => LANGUAGE_ID,
                ],
                'select' => ['ID']
            ])->fetch();
            $arProperty['VALUE'] = $arLocation['ID'];
        } elseif ($arProperty['CODE'] === 'ADDRESS' && empty($arProperty['VALUE']) && !empty($arResult['USER_FIELDS']['UF_ADDRESS'])) {
            $arProperty['VALUE'] = $arResult['USER_FIELDS']['UF_ADDRESS'];
        }

        $arResult['ORDER_PROPS'][$arProperty['GROUP_NAME']][$arProperty['ID']] = $arProperty;
    }
}
foreach ($arResult['ORDER_PROPS'] as &$arProperties) {
    uasort($arProperties, function ($a, $b) {
        return $a['SORT'] <=> $b['SORT'];
    });
}
unset($arResult['ORDER_PROP']);

uasort($arResult['PAY_SYSTEM'], function ($a, $b) {
    return $a['SORT'] <=> $b['SORT'];
});

uasort($arResult['DELIVERY'], function ($a, $b) {
    return $a['SORT'] <=> $b['SORT'];
});
