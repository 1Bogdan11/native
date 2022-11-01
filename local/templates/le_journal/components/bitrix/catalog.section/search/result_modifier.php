<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

if (!empty($arResult['ITEMS'])) {
    $count    = count($arResult['ITEMS']);
    $products = ['товар', 'товара', 'товаров'];
    $num      = $count % 100;
    if ($num > 19) {
        $num = $num % 10;
    }
    $out = $count . ' ';
    switch ($num) {
        case 1:  $out .= $products[0]; break;
        case 2:
        case 3:
        case 4:  $out .= $products[1]; break;
        default: $out .= $products[2]; break;
    }
    $APPLICATION->AddViewContent('SEARCH_COUNT', $out);
}
