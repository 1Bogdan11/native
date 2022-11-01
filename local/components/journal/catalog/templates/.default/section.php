<?php

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Catalog\GroupTable;
use Its\Library\Asset\AssetManager;
use Bitrix\Iblock\Model\Section;

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

$APPLICATION->AddViewContent('MAIN_ATTRIBUTE', 'id="js-scroll-native" data-direction="vertical"');

try {
    Loader::includeModule('catalog');
    $resPrice = GroupTable::getList([
        'filter' => ['NAME' => $arParams['PRICE_CODE']],
        'select' => ['ID']
    ]);
    $arPrice = $resPrice->fetch();
} catch (\Throwable $e) {
    $arPrice = false;
}

$arOrderTypes = [
    'default' => [
        'name' => Loc::getMessage('CATALOG_SECTION_ORDER_DEFAULT'),
        'order' => $arParams['ELEMENT_SORT_FIELD'],
        'by' => $arParams['ELEMENT_SORT_ORDER'],
    ],
    'cheaper' => [
        'name' => Loc::getMessage('CATALOG_SECTION_ORDER_CHEAPER'),
        'order' => 'PROPERTY_FILTER_DISCOUNT_PRICE', //"catalog_PRICE_{$arPrice['ID']}",
        'by' => 'asc,nulls',
    ],
    'pricey' => [
        'name' => Loc::getMessage('CATALOG_SECTION_ORDER_PRICEY'),
        'order' => 'PROPERTY_FILTER_DISCOUNT_PRICE', //"catalog_PRICE_{$arPrice['ID']}",
        'by' => 'desc,nulls',
    ],
    'name' => [
        'name' => Loc::getMessage('CATALOG_SECTION_ORDER_NAME'),
        'order' => 'NAME',
        'by' => 'asc,nulls',
    ],
    'popular' => [
        'name' => Loc::getMessage('CATALOG_SECTION_ORDER_POPULAR'),
        'order' => 'SHOW_COUNTER',
        'by' => 'desc,nulls',
    ],
];

foreach ($arOrderTypes as $key => $arOrderType) {
    $arOrderTypes[$key]['link'] = $APPLICATION->GetCurPageParam("order={$key}", ['order', 'PAGEN_1'], false);
}

$request = Context::getCurrent()->getRequest();
$order = htmlspecialchars($request['order']);

if (!isset($arOrderTypes[$order])) {
    $order = $_SESSION['CATALOG_ORDER_TYPE'];
}

if (isset($arOrderTypes[$order])) {
    $arOrderTypes[$order]['selected'] = true;
    $selectedOrder = $arOrderTypes[$order]['order'];
    $selectedName = $arOrderTypes[$order]['name'];
    $selectedBy = $arOrderTypes[$order]['by'];
    $_SESSION['CATALOG_ORDER_TYPE'] = $order;
} else {
    $selectedName = reset($arOrderTypes)['name'];
    $selectedOrder = $arParams['ELEMENT_SORT_FIELD'];
    $selectedBy = $arParams['ELEMENT_SORT_ORDER'];
}

$arSection = Section::compileEntityByIblock(intval($arParams['IBLOCK_ID']))::GetList([
    'order' => ['ID' => 'DESC'],
    'filter' => [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        [
            'LOGIC' => 'OR',
            '=ID' => $arResult['VARIABLES']['SECTION_ID'],
            '=CODE' => $arResult['VARIABLES']['SECTION_CODE'],
        ]
    ],
    'select' => ['ID', 'NAME', 'DESCRIPTION', 'UF_DESCRIPTION', 'UF_HIDE_LABEL', 'UF_HIDE_NOT_AVAILABLE', 'DEPTH_LEVEL'],
])->fetch();

if ($arSection['UF_HIDE_LABEL'] && !defined('DISABLE_LABEL')) {
    $arUserFieldValue = \CUserFieldEnum::GetList([], ['ID' => $arSection['UF_HIDE_LABEL']])->Fetch();
    define('DISABLE_LABEL', $arUserFieldValue['XML_ID']);
}
if ($arSection['UF_HIDE_NOT_AVAILABLE']) {
    $arParams['HIDE_NOT_AVAILABLE'] = 'Y';
}

