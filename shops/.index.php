<?php

use Its\Library\Iblock\Iblock;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/404.php')) {
        require $_SERVER['DOCUMENT_ROOT'] . '/404.php';
    }
    die();
}

/** @global $APPLICATION */
?>
<?php $APPLICATION->IncludeComponent(
    'bitrix:news',
    'shops',
    [
        'ADD_ELEMENT_CHAIN' => 'Y',
        'ADD_SECTIONS_CHAIN' => 'Y',
        'AJAX_MODE' => 'N',
        'BROWSER_TITLE' => '-',
        'CACHE_FILTER' => 'N',
        'CACHE_GROUPS' => 'Y',
        'CACHE_TIME' => '36000000',
        'CACHE_TYPE' => 'A',
        'CHECK_DATES' => 'Y',
        'DETAIL_ACTIVE_DATE_FORMAT' => 'd.m.Y',
        'DETAIL_DISPLAY_BOTTOM_PAGER' => 'N',
        'DETAIL_DISPLAY_TOP_PAGER' => 'N',
        'DETAIL_FIELD_CODE' => [],
        'DETAIL_PROPERTY_CODE' => ['FAKE'],
        'DETAIL_SET_CANONICAL_URL' => 'N',
        'DISPLAY_BOTTOM_PAGER' => 'Y',
        'DISPLAY_TOP_PAGER' => 'N',
        'HIDE_LINK_WHEN_NO_DETAIL' => 'N',
        'IBLOCK_ID' => Iblock::getInstance()->get('shops'),
        'IBLOCK_TYPE' => 'content',
        'INCLUDE_IBLOCK_INTO_CHAIN' => 'Y',
        'LIST_ACTIVE_DATE_FORMAT' => 'd.m.Y',
        'LIST_FIELD_CODE' => ['DETAIL_PICTURE'],
        'LIST_PROPERTY_CODE' => ['FAKE'],
        'MESSAGE_404' => '',
        'META_DESCRIPTION' => '-',
        'META_KEYWORDS' => '-',
        'NEWS_COUNT' => 100,
        'PAGER_BASE_LINK_ENABLE' => 'N',
        'PAGER_DESC_NUMBERING' => 'N',
        'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
        'PAGER_SHOW_ALL' => 'N',
        'PAGER_SHOW_ALWAYS' => 'N',
        'PAGER_TEMPLATE' => '.default',
        'PAGER_TITLE' => '',
        'PREVIEW_TRUNCATE_LEN' => '',
        'SEF_MODE' => 'Y',
        'SET_LAST_MODIFIED' => 'Y',
        'SET_STATUS_404' => 'N',
        'SET_TITLE' => 'Y',
        'SHOW_404' => 'Y',
        'SORT_BY1' => 'SORT',
        'SORT_ORDER1' => 'ASC',
        'SORT_BY2' => 'SORT',
        'SORT_ORDER2' => 'ASC',
        'STRICT_SECTION_CHECK' => 'N',
        'USE_CATEGORIES' => 'N',
        'USE_FILTER' => 'N',
        'USE_PERMISSIONS' => 'N',
        'USE_RATING' => 'N',
        'USE_RSS' => 'N',
        'USE_SEARCH' => 'N',
        'USE_SHARE' => 'N',
        'COMPONENT_TEMPLATE' => 'shops',
        'SEF_FOLDER' => '/shops/',
        'SEF_URL_TEMPLATES' => [
            'news' => '',
            'section' => '#SECTION_CODE#/',
            'detail' => '#SECTION_CODE#/#ELEMENT_CODE#/',
        ],
    ],
    false
)?>
