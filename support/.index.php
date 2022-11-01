<?php

use Its\Library\Iblock\Iblock;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/404.php')) {
        require $_SERVER['DOCUMENT_ROOT'] . '/404.php';
    }
    die();
}

/** @global $APPLICATION */

$APPLICATION->AddViewContent('MAIN_ATTRIBUTE', 'id="js-scroll" data-direction="vertical"');

$APPLICATION->IncludeComponent(
    'bitrix:news',
    'support',
    array(
        'IBLOCK_TYPE' => 'content',
        'IBLOCK_ID' => Iblock::getInstance()->get('support'),
        'NEWS_COUNT' => 50,
        'USE_SEARCH' => 'N',
        'USE_RSS' => 'N',
        'USE_RATING' => 'N',
        'USE_CATEGORIES' => 'N',
        'USE_REVIEW' => 'N',
        'USE_FILTER' => 'N',
        'USE_SHARE' => 'N',
        'SORT_BY1' => 'SORT',
        'SORT_ORDER1' => 'ASC',
        'SORT_BY2' => 'ID',
        'SORT_ORDER2' => 'DESC',
        'CHECK_DATES' => 'Y',
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/support/',
        'AJAX_MODE' => 'N',
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => '36000000',
        'CACHE_FILTER' => 'N',
        'CACHE_GROUPS' => 'Y',
        'SET_LAST_MODIFIED' => 'Y',
        'SET_TITLE' => 'Y',
        'SET_BROWSER_TITLE' => 'Y',
        'SET_META_KEYWORDS' => 'Y',
        'SET_META_DESCRIPTION' => 'Y',
        'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
        'ADD_SECTIONS_CHAIN' => 'N',
        'ADD_ELEMENT_CHAIN' => 'Y',
        'USE_PERMISSIONS' => 'N',
        'STRICT_SECTION_CHECK' => 'N',
        'PREVIEW_TRUNCATE_LEN' => '140',
        'LIST_ACTIVE_DATE_FORMAT' => 'j F Y',
        'LIST_FIELD_CODE' => ['DETAIL_PICTURE'],
        'LIST_PROPERTY_CODE' => ['FAKE'],
        'META_KEYWORDS' => '',
        'META_DESCRIPTION' => '',
        'BROWSER_TITLE' => '',
        'DETAIL_SET_CANONICAL_URL' => 'N',
        'DETAIL_ACTIVE_DATE_FORMAT' => 'j F Y',
        'DETAIL_FIELD_CODE' => [],
        'DETAIL_PROPERTY_CODE' => ['FAKE'],
        'DETAIL_DISPLAY_TOP_PAGER' => 'N',
        'DETAIL_DISPLAY_BOTTOM_PAGER' => 'N',
        'PAGER_TEMPLATE' => '',
        'DISPLAY_TOP_PAGER' => 'N',
        'DISPLAY_BOTTOM_PAGER' => 'N',
        'PAGER_SHOW_ALWAYS' => 'N',
        'PAGER_DESC_NUMBERING' => 'N',
        'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
        'PAGER_SHOW_ALL' => 'N',
        'PAGER_BASE_LINK_ENABLE' => 'N',
        'SET_STATUS_404' => 'Y',
        'SHOW_404' => 'Y',
        'FILE_404' => '',
        'SEF_URL_TEMPLATES' => array(
            'news' => '',
            'section' => '',
            'detail' => '#ELEMENT_CODE#/',
        )
    ),
    true
);