if ($arParams['USE_FILTER'] == 'Y') {
    $smartFilterTemplate = 'smart_filter';
    if (!empty($arResult['SMART_FILTER_CUSTOM_TEMPLATE'])) {
        $smartFilterTemplate = $arResult['SMART_FILTER_CUSTOM_TEMPLATE'];
    }

    $arFilterParams = [
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'SECTION_ID' => $arSection['ID'],
        'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
        'FILTER_NAME' => $arParams['FILTER_NAME'],
        'PRICE_CODE' => $arParams['~PRICE_CODE'],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
        'SAVE_IN_SESSION' => 'N',
        'XML_EXPORT' => 'N',
        'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
        'SEF_MODE' => $arParams['SEF_MODE'],
        'SEF_RULE' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES'][$smartFilterTemplate],
        'SMART_FILTER_PATH' => $arResult['VARIABLES']['SMART_FILTER_PATH'],
        'PAGER_PARAMS_NAME' => $arParams['PAGER_PARAMS_NAME'],
        'INSTANT_RELOAD' => $arParams['INSTANT_RELOAD'],
        'DISPLAY_ELEMENT_COUNT' => 'Y',
        'SHOW_ALL_WO_SECTION' => 'Y',
        'CATALOG_ORDER' => $arOrderTypes,
        'PREFILTER_NAME' => "pre_{$arParams['FILTER_NAME']}",
    ];

    if (!empty($arResult['SMART_FILTER_CUSTOM_PREFILTER'])) {
        $GLOBALS[$arFilterParams['PREFILTER_NAME']] = $arResult['SMART_FILTER_CUSTOM_PREFILTER'];
    }

    $this->SetViewTarget('MODAL_CATALOG_FILER');
    ?>
    <section class="modal modal--filters modal--right" data-modal="filters">
        <button class="modal__overlay" type="button" data-modal-close="filters">
            <button class="modal__mobile-close"></button>
        </button>
        <div class="modal__container">
            <?php
            $APPLICATION->IncludeComponent(
                'bitrix:catalog.smart.filter',
                '',
                $arFilterParams,
                $component,
                ['HIDE_ICONS' => 'Y']
            );
            ?>
        </div>
    </section>
    <?php
    $this->EndViewTarget();

    if (Loader::includeModule('sotbit.seometa')) {
        $APPLICATION->IncludeComponent(
            'sotbit:seo.meta',
            '.default',
            [
                'FILTER_NAME' => $arParams['FILTER_NAME'],
                'SECTION_ID' => $arSection['ID'],
                'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                'CACHE_TIME' => $arParams['CACHE_TIME'],
            ]
        );
    }
}

if ($arResult['DISABLE_SECTIONS'] !== 'Y' && intval($arSection['DEPTH_LEVEL']) < 2) {
    $arParentSection = [];
    if ($arSection) {
        $arParentSection = \CIBlockSection::GetNavChain($arParams['IBLOCK_ID'], $arSection['ID'])->Fetch();
    }
    $arSectionListParams = [
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
        'TOP_DEPTH' => $arParams['SECTION_TOP_DEPTH'],
        'SECTION_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['section'],
        'ADD_SECTIONS_CHAIN' => 'N',
        'COUNT_ELEMENTS' => 'Y',
        'SECTION_ID' => 0,
        'SECTION_CODE' => '',
        'SECTION_FIELDS' => [],
        'SECTION_USER_FIELDS' => [],

        'FOLDER' => $arResult['FOLDER'],
        'CURRENT_SECTION_ID' => $arParentSection['ID'],
        'CURRENT_SECTION_CODE' => $arParentSection['CODE'],
    ];
    if ($arParams['HIDE_NOT_AVAILABLE'] == 'Y') {
        $sectionListParams['COUNT_ELEMENTS_FILTER'] = 'CNT_AVAILABLE';
    }

    $this->SetViewTarget('MODAL_CATALOG_CATEGORIES');
    ?>
    <section class="modal modal--categories modal--center modal--beige" data-modal="categories">
        <button class="modal__overlay" type="button" data-modal-close="categories">
            <button class="modal__mobile-close"></button>
        </button>
        <div class="modal__container">
            <?php $APPLICATION->IncludeComponent(
                'bitrix:catalog.section.list',
                'modal',
                $arSectionListParams,
                $component,
                ['HIDE_ICONS' => 'Y']
            )?>
        </div>
    </section>
    <?php
    $this->EndViewTarget();
}

