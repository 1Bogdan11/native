<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}


/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);
$request = Context::getCurrent()->getRequest();

if (!Loader::includeModule('search') || !Loader::includeModule('catalog')) {
    return;
}

$skuIblockInfo = \CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
$arElements = $APPLICATION->IncludeComponent(
    'bitrix:search.page',
    'empty',
    [
        'RESTART' => 'Y',
        'NO_WORD_LOGIC' => 'Y',
        'USE_LANGUAGE_GUESS' => 'N',
        'CHECK_DATES' => 'Y',
        'arrFILTER' => ["iblock_{$arParams['IBLOCK_TYPE']}"],
        "arrFILTER_iblock_{$arParams['IBLOCK_TYPE']}" => array_unique([$arParams['IBLOCK_ID'], $skuIblockInfo['IBLOCK_ID']]),
        'USE_TITLE_RANK' => 'N',
        'DEFAULT_SORT' => 'rank',
        'FILTER_NAME' => '',
        'SHOW_WHERE' => 'N',
        'arrWHERE' => [],
        'SHOW_WHEN' => 'N',
        'PAGE_RESULT_COUNT' => 50,
        'DISPLAY_TOP_PAGER' => 'N',
        'DISPLAY_BOTTOM_PAGER' => 'N',
        'PAGER_TITLE' => '',
        'PAGER_SHOW_ALWAYS' => 'N',
        'PAGER_TEMPLATE' => '',
    ],
    $component,
    ['HIDE_ICONS' => 'Y']
);

$arOffersInfo = \CCatalogSKU::getProductList(!empty($arElements) ? $arElements : []);
if (is_array($arElements) && is_array($arOffersInfo)) {
    $arElements = array_unique(array_merge(
        array_values($arElements),
        array_column($arOffersInfo, 'ID')
    ));
}

if ($request['ajax_result'] === 'Y') {
    ?>
    <div id="jsSearchResultWrap">
        <?php
        $GLOBALS['CATALOG_SEARCH_FILTER'] = [
            'ACTIVE' => 'Y',
            'ID' => !empty($arElements) ? $arElements : false,
            '!PROPERTY_OUTLET_ONLY_VALUE' => 'Да'
        ];
        $APPLICATION->IncludeComponent(
            'bitrix:catalog.section',
            'search',
            [
                'SEARCH_QUERY' => $request['q'],
                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                'ELEMENT_SORT_FIELD' => 'ID',
                'ELEMENT_SORT_ORDER' => $arElements,
                'ELEMENT_SORT_FIELD2' => $arParams['ELEMENT_SORT_FIELD2'],
                'ELEMENT_SORT_ORDER2' => $arParams['ELEMENT_SORT_ORDER2'],
                'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                'CACHE_TIME' => $arParams['CACHE_TIME'],
                'CACHE_FILTER' => $arParams['CACHE_FILTER'],
                'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                'DISPLAY_TOP_PAGER' => 'N',
                'DISPLAY_BOTTOM_PAGER' => 'N',
                'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
                'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
                'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
                'META_KEYWORDS' => $arParams['LIST_META_KEYWORDS'],
                'META_DESCRIPTION' => $arParams['LIST_META_DESCRIPTION'],
                'BROWSER_TITLE' => $arParams['LIST_BROWSER_TITLE'],
                'SET_LAST_MODIFIED' => 'N',
                'INCLUDE_SUBSECTIONS' => $arParams['INCLUDE_SUBSECTIONS'],
                'BASKET_URL' => $arParams['BASKET_URL'],
                'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
                'FILTER_NAME' => 'CATALOG_SEARCH_FILTER',
                'SET_TITLE' => 'N',
                'MESSAGE_404' => $arParams['~MESSAGE_404'],
                'SET_STATUS_404' => $arParams['SET_STATUS_404'],
                'SHOW_404' => $arParams['SHOW_404'],
                'FILE_404' => $arParams['FILE_404'],
                'PAGE_ELEMENT_COUNT' => 24,
                'PROPERTY_CODE' => $arParams['LIST_PROPERTY_CODE'] ?? [],
                'PRICE_CODE' => $arParams['~PRICE_CODE'],
                'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
                'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],
                'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
                'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
                'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'] ?? '',
                'PARTIAL_PRODUCT_PROPERTIES' => ($arParams['PARTIAL_PRODUCT_PROPERTIES'] ?? ''),
                'PRODUCT_PROPERTIES' => $arParams['PRODUCT_PROPERTIES'] ?? [],
                'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                'HIDE_NOT_AVAILABLE' => 'Y',
                'HIDE_NOT_AVAILABLE_OFFERS' => 'Y',
                'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
                'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],
                'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
                'LINE_ELEMENT_COUNT' => $arParams['LINE_ELEMENT_COUNT'],
                'SECTION_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['section'],
                'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['element'],
                'USE_MAIN_ELEMENT_SECTION' => $arParams['USE_MAIN_ELEMENT_SECTION'],
                'OFFERS_CART_PROPERTIES' => ($arParams['OFFERS_CART_PROPERTIES'] ?? []),
                'OFFERS_FIELD_CODE' => $arParams['LIST_OFFERS_FIELD_CODE'],
                'OFFERS_PROPERTY_CODE' => $arParams['LIST_OFFERS_PROPERTY_CODE'] ?? [],
                'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
                'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
                'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
                'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],
                'OFFERS_LIMIT' => $arParams['LIST_OFFERS_LIMIT'] ?? 0,
                'ADD_SECTIONS_CHAIN' => 'N',
                'DISPLAY_COMPARE' => 'N',
                'DISABLE_INIT_JS_IN_COMPONENT' => $arParams['DISABLE_INIT_JS_IN_COMPONENT'] ?? '',
                'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'] ?? [],
                'PRODUCT_DISPLAY_MODE' => 'Y',
                'SHOW_ALL_WO_SECTION' => 'Y',
            ],
            $component,
            ['HIDE_ICONS' => 'Y']
        );
        ?>
    </div>
    <?php
    return;
}

