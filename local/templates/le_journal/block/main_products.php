<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @global CMain $APPLICATION */

$iblockHelper = Its\Library\Iblock\Iblock::getInstance();
$catalogIblockId = $iblockHelper->get('catalog');

$propLabel = Journal\Utils::loadEnumValues($catalogIblockId, 'LABEL');

?>
<?php
if ($propLabel) { ?>
    <section class="incomes-section">
        <div class="incomes__head-mobile media-min--tab">
            <select class="select js-select js-incomes-select" data-scroll data-observe="fade-y">
                <?
                foreach ($propLabel as $itemLabel) { ?>
                    <option value="<?= $itemLabel['VALUE']; ?>"><?= $itemLabel['VALUE']; ?></option>
                    <?
                } ?>
            </select>
        </div>
        <div class="incomes__head swiper-container media-max--tab js-incomes-slider" data-scroll data-observe="fade-y">
            <div class="incomes__tabs swiper-wrapper">
                <?
                foreach ($propLabel as $itemLabel) { ?>
                    <div class="incomes__tab swiper-slide"><span><?= $itemLabel['VALUE'] ?></span></div>
                    <?
                } ?>
            </div>
        </div>
        <div class="incomes__content js-incomes-tabs">
            <?php
            foreach ($propLabel as $i => $itemLabel) {
                $sDataScroll = '';
                if ($i == 0) {
                    $sDataScroll = 'data-scroll';
                }
                $GLOBALS['arrFilterMain'] = [
                    'PROPERTY_LABEL' => $itemLabel['ID'],
                ];

                $APPLICATION->IncludeComponent(
                    'bitrix:catalog.section',
                    'section-in-main',
                    [
                        'ACTION_VARIABLE' => 'action',
                        'ADD_PROPERTIES_TO_BASKET' => 'Y',
                        'ADD_SECTIONS_CHAIN' => 'N',
                        'BASKET_URL' => '/personal/basket.php',
                        'BROWSER_TITLE' => '-',
                        'CACHE_FILTER' => 'Y',
                        'CACHE_GROUPS' => 'N',
                        'CACHE_TIME' => '3600000',
                        'CACHE_TYPE' => 'A',
                        'CONVERT_CURRENCY' => 'N',
                        'DETAIL_URL' => '',
                        'DISABLE_INIT_JS_IN_COMPONENT' => 'N',
                        'DISPLAY_BOTTOM_PAGER' => 'Y',
                        'DISPLAY_COMPARE' => 'N',
                        'DISPLAY_TOP_PAGER' => 'N',
                        'ELEMENT_SORT_FIELD' => 'sort',
                        'ELEMENT_SORT_FIELD2' => 'shows',
                        'ELEMENT_SORT_ORDER' => 'asc',
                        'ELEMENT_SORT_ORDER2' => 'asc',
                        'FILTER_NAME' => 'arrFilterMain',
                        'HIDE_NOT_AVAILABLE' => 'N',
                        'HIDE_NOT_AVAILABLE_OFFERS' => 'N',
                        'IBLOCK_ID' => $catalogIblockId,
                        'IBLOCK_TYPE' => '1c_catalog',
                        'INCLUDE_SUBSECTIONS' => 'Y',
                        'MESSAGE_404' => '',
                        'META_DESCRIPTION' => '-',
                        'META_KEYWORDS' => '-',
                        'OFFERS_FIELD_CODE' => ['PREVIEW_TEXT', 'DETAIL_PICTURE', ''],
                        'OFFERS_LIMIT' => '5',
                        'OFFERS_SORT_FIELD' => 'shows',
                        'OFFERS_SORT_FIELD2' => 'shows',
                        'OFFERS_SORT_ORDER' => 'asc',
                        'OFFERS_SORT_ORDER2' => 'asc',
                        'PAGER_DESC_NUMBERING' => 'N',
                        'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
                        'PAGER_TEMPLATE' => '.default',
                        'PAGE_ELEMENT_COUNT' => '4',
                        'PARTIAL_PRODUCT_PROPERTIES' => 'N',
                        'PRICE_CODE' => ['Цены продажи'],
                        'PRICE_VAT_INCLUDE' => 'Y',
                        'PRODUCT_DISPLAY_MODE' => 'N',
                        'PRODUCT_ID_VARIABLE' => 'id',
                        'PRODUCT_PROPS_VARIABLE' => 'prop',
                        'PRODUCT_QUANTITY_VARIABLE' => 'quantity',
                        'SECTION_CODE' => '',
                        'SECTION_ID' => 0,
                        'SECTION_ID_VARIABLE' => 'SECTION_ID',
                        'SECTION_URL' => '',
                        'SET_LAST_MODIFIED' => 'N',
                        'SET_META_DESCRIPTION' => 'N',
                        'SET_META_KEYWORDS' => 'N',
                        'SET_STATUS_404' => 'N',
                        'SET_TITLE' => 'N',
                        'SHOW_404' => 'N',
                        'SHOW_ALL_WO_SECTION' => 'N',
                        'SHOW_PRICE_COUNT' => '1',
                        'USE_MAIN_ELEMENT_SECTION' => 'N',
                        'USE_PRICE_COUNT' => 'N',
                        'USE_PRODUCT_QUANTITY' => 'N',
                        'ADD_DATA_SCROLL' => $sDataScroll,
                    ]
                );
            }
            ?>
        </div>
    </section>
    <?php
} ?>
