<?php

use Journal\Favorite\Favorite;
use Its\Library\Iblock\Iblock;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Its\Maxma\Order\Discount;

if (!empty($_REQUEST['siteId'])) {
    define('SITE_ID', htmlspecialchars(strval($_REQUEST['siteId'])));
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

Loc::loadMessages(__FILE__);

/** @global CMain $APPLICATION */
/** @global CUser $USER */

?>
<section class="modal modal--basket-modal modal--right modal--bordered" data-modal="basket-modal"
         data-tabs-modal="data-tabs-modal">
    <button class="modal__overlay" type="button" data-modal-close="basket-modal"></button>
    <button class="modal__mobile-close"></button>
    <div class="modal__container">
        <div class="modal__content">
            <div class="aside-tabs">
                <div class="aside-tabs__links js-modal__tabs">
                    <button class="aside-tabs__link" data-modal-tab="basket">
                        <?=Loc::getMessage('LJ_BASKET_MODAL_BASKET')?>
                    </button>
                    <button class="aside-tabs__link" data-modal-tab="favorites">
                        <?=Loc::getMessage('LJ_BASKET_MODAL_FAVORITES')?>
                    </button>
                    <button class="aside-tabs__link media-max--tab" data-modal-tab="maybe-like">
                        <?=Loc::getMessage('LJ_BASKET_MODAL_RECOM')?>
                    </button>
                </div>
                <div class="aside-tabs__area js-modal__contents">
                    <div class="aside-tabs__content" data-modal-content="basket">
                        <?php
                        if (Loader::includeModule('its.maxma')) {
                            Discount::enableCalculation();
                        }
                        $APPLICATION->IncludeComponent(
                            'journal:sale.basket.basket',
                            '',
                            array(
                                'COLUMNS_LIST_EXT' => [],
                                'PATH_TO_ORDER' => '/order/',
                                'PATH_TO_CATALOG' => '/catalog/',
                                'HIDE_COUPON' => 'N',
                                'PRICE_VAT_SHOW_VALUE' => 'N',
                                'USE_PREPAYMENT' => 'N',
                                'QUANTITY_FLOAT' => 'N',
                                'CORRECT_RATIO' => 'N',
                                'AUTO_CALCULATION' => 'Y',
                                'SET_TITLE' => 'N',
                                'ACTION_VARIABLE' => 'basketAction',
                                'COMPATIBLE_MODE' => 'Y',
                                'USE_GIFTS' => 'N',
                                'DEFERRED_REFRESH' => 'N',
                            ),
                            false
                        );
                        if (Loader::includeModule('its.maxma')) {
                            Discount::disableCalculation();
                        }
                        ?>
                    </div>
                    <div class="aside-tabs__content" data-modal-content="favorites">
                        <div class="basket__content_inner basket__content_inner--favorites" id="jsFavoriteModalWrap">
                            <?php
                            $favorite = new Favorite(Iblock::getInstance()->get('catalog'));
                            $favoriteItems = $favorite->getItems();
                            $GLOBALS['FAVORITE_PAGE_FILTER'] = ['ID' => !empty($favoriteItems) ? $favoriteItems : false];
                            $APPLICATION->IncludeComponent(
                                'bitrix:catalog.section',
                                'favorite-and-recomen',
                                [
                                    'IBLOCK_ID' => Iblock::getInstance()->get('catalog'),
                                    'IBLOCK_TYPE' => '1c_catalog',
                                    'ELEMENT_SORT_FIELD' => 'ID',
                                    'ELEMENT_SORT_ORDER' => $favoriteItems,
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
                                false
                            );
                            ?>
                        </div>
                        <script>
                            document.addEventListener('custom_site_data_update', function () {
                                showPreloader(document.getElementById('jsFavoriteModalWrap'));
                                axios({
                                    url: <?=json_encode($APPLICATION->GetCurPageParam())?>,
                                    method: 'get',
                                    params: {},
                                    data: {},
                                    timeout: 0,
                                    responseType: 'text',
                                }).then(function (response) {
                                    let parser, html, wrap, responseWrap;
                                    parser = new DOMParser();
                                    html = parser.parseFromString(response.data, 'text/html');
                                    responseWrap = html.getElementById('jsFavoriteModalWrap');
                                    wrap = document.getElementById('jsFavoriteModalWrap');
                                    if (wrap && responseWrap) {
                                        wrap.innerHTML = responseWrap.innerHTML;
                                        document.dispatchEvent(new CustomEvent('custom_favorite_loaded'));
                                    }
                                    hidePreloader(wrap);
                                });
                            });
                        </script>
                    </div>
                    <div class="aside-tabs__content" data-modal-content="maybe-like">
                        <?php
                        $APPLICATION->IncludeComponent(
                            "bitrix:catalog.section",
                            "favorite-and-recomen",
                            array(
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
                                "CACHE_TIME" => "3600",
                                "CACHE_TYPE" => "N",
                                "COMPATIBLE_MODE" => "N",
                                "CONVERT_CURRENCY" => "Y",
                                "CURRENCY_ID" => "RUB",
                                "CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
                                "DETAIL_URL" => "",
                                "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                                "DISPLAY_BOTTOM_PAGER" => "N",
                                "DISPLAY_COMPARE" => "N",
                                "DISPLAY_TOP_PAGER" => "N",
                                "ELEMENT_SORT_FIELD" => "rand",
                                "ELEMENT_SORT_FIELD2" => "rand",
                                "ELEMENT_SORT_ORDER" => "rand",
                                "ELEMENT_SORT_ORDER2" => "rand",
                                "ENLARGE_PRODUCT" => "STRICT",
                                "FILTER_NAME" => "arrFilterRecom",
                                "HIDE_NOT_AVAILABLE" => "N",
                                "HIDE_NOT_AVAILABLE_OFFERS" => "N",
                                "IBLOCK_ID" => "1",
                                "IBLOCK_TYPE" => "1c_catalog",
                                "INCLUDE_SUBSECTIONS" => "Y",
                                "LABEL_PROP" => array(),
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
                                "OFFERS_FIELD_CODE" => array(
                                    0 => "",
                                    1 => "",
                                ),
                                "OFFERS_LIMIT" => "5",
                                "OFFERS_SORT_FIELD" => "sort",
                                "OFFERS_SORT_FIELD2" => "id",
                                "OFFERS_SORT_ORDER" => "asc",
                                "OFFERS_SORT_ORDER2" => "desc",
                                "OFFER_ADD_PICT_PROP" => "MORE_PHOTO",
                                "PAGER_BASE_LINK_ENABLE" => "N",
                                "PAGER_DESC_NUMBERING" => "N",
                                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                                "PAGER_SHOW_ALL" => "N",
                                "PAGER_SHOW_ALWAYS" => "N",
                                "PAGER_TEMPLATE" => ".default",
                                "PAGER_TITLE" => "Товары",
                                "PAGE_ELEMENT_COUNT" => "3",
                                "PARTIAL_PRODUCT_PROPERTIES" => "N",
                                "PRICE_CODE" => array(
                                    0 => "Цены продажи",
                                ),
                                "PRICE_VAT_INCLUDE" => "Y",
                                "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
                                "PRODUCT_DISPLAY_MODE" => "Y",
                                "PRODUCT_ID_VARIABLE" => "id",
                                "PRODUCT_PROPS_VARIABLE" => "prop",
                                "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                                "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'0','BIG_DATA':false},{'VARIANT':'0','BIG_DATA':false},{'VARIANT':'0','BIG_DATA':false}]",
                                "PRODUCT_SUBSCRIPTION" => "Y",
                                "RCM_PROD_ID" => $_REQUEST["RECOM_PRODUCT_ID"],
                                "RCM_TYPE" => "bestsell",
                                "SECTION_CODE" => "",
                                "SECTION_ID" => $_REQUEST["RECOM_SECTION_ID"],
                                "SECTION_ID_VARIABLE" => "RECOM_SECTION_ID",
                                "SECTION_URL" => "",
                                "SECTION_USER_FIELDS" => array(
                                    0 => "",
                                    1 => "",
                                ),
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
                                "COMPONENT_TEMPLATE" => "favorite-and-recomen"
                            ),
                            false
                        );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
\Bitrix\Main\Application::getInstance()->getManagedCache()::finalize();