$this->SetViewTarget('CATALOG_SECTION_HEAD');
?>
    <div class="catalog-section__head">
        <div class="catalog-section__head-row catalog-section__head-row--single">
            <form class="search-form" action="<?=$arParams['SEF_FOLDER']?>">
                <div class="input">
                    <input type="text" name="q" required value="<?=htmlspecialchars($request->get('q'))?>">
                    <label class="input__label">
                        <?=Loc::getMessage('CATALOG_SEARCH_INPUT_TITLE')?>
                    </label>
                    <div class="input__bar"></div>
                </div>
                <div id="searchStatusWrap"></div>
                <button class="search-form__submit">
                    <svg class="i-search"><use xlink:href="#i-search"></use></svg>
                </button>
            </form>
        </div>
    </div>
<?php
$this->EndViewTarget();

$arResult['DISABLE_SECTIONS'] = 'Y';
$arResult['DISABLE_SECTIONS_BUTTON'] = 'Y';

$arResult['SMART_FILTER_CUSTOM_TEMPLATE'] = 'smart_filter_sections';
$arResult['SMART_FILTER_CUSTOM_PREFILTER'] = [
    'ACTIVE' => 'Y',
    'ID' => !empty($arElements) ? $arElements : false,
    '!PROPERTY_OUTLET_ONLY_VALUE' => 'Да'
];

require __DIR__ . '/section.php';

$APPLICATION->SetTitle(Loc::getMessage('CATALOG_SEARCH_TITLE'));
$APPLICATION->SetPageProperty('title', Loc::getMessage('CATALOG_SEARCH_TITLE'));
$APPLICATION->AddChainItem(
    Loc::getMessage('CATALOG_SEARCH_TITLE'),
    "{$arResult['FOLDER']}?q=" . htmlspecialchars($request->get('q'))
);
