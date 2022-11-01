<?php

use Its\Library\Iblock\Iblock;
use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @global CMain $APPLICATION */
/** @global CUser $USER */
?>
<div class="modal__content">
    <div class="aside-tabs">
        <div class="aside-tabs__links js-modal__tabs">
            <a class="aside-tabs__link" href="<?=SITE_DIR?>catalog/" data-modal-tab="catalog">
                <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_CATALOG')?>
            </a>
            <a class="aside-tabs__link" href="<?=SITE_DIR?>catalog/collections/" data-modal-tab="collections">
                <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_COLLECTIONS')?>
            </a>
            <a class="aside-tabs__link" href="<?=SITE_DIR?>shops/" data-modal-tab="shops">
                <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_STORES')?>
            </a>
            <button type="button" class="t-button t-button--search" data-modal-open="search">
                <svg class="i-search"><use xlink:href="#i-search"></use></svg>
            </button>
        </div>
        <div class="aside-tabs__area js-modal__contents">
            <div class="aside-tabs__content" data-modal-content="catalog">
                <?php $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "catalog-intopmenu",
                    [
                        "ALLOW_MULTI_SELECT" => "N",
                        "CHILD_MENU_TYPE" => "",
                        "COMPONENT_TEMPLATE" => "catalog-intopmenu",
                        "DELAY" => "N",
                        "MAX_LEVEL" => "2",
                        "MENU_CACHE_GET_VARS" => [],
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_TYPE" => "Y",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_THEME" => "site",
                        "ROOT_MENU_TYPE" => "catalog",
                        "USE_EXT" => "Y"
                    ]
                )?>
                <div class="aside-tabs__item">
                    <a href="<?= SITE_DIR ?>catalog/">
                        <span><?=Loc::getMessage('HEADER_MAIN_MENU_ALL_PRODUCTS')?></span>
                    </a>
                </div>
            </div>
            <div class="aside-tabs__content" data-modal-content="collections">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:news.list',
                    'collections_side',
                    [
                        'IBLOCK_TYPE' => '',
                        'IBLOCK_ID' => Iblock::getInstance()->get('collections'),
                        'NEWS_COUNT' => '100',
                        'SORT_BY1' => 'SORT',
                        'SORT_ORDER1' => 'ASC',
                        'SORT_BY2' => 'NAME',
                        'SORT_ORDER2' => 'ASC',
                        'FILTER_NAME' => '',
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
                    true
                )?>
            </div>
            <div class="aside-tabs__content" data-modal-content="shops">
                <?php
                $GLOBALS['ASIDE_MENU_SHOPS'] = ['PROPERTY_VENDOR_VALUE' => 'Да'];
                $APPLICATION->IncludeComponent(
                    'bitrix:news.list',
                    'shops_aside',
                    [
                        'ACTIVE_DATE_FORMAT' => 'd.m.Y',
                        'ADD_SECTIONS_CHAIN' => 'Y',
                        'AJAX_MODE' => 'N',
                        'AJAX_OPTION_ADDITIONAL' => '',
                        'AJAX_OPTION_HISTORY' => 'N',
                        'AJAX_OPTION_JUMP' => 'N',
                        'AJAX_OPTION_STYLE' => 'Y',
                        'CACHE_FILTER' => 'N',
                        'CACHE_GROUPS' => 'Y',
                        'CACHE_TIME' => '36000000',
                        'CACHE_TYPE' => 'A',
                        'CHECK_DATES' => 'Y',
                        'DETAIL_URL' => '',
                        'DISPLAY_BOTTOM_PAGER' => 'Y',
                        'DISPLAY_DATE' => 'Y',
                        'DISPLAY_NAME' => 'Y',
                        'DISPLAY_PICTURE' => 'Y',
                        'DISPLAY_PREVIEW_TEXT' => 'Y',
                        'DISPLAY_TOP_PAGER' => 'N',
                        'FIELD_CODE' => ['DETAIL_PICTURE'],
                        'FILTER_NAME' => 'ASIDE_MENU_SHOPS',
                        'HIDE_LINK_WHEN_NO_DETAIL' => 'N',
                        'IBLOCK_ID' => Iblock::getInstance()->get('shops'),
                        'IBLOCK_TYPE' => 'content',
                        'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
                        'INCLUDE_SUBSECTIONS' => 'Y',
                        'MESSAGE_404' => '',
                        'NEWS_COUNT' => '20',
                        'PAGER_BASE_LINK_ENABLE' => 'N',
                        'PAGER_DESC_NUMBERING' => 'N',
                        'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
                        'PAGER_SHOW_ALL' => 'N',
                        'PAGER_SHOW_ALWAYS' => 'N',
                        'PAGER_TEMPLATE' => '.default',
                        'PAGER_TITLE' => 'Магазины',
                        'PARENT_SECTION' => '',
                        'PARENT_SECTION_CODE' => '',
                        'PREVIEW_TRUNCATE_LEN' => '',
                        'PROPERTY_CODE' => ['FAKE'],
                        'LIST_PROPERTY_CODE' => ['FAKE'],
                        'SET_BROWSER_TITLE' => 'N',
                        'SET_LAST_MODIFIED' => 'N',
                        'SET_META_DESCRIPTION' => 'N',
                        'SET_META_KEYWORDS' => 'N',
                        'SET_STATUS_404' => 'N',
                        'SET_TITLE' => 'N',
                        'SHOW_404' => 'N',
                        'SORT_BY1' => 'ACTIVE_FROM',
                        'SORT_BY2' => 'SORT',
                        'SORT_ORDER1' => 'DESC',
                        'SORT_ORDER2' => 'ASC',
                        'STRICT_SECTION_CHECK' => 'N',
                    ],
                    false,
                    ['HIDE_ICONS' => 'Y']
                );
                ?>
            </div>
        </div>
        <?php
        // $GLOBALS["menuProducts"] формируется в файле .catalog.menu_ext.php
        if ($GLOBALS["menuProducts"]) {
            $GLOBALS["arrFilterInTopMenu"] = ["ID" => $GLOBALS["menuProducts"]];
            $APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "intopmenu",
                [
                    "ACTION_VARIABLE" => "action",
                    "ADD_PICT_PROP" => "-",
                    "ADD_PROPERTIES_TO_BASKET" => "Y",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "ADD_TO_BASKET_ACTION" => "ADD",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_ADDITIONAL" => "",
                    "AJAX_OPTION_HISTORY" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "N",
                    "BACKGROUND_IMAGE" => "-",
                    "BASKET_URL" => "/personal/basket.php",
                    "BROWSER_TITLE" => "-",
                    "CACHE_FILTER" => "N",
                    "CACHE_GROUPS" => "Y",
                    "CACHE_TIME" => "36000000",
                    "CACHE_TYPE" => "A",
                    "COMPATIBLE_MODE" => "N",
                    "CONVERT_CURRENCY" => "N",
                    "CUSTOM_FILTER" => "",
                    "DETAIL_URL" => "",
                    "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                    "DISPLAY_BOTTOM_PAGER" => "N",
                    "DISPLAY_COMPARE" => "N",
                    "DISPLAY_TOP_PAGER" => "N",
                    "ELEMENT_SORT_FIELD" => "sort",
                    "ELEMENT_SORT_FIELD2" => "id",
                    "ELEMENT_SORT_ORDER" => "asc",
                    "ELEMENT_SORT_ORDER2" => "desc",
                    "ENLARGE_PRODUCT" => "STRICT",
                    "FILTER_NAME" => "arrFilterInTopMenu",
                    "HIDE_NOT_AVAILABLE" => "N",
                    "HIDE_NOT_AVAILABLE_OFFERS" => "N",
                    "IBLOCK_ID" => Iblock::getInstance()->get('catalog'),
                    "IBLOCK_TYPE" => "1c_catalog",
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "LABEL_PROP" => "",
                    "LAZY_LOAD" => "N",
                    "LINE_ELEMENT_COUNT" => "3",
                    "LOAD_ON_SCROLL" => "N",
                    "MESSAGE_404" => "",
                    "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                    "MESS_BTN_BUY" => "Купить",
                    "MESS_BTN_DETAIL" => "Подробнее",
                    "MESS_BTN_LAZY_LOAD" => "Показать ещё",
                    "MESS_BTN_SUBSCRIBE" => "Подписаться",
                    "MESS_NOT_AVAILABLE" => "Нет в наличии",
                    "META_DESCRIPTION" => "-",
                    "META_KEYWORDS" => "-",
                    "OFFERS_FIELD_CODE" => [
                        0 => "",
                        1 => "",
                    ],
                    "OFFERS_LIMIT" => "5",
                    "OFFERS_SORT_FIELD" => "sort",
                    "OFFERS_SORT_FIELD2" => "id",
                    "OFFERS_SORT_ORDER" => "asc",
                    "OFFERS_SORT_ORDER2" => "desc",
                    "PAGER_BASE_LINK_ENABLE" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "N",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_TEMPLATE" => ".default",
                    "PAGER_TITLE" => "Товары",
                    "PAGE_ELEMENT_COUNT" => "20",
                    "PARTIAL_PRODUCT_PROPERTIES" => "N",
                    "PRICE_CODE" => [
                        0 => "Цены продажи",
                    ],
                    "PRICE_VAT_INCLUDE" => "Y",
                    "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
                    "PRODUCT_DISPLAY_MODE" => "N",
                    "PRODUCT_ID_VARIABLE" => "id",
                    "PRODUCT_PROPS_VARIABLE" => "prop",
                    "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                    "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
                    "PRODUCT_SUBSCRIPTION" => "Y",
                    "PROPERTY_CODE_MOBILE" => "",
                    "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
                    "RCM_TYPE" => "personal",
                    "SECTION_CODE" => "",
                    "SECTION_ID" => $_REQUEST["SECTION_ID_INMENU"],
                    "SECTION_ID_VARIABLE" => "SECTION_ID",
                    "SECTION_URL" => "",
                    "SECTION_USER_FIELDS" => [
                        0 => "",
                        1 => "",
                    ],
                    "SEF_MODE" => "N",
                    "SET_BROWSER_TITLE" => "N",
                    "SET_LAST_MODIFIED" => "N",
                    "SET_META_DESCRIPTION" => "N",
                    "SET_META_KEYWORDS" => "N",
                    "SET_STATUS_404" => "N",
                    "SET_TITLE" => "N",
                    "SHOW_404" => "N",
                    "SHOW_ALL_WO_SECTION" => "N",
                    "SHOW_CLOSE_POPUP" => "N",
                    "SHOW_DISCOUNT_PERCENT" => "N",
                    "SHOW_FROM_SECTION" => "N",
                    "SHOW_MAX_QUANTITY" => "N",
                    "SHOW_OLD_PRICE" => "N",
                    "SHOW_PRICE_COUNT" => "1",
                    "SHOW_SLIDER" => "N",
                    "SLIDER_INTERVAL" => "3000",
                    "SLIDER_PROGRESS" => "N",
                    "TEMPLATE_THEME" => "blue",
                    "USE_ENHANCED_ECOMMERCE" => "N",
                    "USE_MAIN_ELEMENT_SECTION" => "N",
                    "USE_PRICE_COUNT" => "N",
                    "USE_PRODUCT_QUANTITY" => "N",
                ],
                false
            );
        }

        $APPLICATION->ShowViewContent('SHOPS_ASIDE_DETAIL');
        ?>
    </div>
</div>
