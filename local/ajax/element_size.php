<?php

use Bitrix\Main\Localization\Loc;
use Its\Library\Iblock\Iblock;

if (!empty($_REQUEST['siteId'])) {
    define('SITE_ID', htmlspecialchars(strval($_REQUEST['siteId'])));
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

/** @global CMain $APPLICATION */
Loc::loadMessages(__FILE__);

?>
<section class="modal modal--product-modal modal--right" data-modal="product-modal" data-tabs-modal="data-tabs-modal">
    <button class="modal__overlay" type="button" data-modal-close="product-modal">
        <button class="modal__mobile-close"></button>
    </button>
    <div class="modal__container">
        <div class="modal__content">
            <button class="modal__close" data-modal-close="product-modal" data-modal-state-remove="is-fullscreen"></button>
            <div class="aside-tabs">
                <div class="aside-tabs__links js-modal__tabs">
                    <?php $APPLICATION->ShowViewContent('ELEMENT_SIZE_TABLE_BUTTON')?>
                    <?php $APPLICATION->ShowViewContent('ELEMENT_SIZE_MODEL_BUTTON')?>
                </div>
                <div class="aside-tabs__area js-modal__contents">
                    <?php
                    $GLOBALS['ELEMENT_SIZE_TABLE_FILTER'] = ['ID' => intval($_REQUEST['table'])];
                    $APPLICATION->IncludeComponent(
                        'bitrix:news.list',
                        'size_table',
                        [
                            'IBLOCK_TYPE' => '',
                            'IBLOCK_ID' => Iblock::getInstance()->get('size_table'),
                            'NEWS_COUNT' => 50,
                            "SORT_BY1" => "SORT",
                            "SORT_ORDER1" => "ASC",
                            "SORT_BY2" => "ID",
                            "SORT_ORDER2" => "DESC",
                            "FILTER_NAME" => 'ELEMENT_SIZE_TABLE_FILTER',
                            'FIELD_CODE' => ['DETAIL_PICTURE'],
                            'PROPERTY_CODE' => ['FAKE'],
                            'CHECK_DATES' => 'Y',
                            'DETAIL_URL' => '',
                            'AJAX_MODE' => 'N',
                            'CACHE_TYPE' => 'A',
                            'CACHE_TIME' => '36000000',
                            'CACHE_FILTER' => 'N',
                            'CACHE_GROUPS' => 'Y',
                            'PREVIEW_TRUNCATE_LEN' => '',
                            'ACTIVE_DATE_FORMAT' => 'd.m.Y',
                            'SET_TITLE' => 'N',
                            'SET_BROWSER_TITLE' => 'N',
                            'SET_META_KEYWORDS' => 'N',
                            'SET_META_DESCRIPTION' => 'N',
                            'SET_LAST_MODIFIED' => 'N',
                            'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
                            'ADD_SECTIONS_CHAIN' => 'N',
                            'INCLUDE_SUBSECTIONS' => 'Y',
                            'STRICT_SECTION_CHECK' => 'N',
                            'PAGER_TEMPLATE' => '.default',
                            'DISPLAY_TOP_PAGER' => 'N',
                            'DISPLAY_BOTTOM_PAGER' => 'N',
                            'PAGER_DESC_NUMBERING' => 'N',
                            'SET_STATUS_404' => 'N',
                        ],
                        false,
                        ['HIDE_ICONS' => 'Y']
                    );
                    $GLOBALS['ELEMENT_SIZE_MODEL_FILTER'] = ['PROPERTY_ITEM' => intval($_REQUEST['id'])];
                    $APPLICATION->IncludeComponent(
                        'bitrix:news.list',
                        'size_in_model',
                        [
                            'IBLOCK_TYPE' => '',
                            'IBLOCK_ID' => Iblock::getInstance()->get('sizes'),
                            'NEWS_COUNT' => 50,
                            "SORT_BY1" => "SORT",
                            "SORT_ORDER1" => "ASC",
                            "SORT_BY2" => "ID",
                            "SORT_ORDER2" => "DESC",
                            "FILTER_NAME" => 'ELEMENT_SIZE_MODEL_FILTER',
                            'FIELD_CODE' => ['DETAIL_PICTURE'],
                            'PROPERTY_CODE' => ['FAKE'],
                            'CHECK_DATES' => 'Y',
                            'DETAIL_URL' => '',
                            'AJAX_MODE' => 'N',
                            'CACHE_TYPE' => 'A',
                            'CACHE_TIME' => '36000000',
                            'CACHE_FILTER' => 'N',
                            'CACHE_GROUPS' => 'Y',
                            'PREVIEW_TRUNCATE_LEN' => '',
                            'ACTIVE_DATE_FORMAT' => 'd.m.Y',
                            'SET_TITLE' => 'N',
                            'SET_BROWSER_TITLE' => 'N',
                            'SET_META_KEYWORDS' => 'N',
                            'SET_META_DESCRIPTION' => 'N',
                            'SET_LAST_MODIFIED' => 'N',
                            'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
                            'ADD_SECTIONS_CHAIN' => 'N',
                            'INCLUDE_SUBSECTIONS' => 'Y',
                            'STRICT_SECTION_CHECK' => 'N',
                            'PAGER_TEMPLATE' => '.default',
                            'DISPLAY_TOP_PAGER' => 'N',
                            'DISPLAY_BOTTOM_PAGER' => 'N',
                            'PAGER_DESC_NUMBERING' => 'N',
                            'SET_STATUS_404' => 'N',
                        ],
                        false,
                        ['HIDE_ICONS' => 'Y']
                    );
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
