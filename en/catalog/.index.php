<?php

use Its\Library\Iblock\Iblock;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/404.php')) {
        require $_SERVER['DOCUMENT_ROOT'] . '/404.php';
    }
    die();
}

/** @global $APPLICATION */

$APPLICATION->IncludeComponent(
    'journal:catalog',
    '',
    [
        'IBLOCK_TYPE' => '1c_catalog',
        'IBLOCK_ID' => Iblock::getInstance()->get('catalog'),
        'HIDE_NOT_AVAILABLE' => 'L',
        'HIDE_NOT_AVAILABLE_OFFERS' => 'N',
        'USER_CONSENT' => 'N',
        'USER_CONSENT_ID' => '0',
        'USER_CONSENT_IS_CHECKED' => 'N',
        'USER_CONSENT_IS_LOADED' => 'N',
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/en/catalog/',
        'AJAX_MODE' => 'N',
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_STYLE' => 'N',
        'AJAX_OPTION_HISTORY' => 'N',
        'AJAX_OPTION_ADDITIONAL' => '',
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => '36000000',
        'CACHE_FILTER' => 'N',
        'CACHE_GROUPS' => 'Y',
        'USE_MAIN_ELEMENT_SECTION' => 'N',
        'DETAIL_STRICT_SECTION_CHECK' => 'N',
        'SET_LAST_MODIFIED' => 'Y',
        'SET_TITLE' => 'Y',
        'ADD_SECTIONS_CHAIN' => 'Y',
        'ADD_ELEMENT_CHAIN' => 'Y',
        'USE_FILTER' => 'Y',
        'FILTER_NAME' => '',
        'ACTION_VARIABLE' => 'action',
        'PRODUCT_ID_VARIABLE' => 'id',
        'USE_COMPARE' => 'N',
        'PRICE_CODE' => ['Цены продажи'],
        'USE_PRICE_COUNT' => 'N',
        'SHOW_PRICE_COUNT' => '1',
        'PRICE_VAT_INCLUDE' => 'Y',
        'PRICE_VAT_SHOW_VALUE' => 'N',
        'CONVERT_CURRENCY' => 'N',
        'BASKET_URL' => '',
        'USE_PRODUCT_QUANTITY' => 'N',
        'PRODUCT_QUANTITY_VARIABLE' => 'quantity',
        'ADD_PROPERTIES_TO_BASKET' => 'N',
        'PRODUCT_PROPS_VARIABLE' => 'prop',
        'PARTIAL_PRODUCT_PROPERTIES' => 'N',
        'SHOW_TOP_ELEMENTS' => 'N',
        'SECTION_COUNT_ELEMENTS' => 'Y',
        'SECTION_TOP_DEPTH' => '1',
        'PAGE_ELEMENT_COUNT' => '12',
        'LINE_ELEMENT_COUNT' => '3',
        'ELEMENT_SORT_FIELD' => 'sort',
        'ELEMENT_SORT_ORDER' => 'asc',
        'ELEMENT_SORT_FIELD2' => 'id',
        'ELEMENT_SORT_ORDER2' => 'desc',
        'INCLUDE_SUBSECTIONS' => 'Y',
        'LIST_META_KEYWORDS' => '-',
        'LIST_META_DESCRIPTION' => '-',
        'LIST_BROWSER_TITLE' => '-',
        'SECTION_BACKGROUND_IMAGE' => '-',
        'LIST_PROPERTY_CODE' => ['FAKE'],
        'LIST_OFFERS_FIELD_CODE' => ['FAKE'],
        'DETAIL_META_KEYWORDS' => '-',
        'DETAIL_META_DESCRIPTION' => '-',
        'DETAIL_BROWSER_TITLE' => '-',
        'DETAIL_SET_CANONICAL_URL' => 'N',
        'SECTION_ID_VARIABLE' => 'SECTION_ID',
        'DETAIL_CHECK_SECTION_ID_VARIABLE' => 'N',
        'DETAIL_BACKGROUND_IMAGE' => '-',
        'SHOW_DEACTIVATED' => 'N',
        'SHOW_SKU_DESCRIPTION' => 'N',
        'DETAIL_OFFERS_FIELD_CODE' => ['FAKE'],
        'LINK_IBLOCK_TYPE' => '',
        'LINK_IBLOCK_ID' => '',
        'LINK_PROPERTY_SID' => '',
        'LINK_ELEMENTS_URL' => '',
        'USE_ALSO_BUY' => 'N',
        'USE_GIFTS_DETAIL' => 'N',
        'USE_GIFTS_SECTION' => 'N',
        'USE_GIFTS_MAIN_PR_SECTION_LIST' => 'N',
        'USE_STORE' => 'N',
        'OFFERS_SORT_FIELD' => 'sort',
        'OFFERS_SORT_ORDER' => 'asc',
        'OFFERS_SORT_FIELD2' => 'id',
        'OFFERS_SORT_ORDER2' => 'desc',
        'PAGER_TEMPLATE' => 'show_more',
        'DISPLAY_TOP_PAGER' => 'N',
        'DISPLAY_BOTTOM_PAGER' => 'Y',
        'PAGER_TITLE' => 'Товары',
        'PAGER_SHOW_ALWAYS' => 'N',
        'PAGER_DESC_NUMBERING' => 'N',
        'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
        'PAGER_SHOW_ALL' => 'N',
        'PAGER_BASE_LINK_ENABLE' => 'N',
        'SET_STATUS_404' => 'Y',
        'SHOW_404' => 'Y',
        'FILE_404' => '',
        'COMPATIBLE_MODE' => 'Y',
        'USE_ELEMENT_COUNTER' => 'Y',
        'DISABLE_INIT_JS_IN_COMPONENT' => 'N',
        'DETAIL_SET_VIEWED_IN_COMPONENT' => 'Y',
        'STORES' => [],
        'USE_MIN_AMOUNT' => 'Y',
        'USER_FIELDS' => [],
        'FIELDS' => [],
        'MIN_AMOUNT' => '1',
        'SHOW_EMPTY_STORE' => 'Y',
        'SHOW_GENERAL_STORE_INFORMATION' => 'N',
        'STORE_PATH' => '',
        'MAIN_TITLE' => '',
        'SEF_URL_TEMPLATES' => [
            'sections' => '',
            'section' => '#SECTION_CODE_PATH#/',
            'element' => '#SECTION_CODE_PATH#/#ELEMENT_CODE#/',
            'compare' => '',
            'smart_filter' => '#SECTION_CODE_PATH#/filter/#SMART_FILTER_PATH#/apply/',
            'smart_filter_sections' => 'filter/#SMART_FILTER_PATH#/apply/',
        ],
    ],
    true
);