$arSectionParams = [
    'DISABLE_LABEL_CACHE' => DISABLE_LABEL,
    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],

    'ELEMENT_SORT_FIELD' => $selectedOrder,
    'ELEMENT_SORT_ORDER' => $selectedBy,
    'ELEMENT_SORT_FIELD2' => $arParams['ELEMENT_SORT_FIELD2'],
    'ELEMENT_SORT_ORDER2' => $arParams['ELEMENT_SORT_ORDER2'],

    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'CACHE_FILTER' => $arParams['CACHE_FILTER'],
    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],

    'DISPLAY_TOP_PAGER' => $arParams['DISPLAY_TOP_PAGER'],
    'DISPLAY_BOTTOM_PAGER' => $arParams['DISPLAY_BOTTOM_PAGER'],
    'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
    'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
    'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],

    'META_KEYWORDS' => $arParams['LIST_META_KEYWORDS'],
    'META_DESCRIPTION' => $arParams['LIST_META_DESCRIPTION'],
    'BROWSER_TITLE' => $arParams['LIST_BROWSER_TITLE'],
    'SET_LAST_MODIFIED' => $arParams['SET_LAST_MODIFIED'],
    'INCLUDE_SUBSECTIONS' => $arParams['INCLUDE_SUBSECTIONS'],
    'BASKET_URL' => $arParams['BASKET_URL'],
    'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
    'FILTER_NAME' => $arParams['FILTER_NAME'],

    'SET_TITLE' => $arParams['SET_TITLE'],
    'MESSAGE_404' => $arParams['~MESSAGE_404'],
    'SET_STATUS_404' => $arParams['SET_STATUS_404'],
    'SHOW_404' => $arParams['SHOW_404'],
    'FILE_404' => $arParams['FILE_404'],
    'PAGE_ELEMENT_COUNT' => $arParams['PAGE_ELEMENT_COUNT'],

    'PROPERTY_CODE' => $arParams['LIST_PROPERTY_CODE'] ?? [],
    'PRICE_CODE' => $arParams['~PRICE_CODE'],
    'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
    'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],
    'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
    'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
    'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'] ?? '',
    'PARTIAL_PRODUCT_PROPERTIES' => $arParams['PARTIAL_PRODUCT_PROPERTIES'] ?? '',
    'PRODUCT_PROPERTIES' => $arParams['PRODUCT_PROPERTIES'] ?? [],
    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
    'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
    'HIDE_NOT_AVAILABLE_OFFERS' => $arParams['HIDE_NOT_AVAILABLE_OFFERS'],
    'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
    'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],
    'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
    'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
    'LINE_ELEMENT_COUNT' => $arParams['LINE_ELEMENT_COUNT'],

    'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
    'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
    'SECTION_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['section'],
    'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['element'],
    'USE_MAIN_ELEMENT_SECTION' => $arParams['USE_MAIN_ELEMENT_SECTION'],

    'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'] ?? [],
    'OFFERS_FIELD_CODE' => $arParams['LIST_OFFERS_FIELD_CODE'],
    'OFFERS_PROPERTY_CODE' => $arParams['LIST_OFFERS_PROPERTY_CODE'] ?? [],
    'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
    'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
    'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
    'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],
    'OFFERS_LIMIT' => $arParams['LIST_OFFERS_LIMIT'] ?? 0,
    'ADD_SECTIONS_CHAIN' => $arParams['ADD_SECTIONS_CHAIN'],

    'DISPLAY_COMPARE' => 'N',
    'DISABLE_INIT_JS_IN_COMPONENT' => $arParams['DISABLE_INIT_JS_IN_COMPONENT'] ?? '',

    'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'] ?? [],
    'PRODUCT_DISPLAY_MODE' => 'Y',
    'SHOW_ALL_WO_SECTION' => 'Y',
];

?>
<div class="container section__wrapper">
    <div class="breadcrumbs breadcrumbs--full">
        <?php $APPLICATION->IncludeComponent(
            'bitrix:breadcrumb',
            '',
            [
                'PATH' => '',
                'SITE_ID' => SITE_ID,
                'START_FROM' => '0',
            ],
            false,
            ['HIDE_ICONS' => 'Y']
        )?>
    </div>
