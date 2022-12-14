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
	'blog',
	[
        "ADD_ELEMENT_CHAIN" => "Y",
        "ADD_SECTIONS_CHAIN" => "Y",
		'AJAX_MODE' => 'N',
		'AJAX_OPTION_ADDITIONAL' => '',
		'AJAX_OPTION_HISTORY' => 'N',
		'AJAX_OPTION_JUMP' => 'N',
		'AJAX_OPTION_STYLE' => 'Y',
		'BROWSER_TITLE' => '-',
		'CACHE_FILTER' => 'N',
		'CACHE_GROUPS' => 'Y',
		'CACHE_TIME' => '36000000',
		'CACHE_TYPE' => 'A',
		'CHECK_DATES' => 'Y',
		'DETAIL_ACTIVE_DATE_FORMAT' => 'j F',
		'DETAIL_DISPLAY_BOTTOM_PAGER' => 'Y',
		'DETAIL_DISPLAY_TOP_PAGER' => 'N',
		'DETAIL_FIELD_CODE' => [
			'PREVIEW_TEXT',
			'PREVIEW_PICTURE',
		],
		'DETAIL_PAGER_SHOW_ALL' => 'Y',
		'DETAIL_PAGER_TEMPLATE' => '',
		'DETAIL_PAGER_TITLE' => 'Страница',
		'DETAIL_PROPERTY_CODE' => [
			'AUTHOR_TEXT',
			'AUTHOR_PHOTO',
		],
		'DETAIL_SET_CANONICAL_URL' => 'N',
		'DISPLAY_BOTTOM_PAGER' => 'Y',
		'DISPLAY_DATE' => 'Y',
		'DISPLAY_NAME' => 'Y',
		'DISPLAY_PICTURE' => 'Y',
		'DISPLAY_PREVIEW_TEXT' => 'Y',
		'DISPLAY_TOP_PAGER' => 'N',
		'HIDE_LINK_WHEN_NO_DETAIL' => 'N',
		'IBLOCK_ID' => Iblock::getInstance()->get('blog'),
		'IBLOCK_TYPE' => 'content',
		'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
		'LIST_ACTIVE_DATE_FORMAT' => 'j F',
		'LIST_FIELD_CODE' => [],
		'LIST_PROPERTY_CODE' => [],
		'MESSAGE_404' => '',
		'META_DESCRIPTION' => '-',
		'META_KEYWORDS' => '-',
		'NEWS_COUNT' => '9',
		'PAGER_BASE_LINK_ENABLE' => 'N',
		'PAGER_DESC_NUMBERING' => 'N',
		'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
		'PAGER_SHOW_ALL' => 'N',
		'PAGER_SHOW_ALWAYS' => 'N',
		'PAGER_TEMPLATE' => 'show_more_blog',
		'PAGER_TITLE' => 'Новости',
		'PREVIEW_TRUNCATE_LEN' => '',
		'SEF_MODE' => 'Y',
		'SET_LAST_MODIFIED' => 'N',
		'SET_STATUS_404' => 'N',
		'SET_TITLE' => 'Y',
		'SHOW_404' => 'N',
		'SORT_BY1' => 'ACTIVE_FROM',
		'SORT_BY2' => 'SORT',
		'SORT_ORDER1' => 'DESC',
		'SORT_ORDER2' => 'ASC',
		'STRICT_SECTION_CHECK' => 'N',
		'USE_CATEGORIES' => 'N',
		'USE_FILTER' => 'N',
		'USE_PERMISSIONS' => 'N',
		'USE_RATING' => 'N',
		'USE_RSS' => 'N',
		'USE_SEARCH' => 'N',
		'USE_SHARE' => 'N',
		'COMPONENT_TEMPLATE' => 'blog',
		'SEF_FOLDER' => '/en/blog/',
		'SHARE_HIDE' => 'N',
		'SHARE_TEMPLATE' => 'lejournal',
		'SHARE_HANDLERS' => [
			'vk',
			'facebook',
		],
		'SHARE_SHORTEN_URL_LOGIN' => '',
		'SHARE_SHORTEN_URL_KEY' => '',
		'SEF_URL_TEMPLATES' => [
			'news' => '',
			'section' => '#SECTION_CODE#/',
			'detail' => '#SECTION_CODE#/#ELEMENT_CODE#/',
		],
	],
	false
);
