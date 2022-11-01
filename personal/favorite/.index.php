<?php

use Journal\Favorite\Favorite;
use Its\Library\Iblock\Iblock;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/404.php')) {
        require $_SERVER['DOCUMENT_ROOT'] . '/404.php';
    }
    die();
}

/** @global $APPLICATION */

$favorite = new Favorite(Iblock::getInstance()->get('catalog'));
$items = $favorite->getItems();
$GLOBALS['FAVORITE_PAGE_FILTER'] = ['ID' => !empty($items) ? $items : false];

?>
<div class="container profile js-toggle">
    <div class="profile__header">
        <div class="profile__header-left">
            <?php $APPLICATION->IncludeComponent(
                'journal:personal.user',
                '',
                [],
                true
            )?>
        </div>
        <div class="profile__header-right">
            <div class="profile__header-title-wrapper">
                <h5 class="profile__header-title">
                    <?php $APPLICATION->ShowTitle(false)?>
                    <?php $APPLICATION->ShowViewContent('PERSONAL_TITLE_COUNTER')?>
                </h5>
            </div>
            <?php $APPLICATION->ShowViewContent('PERSONAL_MENU_MOBILE')?>
        </div>
    </div>
    <div class="profile__main">
        <?php $APPLICATION->IncludeComponent(
            'its.agency:personal.section',
            '',
            [
                'SELECTED_TEMPLATE' => 'favorite',
                'ONLY_MENU' => 'Y',
                'PATH_TO_BASKET' => '',
                'PATH_TO_CATALOG' => '/catalog/',
                'ACTIVE_DATE_FORMAT' => 'd.m.Y',
                'SEF_MODE' => 'Y',
                'SEF_FOLDER' => '/personal/',
                'CACHE_TYPE' => 'A',
                'CACHE_TIME' => '86400',
            ],
            false
        )?>
        <div class="profile__content">
            <?php $APPLICATION->IncludeComponent(
                'bitrix:catalog.section',
                'favorite',
                [
                    'IBLOCK_ID' => Iblock::getInstance()->get('catalog'),
                    'IBLOCK_TYPE' => '1c_catalog',
                    'ELEMENT_SORT_FIELD' => 'ID',
                    'ELEMENT_SORT_ORDER' => $items,
                    'ELEMENT_SORT_FIELD2' => 'SORT',
                    'ELEMENT_SORT_ORDER2' => 'ASC',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => '86400',
                    'CACHE_FILTER' => 'Y',
                    'CACHE_GROUPS' => 'Y',
                    'DISPLAY_TOP_PAGER' => 'N',
                    'DISPLAY_BOTTOM_PAGER' => 'N',
                    'SET_LAST_MODIFIED' => 'N',
                    'INCLUDE_SUBSECTIONS' => 'Y',
                    'BASKET_URL' => '',
                    'ACTION_VARIABLE' => 'action',
                    'FILTER_NAME' => 'FAVORITE_PAGE_FILTER',
                    'SET_TITLE' => 'N',
                    'SET_STATUS_404' => 'N',
                    'SHOW_404' => 'N',
                    'PAGE_ELEMENT_COUNT' => 100,
                    'PROPERTY_CODE' => ['FAKE'],
                    'PRICE_CODE' => ['Цены продажи'],
                    'USE_PRICE_COUNT' => 'Y',
                    'SHOW_PRICE_COUNT' => '1',
                    'PRICE_VAT_INCLUDE' => 'N',
                    'USE_PRODUCT_QUANTITY' => 'Y',
                    'ADD_PROPERTIES_TO_BASKET' => 'N',
                    'PARTIAL_PRODUCT_PROPERTIES' => 'N',
                    'PRODUCT_PROPERTIES' => [],
                    'CONVERT_CURRENCY' => 'N',
                    'HIDE_NOT_AVAILABLE' => 'L',
                    'HIDE_NOT_AVAILABLE_OFFERS' => 'N',
                    'PRODUCT_ID_VARIABLE' => 'id',
                    'SECTION_ID_VARIABLE' => 'SECTION_ID',
                    'PRODUCT_QUANTITY_VARIABLE' => 'quantity',
                    'PRODUCT_PROPS_VARIABLE' => 'prop',
                    'SECTION_ID' => 0,
                    'SECTION_CODE' => '',
                    'SECTION_URL' => '',
                    'DETAIL_URL' => '',
                    'OFFERS_CART_PROPERTIES' => [],
                    'OFFERS_FIELD_CODE' => [],
                    'OFFERS_PROPERTY_CODE' => ['FAKE'],
                    'OFFERS_SORT_FIELD' => 'sort',
                    'OFFERS_SORT_ORDER' => 'asc',
                    'OFFERS_SORT_FIELD2' => 'id',
                    'OFFERS_SORT_ORDER2' => 'desc',
                    'OFFERS_LIMIT' => 0,
                    'ADD_SECTIONS_CHAIN' => 'N',
                    'DISPLAY_COMPARE' => 'N',
                    'DISABLE_INIT_JS_IN_COMPONENT' => 'N',
                    'OFFER_TREE_PROPS' => [],
                    'PRODUCT_DISPLAY_MODE' => 'Y',
                    'SHOW_ALL_WO_SECTION' => 'Y',
                ],
                false,
                ['HIDE_ICONS' => 'Y']
            )?>
        </div>
    </div>
</div>