</div>
<div class="container section__wrapper">
    <section class="catalog-section">
        <?php $APPLICATION->ShowViewContent('CATALOG_SECTION_HEAD')?>
        <div class="catalog-section__head is-inview" data-scroll>
            <?php
            if ($arResult['DISABLE_SECTIONS_BUTTON'] !== 'Y' && intval($arSection['DEPTH_LEVEL']) < 2) {
                ?>
                <div class="catalog-section__head-row catalog-section__head-row--single">
                    <button class="catalog__categories-btn" data-modal-open="categories">
                        <h1>
                            <?php $APPLICATION->ShowViewContent('CATALOG_CATEGORIES_SELECTOR')?>
                        </h1>
                    </button>
                </div>
                <?php
            } elseif (intval($arSection['DEPTH_LEVEL']) > 1) {
                ?>
                <div class="catalog-section__head-row catalog-section__head-row--single">
                    <h1 class="catalog__min-title">
                        <?php $APPLICATION->ShowTitle(false)?>
                    </h1>
                </div>
                <?php
            }
            if (!empty($arSection['UF_DESCRIPTION']) && intval($request->get('PAGEN_1') ?: $request->get('PAGEN_2')) < 2) {
                ?>
                <div class="catalog-section__desc">
                    <?=$arSection['UF_DESCRIPTION']?>
                </div>
                <?php
            }
            if ($arResult['DISABLE_SECTIONS'] !== 'Y' && ($arResult['VARIABLES']['SECTION_ID'] || $arResult['VARIABLES']['SECTION_CODE'])) {
                $arSectionListParams['SECTION_ID'] = $arResult['VARIABLES']['SECTION_ID'];
                $arSectionListParams['SECTION_CODE'] = $arResult['VARIABLES']['SECTION_CODE'];
                $arSectionListParams['COUNT_ELEMENTS'] = 'N';
                $APPLICATION->IncludeComponent(
                    'bitrix:catalog.section.list',
                    'subsections',
                    $arSectionListParams,
                    $component,
                    ['HIDE_ICONS' => 'Y']
                );
            }
            ?>
            <div class="catalog-section__head-row catalog-section__head-row--double">
                <div class="catalog__sort">
                    <select class="select js-select select--overlay jsCatalogSectionOrder" data-select-overlay>
                        <option data-placeholder="true">
                            <?=Loc::getMessage('CATALOG_SECTION_ORDER')?>
                        </option>
                        <?php foreach ($arOrderTypes as $key => $arOrderType) :?>
                            <option <?=($arOrderType['selected'] ? 'selected' : null)?> value="<?=$arOrderType['link']?>">
                                <?=$arOrderType['name']?>
                            </option>
                        <?php endforeach?>
                    </select>
                </div>
                <div class="catalog-section__btn js-filters-btn">
                    <button data-modal-open="filters">
                        <?=Loc::getMessage('CATALOG_SECTION_FILTERS')?>
                    </button>
                    <div class="filter-status js-filter-reset">
                        <span class="filter-status__counter">0</span>
                        <div class="filter-status__close"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="jsCatalogSectionWrap">
            <?php
            $arSectionParams['CURRENT_FILTER'] = $GLOBALS[$arParams['FILTER_NAME']];
            $APPLICATION->IncludeComponent(
                'bitrix:catalog.section',
                '',
                $arSectionParams,
                $component,
                ['HIDE_ICONS' => 'Y']
            );

            if ($request['ajax_request'] === 'Y') {
                echo "<script data-skip-moving='true'>";
                foreach ($arOrderTypes as $key => $arOrderType) {
                    echo "if (document.getElementById('order_{$key}')) {
                            document.getElementById('order_{$key}').setAttribute('data-link', '{$arOrderType['link']}');
                          };";
                }
                echo "</script>";
            }
            ?>
        </div>

        <?php
        if (!empty($arSection['DESCRIPTION']) && intval($request->get('PAGEN_1') ?: $request->get('PAGEN_2')) < 2) {
            ?>
            <div class="catalog-section__article">
                <div class="article__content article__content--catalog" data-observe="fade-y" data-scroll>
                    <?=$arSection['DESCRIPTION']?>
                </div>
            </div>
            <?php
        }
        ?>

    </section>
    
    <?php
    $arOfferInfo = \CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
    if ($arOfferInfo) {
        $APPLICATION->IncludeComponent(
            'bitrix:catalog.products.viewed',
            'section',
            [
                'IBLOCK_MODE' => 'single',
                'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                'ELEMENT_SORT_FIELD' => $arParams['ELEMENT_SORT_FIELD'],
                'ELEMENT_SORT_ORDER' => $arParams['ELEMENT_SORT_ORDER'],
                'ELEMENT_SORT_FIELD2' => $arParams['ELEMENT_SORT_FIELD2'],
                'ELEMENT_SORT_ORDER2' => $arParams['ELEMENT_SORT_ORDER2'],
                'PROPERTY_CODE_' . $arParams['IBLOCK_ID'] => $arParams['LIST_PROPERTY_CODE'] ?? [],
                'PROPERTY_CODE_' . $arOfferInfo['IBLOCK_ID'] => $arParams['LIST_OFFERS_PROPERTY_CODE'] ?? [],
                'BASKET_URL' => $arParams['BASKET_URL'],
                'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
                'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
                'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
                'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                'CACHE_TIME' => $arParams['CACHE_TIME'],
                'CACHE_FILTER' => $arParams['CACHE_FILTER'],
                'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                'DISPLAY_COMPARE' => 'N',
                'PRICE_CODE' => $arParams['~PRICE_CODE'],
                'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
                'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],
                'PAGE_ELEMENT_COUNT' => 4,
                'SET_TITLE' => 'N',
                'SET_BROWSER_TITLE' => 'N',
                'SET_META_KEYWORDS' => 'N',
                'SET_META_DESCRIPTION' => 'N',
                'SET_LAST_MODIFIED' => 'N',
                'ADD_SECTIONS_CHAIN' => 'N',
                'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
                'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
                'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'] ?? '',
                'PARTIAL_PRODUCT_PROPERTIES' => $arParams['PARTIAL_PRODUCT_PROPERTIES'] ?? '',
                'CART_PROPERTIES_' . $arParams['IBLOCK_ID'] => $arParams['PRODUCT_PROPERTIES'] ?? [],
                'CART_PROPERTIES_' . $arOfferInfo['IBLOCK_ID'] => $arParams['OFFERS_CART_PROPERTIES'] ?? [],
                'SHOW_FROM_SECTION' => 'N',
                'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['element'],
                'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
                'HIDE_NOT_AVAILABLE_OFFERS' => $arParams['HIDE_NOT_AVAILABLE_OFFERS'],
                'OFFER_TREE_PROPS_' . $arOfferInfo['IBLOCK_ID'] => $arParams['OFFER_TREE_PROPS'] ?? [],
                'PRODUCT_DISPLAY_MODE' => 'Y',
                'SHOW_ALL_WO_SECTION' => 'Y',
            ],
            $component
        );
    }
    ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.section = new SectionPage({}, {
            wrapId: 'jsCatalogSectionWrap',
            orderSelectClass: 'jsCatalogSectionOrder',
            pageNavigationClass: 'jsPageNavigationWrap',
            showMoreClass: 'jsPageNavigationShowMoreButton',
            elementListClass: 'jsCatalogSectionElementListWrap',
        });
    });
</script>
<?php
AssetManager::getInstance()->addJs(SITE_TEMPLATE_PATH . '/section.js');

global $sotbitSeoMetaTitle;
global $sotbitSeoMetaKeywords;
global $sotbitSeoMetaDescription;
global $sotbitSeoMetaBreadcrumbTitle;
global $sotbitSeoMetaH1;

if (!empty($sotbitSeoMetaH1)) {
    $APPLICATION->SetTitle($sotbitSeoMetaH1);
}
if (!empty($sotbitSeoMetaTitle)) {
    $APPLICATION->SetPageProperty("title", $sotbitSeoMetaTitle);
}
if (!empty($sotbitSeoMetaKeywords)) {
    $APPLICATION->SetPageProperty("keywords", $sotbitSeoMetaKeywords);
}
if (!empty($sotbitSeoMetaDescription)) {
    $APPLICATION->SetPageProperty("description", $sotbitSeoMetaDescription);
}
if (!empty($sotbitSeoMetaBreadcrumbTitle)) {
    $APPLICATION->AddChainItem($sotbitSeoMetaBreadcrumbTitle);
}
