<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Iblock\Model\Section;
use Bitrix\Iblock\ElementTable;
use Its\Library\Iblock\Iblock;

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

$APPLICATION->AddViewContent('HEADER_CLASSES', 'is-product');
$APPLICATION->AddViewContent('MAIN_ATTRIBUTE', 'class="is-product" id="js-scroll-native" data-direction="vertical"');

$APPLICATION->IncludeComponent(
    'bitrix:breadcrumb',
    'element',
    [
        'PATH' => '',
        'SITE_ID' => SITE_ID,
        'START_FROM' => '0',
    ],
    false,
    ['HIDE_ICONS' => 'Y']
);

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
    'select' => ['ID', 'NAME', 'DESCRIPTION', 'UF_DESCRIPTION', 'UF_HIDE_LABEL'],
])->fetch();

if ($arSection['UF_HIDE_LABEL'] && !defined('DISABLE_LABEL')) {
    $arUserFieldValue = \CUserFieldEnum::GetList([], ['ID' => $arSection['UF_HIDE_LABEL']])->Fetch();
    define('DISABLE_LABEL', $arUserFieldValue['XML_ID']);
}

?>
<div class="product js-product" itemscope itemtype="http://schema.org/Product">
<?php
    $componentElementParams = [
        'DISABLE_LABEL_CACHE' => DISABLE_LABEL,
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'PROPERTY_CODE' => (isset($arParams['DETAIL_PROPERTY_CODE']) ? $arParams['DETAIL_PROPERTY_CODE'] : []),
        'META_KEYWORDS' => $arParams['DETAIL_META_KEYWORDS'],
        'META_DESCRIPTION' => $arParams['DETAIL_META_DESCRIPTION'],
        'BROWSER_TITLE' => $arParams['DETAIL_BROWSER_TITLE'],
        'SET_CANONICAL_URL' => $arParams['DETAIL_SET_CANONICAL_URL'],
        'BASKET_URL' => $arParams['BASKET_URL'],
        'SHOW_SKU_DESCRIPTION' => $arParams['SHOW_SKU_DESCRIPTION'],
        'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
        'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
        'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],
        'CHECK_SECTION_ID_VARIABLE' => (isset($arParams['DETAIL_CHECK_SECTION_ID_VARIABLE']) ? $arParams['DETAIL_CHECK_SECTION_ID_VARIABLE'] : ''),
        'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
        'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
        'SET_TITLE' => $arParams['SET_TITLE'],
        'SET_LAST_MODIFIED' => $arParams['SET_LAST_MODIFIED'],
        'MESSAGE_404' => $arParams['~MESSAGE_404'],
        'SET_STATUS_404' => $arParams['SET_STATUS_404'],
        'SHOW_404' => $arParams['SHOW_404'],
        'FILE_404' => $arParams['FILE_404'],
        'PRICE_CODE' => $arParams['~PRICE_CODE'],
        'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
        'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],
        'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
        'PRICE_VAT_SHOW_VALUE' => $arParams['PRICE_VAT_SHOW_VALUE'],
        'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
        'PRODUCT_PROPERTIES' => (isset($arParams['PRODUCT_PROPERTIES']) ? $arParams['PRODUCT_PROPERTIES'] : []),
        'ADD_PROPERTIES_TO_BASKET' => (isset($arParams['ADD_PROPERTIES_TO_BASKET']) ? $arParams['ADD_PROPERTIES_TO_BASKET'] : ''),
        'PARTIAL_PRODUCT_PROPERTIES' => (isset($arParams['PARTIAL_PRODUCT_PROPERTIES']) ? $arParams['PARTIAL_PRODUCT_PROPERTIES'] : ''),
        'LINK_IBLOCK_TYPE' => $arParams['LINK_IBLOCK_TYPE'],
        'LINK_IBLOCK_ID' => $arParams['LINK_IBLOCK_ID'],
        'LINK_PROPERTY_SID' => $arParams['LINK_PROPERTY_SID'],
        'LINK_ELEMENTS_URL' => $arParams['LINK_ELEMENTS_URL'],

        'OFFERS_CART_PROPERTIES' => (isset($arParams['OFFERS_CART_PROPERTIES']) ? $arParams['OFFERS_CART_PROPERTIES'] : []),
        'OFFERS_FIELD_CODE' => $arParams['DETAIL_OFFERS_FIELD_CODE'],
        'OFFERS_PROPERTY_CODE' => (isset($arParams['DETAIL_OFFERS_PROPERTY_CODE']) ? $arParams['DETAIL_OFFERS_PROPERTY_CODE'] : []),
        'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
        'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
        'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
        'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],

        'ELEMENT_ID' => $arResult['VARIABLES']['ELEMENT_ID'],
        'ELEMENT_CODE' => $arResult['VARIABLES']['ELEMENT_CODE'],
        'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
        'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
        'SECTION_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['section'],
        'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['element'],
        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
        'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
        'HIDE_NOT_AVAILABLE_OFFERS' => $arParams['HIDE_NOT_AVAILABLE_OFFERS'],
        'USE_ELEMENT_COUNTER' => $arParams['USE_ELEMENT_COUNTER'],
        'SHOW_DEACTIVATED' => $arParams['SHOW_DEACTIVATED'],
        'USE_MAIN_ELEMENT_SECTION' => $arParams['USE_MAIN_ELEMENT_SECTION'],
        'STRICT_SECTION_CHECK' => (isset($arParams['DETAIL_STRICT_SECTION_CHECK']) ? $arParams['DETAIL_STRICT_SECTION_CHECK'] : ''),
        'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
        'LABEL_PROP' => $arParams['LABEL_PROP'],
        'LABEL_PROP_MOBILE' => $arParams['LABEL_PROP_MOBILE'],
        'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],
        'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
        'OFFER_TREE_PROPS' => (isset($arParams['OFFER_TREE_PROPS']) ? $arParams['OFFER_TREE_PROPS'] : []),
        'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
        'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
        'DISCOUNT_PERCENT_POSITION' => (isset($arParams['DISCOUNT_PERCENT_POSITION']) ? $arParams['DISCOUNT_PERCENT_POSITION'] : ''),
        'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
        'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
        'MESS_SHOW_MAX_QUANTITY' => (isset($arParams['~MESS_SHOW_MAX_QUANTITY']) ? $arParams['~MESS_SHOW_MAX_QUANTITY'] : ''),
        'RELATIVE_QUANTITY_FACTOR' => (isset($arParams['RELATIVE_QUANTITY_FACTOR']) ? $arParams['RELATIVE_QUANTITY_FACTOR'] : ''),
        'MESS_RELATIVE_QUANTITY_MANY' => (isset($arParams['~MESS_RELATIVE_QUANTITY_MANY']) ? $arParams['~MESS_RELATIVE_QUANTITY_MANY'] : ''),
        'MESS_RELATIVE_QUANTITY_FEW' => (isset($arParams['~MESS_RELATIVE_QUANTITY_FEW']) ? $arParams['~MESS_RELATIVE_QUANTITY_FEW'] : ''),
        'MESS_BTN_BUY' => (isset($arParams['~MESS_BTN_BUY']) ? $arParams['~MESS_BTN_BUY'] : ''),
        'MESS_BTN_ADD_TO_BASKET' => (isset($arParams['~MESS_BTN_ADD_TO_BASKET']) ? $arParams['~MESS_BTN_ADD_TO_BASKET'] : ''),
        'MESS_BTN_SUBSCRIBE' => (isset($arParams['~MESS_BTN_SUBSCRIBE']) ? $arParams['~MESS_BTN_SUBSCRIBE'] : ''),
        'MESS_BTN_DETAIL' => (isset($arParams['~MESS_BTN_DETAIL']) ? $arParams['~MESS_BTN_DETAIL'] : ''),
        'MESS_NOT_AVAILABLE' => (isset($arParams['~MESS_NOT_AVAILABLE']) ? $arParams['~MESS_NOT_AVAILABLE'] : ''),
        'MESS_BTN_COMPARE' => (isset($arParams['~MESS_BTN_COMPARE']) ? $arParams['~MESS_BTN_COMPARE'] : ''),
        'MESS_PRICE_RANGES_TITLE' => (isset($arParams['~MESS_PRICE_RANGES_TITLE']) ? $arParams['~MESS_PRICE_RANGES_TITLE'] : ''),
        'MESS_DESCRIPTION_TAB' => (isset($arParams['~MESS_DESCRIPTION_TAB']) ? $arParams['~MESS_DESCRIPTION_TAB'] : ''),
        'MESS_PROPERTIES_TAB' => (isset($arParams['~MESS_PROPERTIES_TAB']) ? $arParams['~MESS_PROPERTIES_TAB'] : ''),
        'MESS_COMMENTS_TAB' => (isset($arParams['~MESS_COMMENTS_TAB']) ? $arParams['~MESS_COMMENTS_TAB'] : ''),
        'MAIN_BLOCK_PROPERTY_CODE' => (isset($arParams['DETAIL_MAIN_BLOCK_PROPERTY_CODE']) ? $arParams['DETAIL_MAIN_BLOCK_PROPERTY_CODE'] : ''),
        'MAIN_BLOCK_OFFERS_PROPERTY_CODE' => (isset($arParams['DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE']) ? $arParams['DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE'] : ''),
        'USE_VOTE_RATING' => $arParams['DETAIL_USE_VOTE_RATING'],
        'VOTE_DISPLAY_AS_RATING' => (isset($arParams['DETAIL_VOTE_DISPLAY_AS_RATING']) ? $arParams['DETAIL_VOTE_DISPLAY_AS_RATING'] : ''),
        'USE_COMMENTS' => $arParams['DETAIL_USE_COMMENTS'],
        'BLOG_USE' => (isset($arParams['DETAIL_BLOG_USE']) ? $arParams['DETAIL_BLOG_USE'] : ''),
        'BLOG_URL' => (isset($arParams['DETAIL_BLOG_URL']) ? $arParams['DETAIL_BLOG_URL'] : ''),
        'BLOG_EMAIL_NOTIFY' => (isset($arParams['DETAIL_BLOG_EMAIL_NOTIFY']) ? $arParams['DETAIL_BLOG_EMAIL_NOTIFY'] : ''),
        'VK_USE' => (isset($arParams['DETAIL_VK_USE']) ? $arParams['DETAIL_VK_USE'] : ''),
        'VK_API_ID' => (isset($arParams['DETAIL_VK_API_ID']) ? $arParams['DETAIL_VK_API_ID'] : 'API_ID'),
        'FB_USE' => (isset($arParams['DETAIL_FB_USE']) ? $arParams['DETAIL_FB_USE'] : ''),
        'FB_APP_ID' => (isset($arParams['DETAIL_FB_APP_ID']) ? $arParams['DETAIL_FB_APP_ID'] : ''),
        'BRAND_USE' => (isset($arParams['DETAIL_BRAND_USE']) ? $arParams['DETAIL_BRAND_USE'] : 'N'),
        'BRAND_PROP_CODE' => (isset($arParams['DETAIL_BRAND_PROP_CODE']) ? $arParams['DETAIL_BRAND_PROP_CODE'] : ''),
        'DISPLAY_NAME' => (isset($arParams['DETAIL_DISPLAY_NAME']) ? $arParams['DETAIL_DISPLAY_NAME'] : ''),
        'IMAGE_RESOLUTION' => (isset($arParams['DETAIL_IMAGE_RESOLUTION']) ? $arParams['DETAIL_IMAGE_RESOLUTION'] : ''),
        'PRODUCT_INFO_BLOCK_ORDER' => (isset($arParams['DETAIL_PRODUCT_INFO_BLOCK_ORDER']) ? $arParams['DETAIL_PRODUCT_INFO_BLOCK_ORDER'] : ''),
        'PRODUCT_PAY_BLOCK_ORDER' => (isset($arParams['DETAIL_PRODUCT_PAY_BLOCK_ORDER']) ? $arParams['DETAIL_PRODUCT_PAY_BLOCK_ORDER'] : ''),
        'ADD_DETAIL_TO_SLIDER' => (isset($arParams['DETAIL_ADD_DETAIL_TO_SLIDER']) ? $arParams['DETAIL_ADD_DETAIL_TO_SLIDER'] : ''),
        'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
        'ADD_SECTIONS_CHAIN' => (isset($arParams['ADD_SECTIONS_CHAIN']) ? $arParams['ADD_SECTIONS_CHAIN'] : ''),
        'ADD_ELEMENT_CHAIN' => 'N',
        'DISPLAY_PREVIEW_TEXT_MODE' => (isset($arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE']) ? $arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE'] : ''),
        'DETAIL_PICTURE_MODE' => (isset($arParams['DETAIL_DETAIL_PICTURE_MODE']) ? $arParams['DETAIL_DETAIL_PICTURE_MODE'] : array()),
        'ADD_TO_BASKET_ACTION_PRIMARY' => (isset($arParams['DETAIL_ADD_TO_BASKET_ACTION_PRIMARY']) ? $arParams['DETAIL_ADD_TO_BASKET_ACTION_PRIMARY'] : null),
        'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
        'DISPLAY_COMPARE' => (isset($arParams['USE_COMPARE']) ? $arParams['USE_COMPARE'] : ''),
        'COMPARE_PATH' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['compare'],
        'USE_COMPARE_LIST' => 'Y',
        'BACKGROUND_IMAGE' => (isset($arParams['DETAIL_BACKGROUND_IMAGE']) ? $arParams['DETAIL_BACKGROUND_IMAGE'] : ''),
        'COMPATIBLE_MODE' => (isset($arParams['COMPATIBLE_MODE']) ? $arParams['COMPATIBLE_MODE'] : ''),
        'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),
        'SET_VIEWED_IN_COMPONENT' => (isset($arParams['DETAIL_SET_VIEWED_IN_COMPONENT']) ? $arParams['DETAIL_SET_VIEWED_IN_COMPONENT'] : ''),
        'SHOW_SLIDER' => (isset($arParams['DETAIL_SHOW_SLIDER']) ? $arParams['DETAIL_SHOW_SLIDER'] : ''),
        'SLIDER_INTERVAL' => (isset($arParams['DETAIL_SLIDER_INTERVAL']) ? $arParams['DETAIL_SLIDER_INTERVAL'] : ''),
        'SLIDER_PROGRESS' => (isset($arParams['DETAIL_SLIDER_PROGRESS']) ? $arParams['DETAIL_SLIDER_PROGRESS'] : ''),
        'USE_ENHANCED_ECOMMERCE' => (isset($arParams['USE_ENHANCED_ECOMMERCE']) ? $arParams['USE_ENHANCED_ECOMMERCE'] : ''),
        'DATA_LAYER_NAME' => (isset($arParams['DATA_LAYER_NAME']) ? $arParams['DATA_LAYER_NAME'] : ''),
        'BRAND_PROPERTY' => (isset($arParams['BRAND_PROPERTY']) ? $arParams['BRAND_PROPERTY'] : ''),

        'USE_GIFTS_DETAIL' => $arParams['USE_GIFTS_DETAIL'] ?: 'Y',
        'USE_GIFTS_MAIN_PR_SECTION_LIST' => $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST'] ?: 'Y',
        'GIFTS_SHOW_DISCOUNT_PERCENT' => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
        'GIFTS_SHOW_OLD_PRICE' => $arParams['GIFTS_SHOW_OLD_PRICE'],
        'GIFTS_DETAIL_PAGE_ELEMENT_COUNT' => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
        'GIFTS_DETAIL_HIDE_BLOCK_TITLE' => $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'],
        'GIFTS_DETAIL_TEXT_LABEL_GIFT' => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],
        'GIFTS_DETAIL_BLOCK_TITLE' => $arParams['GIFTS_DETAIL_BLOCK_TITLE'],
        'GIFTS_SHOW_NAME' => $arParams['GIFTS_SHOW_NAME'],
        'GIFTS_SHOW_IMAGE' => $arParams['GIFTS_SHOW_IMAGE'],
        'GIFTS_MESS_BTN_BUY' => $arParams['~GIFTS_MESS_BTN_BUY'],
        'GIFTS_PRODUCT_BLOCKS_ORDER' => $arParams['LIST_PRODUCT_BLOCKS_ORDER'],
        'GIFTS_SHOW_SLIDER' => $arParams['LIST_SHOW_SLIDER'],
        'GIFTS_SLIDER_INTERVAL' => isset($arParams['LIST_SLIDER_INTERVAL']) ? $arParams['LIST_SLIDER_INTERVAL'] : '',
        'GIFTS_SLIDER_PROGRESS' => isset($arParams['LIST_SLIDER_PROGRESS']) ? $arParams['LIST_SLIDER_PROGRESS'] : '',

        'GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
        'GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],
        'GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE'],
    ];

    $elementId = $APPLICATION->IncludeComponent(
        'bitrix:catalog.element',
        '',
        $componentElementParams,
        $component
    );

    $APPLICATION->IncludeComponent(
        'its.agency:form.sample',
        'one_click',
        [
            'ITEM_ID' => $elementId,
            'IBLOCK_ID' => Iblock::getInstance()->get('one_click'),
            'PROPERTIES' => [
                'NAME',
                'PROPERTY_PHONE',
                'PROPERTY_ITEM',
                'PROPERTY_OFFER',
            ],
            'PROPERTIES_REQUIRE' => [
                'NAME',
                'PROPERTY_PHONE',
                'PROPERTY_ITEM',
            ],
            'USE_CAPTCHA' => 'Y',
            'CAPTCHA_PUBLIC' => RECAPTCHA_PUBLIC_KEY,
            'CAPTCHA_PRIVATE' => RECAPTCHA_PRIVATE_KEY,
            'BUTTON_NAME' => 'one_click_form_send',
            'MAIL_EVENT_NAME' => 'ONE_CLICK_FORM_SEND_EVENT',
        ],
        true
    );

    $arElement = ElementTable::getById($elementId)->fetch();
    $APPLICATION->AddChainItem($arElement['NAME']);

    $GLOBALS['CATALOG_CURRENT_ELEMENT_ID'] = $elementId;
    ?>
    <section class="card-recently js-tabs" data-switch-delay="600" data-animation-end="1400" id="elementRecommendedWrap">
        <div class="card-recently__tabs js-booster-mobile-x">
            <div class="card-recently__tabs_inner js-booster__inner">
                <div class="card-recently__tab js-tabs__btn" style="display:none" data-element-set-tab>
                    <?=Loc::getMessage('ELEMENT_SET_HEAD')?>
                </div>
                <?php $APPLICATION->ShowViewContent('ELEMENT_RECOMMEND_TAB_BUTTON')?>
            </div>
        </div>
        <div class="card-recently__content js-tabs__content" data-scroll id="jsElementSetWrap" style="height:0;"></div>
        <div class="card-recently__content js-tabs__content is-active">
            <?php
            if ($elementId > 0) {
                $recommendedData = [
                    'RECOMMENDED_IDS' => []
                ];
                $cacheId = [
                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                    $elementId,
                ];
                $obCache = new CPHPCache();
                if ($obCache->InitCache(36000, "cache_{$arParams['IBLOCK_ID']}_{$elementId}", '/catalog/recommended')) {
                    $recommendedData = $obCache->GetVars();
                } elseif ($obCache->StartDataCache()) {
                    if (Loader::includeModule('catalog')) {
                        $element = \CIBlockElement::GetByID($elementId)->GetNextElement();
                        $elementData = $element->GetFields();
                        $elementData['PROPERTIES'] = $element->GetProperties();
                        if (is_array($elementData['PROPERTIES']['RECOMMENDED']['VALUE'])) {
                            foreach ($elementData['PROPERTIES']['RECOMMENDED']['VALUE'] as $recommendId) {
                                $recommendedData['RECOMMENDED_IDS'][] = $recommendId;
                            }
                        }
                    }
                    $obCache->EndDataCache($recommendedData);
                }

                $GLOBALS['ELEMENT_RECOMMEND_FILTER']['ID'] = !empty($recommendedData['RECOMMENDED_IDS'])
                    ? $recommendedData['RECOMMENDED_IDS']
                    : false;

                $APPLICATION->IncludeComponent(
                    'bitrix:catalog.section',
                    'recommend',
                    [
                        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],

                        'ELEMENT_SORT_FIELD' => 'ID',
                        'ELEMENT_SORT_ORDER' => $recommendedData['RECOMMENDED_IDS'],
                        'ELEMENT_SORT_FIELD2' => $arParams['ELEMENT_SORT_FIELD2'],
                        'ELEMENT_SORT_ORDER2' => $arParams['ELEMENT_SORT_ORDER2'],

                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                        'CACHE_TIME' => $arParams['CACHE_TIME'],
                        'CACHE_FILTER' => $arParams['CACHE_FILTER'],
                        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],

                        'DISPLAY_TOP_PAGER' => 'N',
                        'DISPLAY_BOTTOM_PAGER' => 'N',

                        'META_KEYWORDS' => '',
                        'META_DESCRIPTION' => '',
                        'BROWSER_TITLE' => 'N',
                        'SET_LAST_MODIFIED' => 'N',
                        'INCLUDE_SUBSECTIONS' => $arParams['INCLUDE_SUBSECTIONS'],
                        'BASKET_URL' => $arParams['BASKET_URL'],
                        'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
                        'FILTER_NAME' => 'ELEMENT_RECOMMEND_FILTER',

                        'SET_TITLE' => 'N',
                        'MESSAGE_404' => '',
                        'SET_STATUS_404' => 'N',
                        'SHOW_404' => 'N',
                        'FILE_404' => '',
                        'PAGE_ELEMENT_COUNT' => 4,

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

                        'SECTION_ID' => 0,
                        'SECTION_CODE' => '',
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
            }
            ?>
        </div>
    </section>

    <?php
    $reviewsCount = $APPLICATION->IncludeComponent(
        'its.agency:iblock.reviews',
        '',
        [
            'IBLOCK_ID' => Iblock::getInstance()->get('reviews'),
            'FOR_ID' => $elementId,
            'FOR_PROPERTY' => 'ITEM',
            'RATING_PROPERTY' => 'RATING',
            'SHOW_ALL' => 'N',
            'PAGE_ELEMENT_COUNT' => 100,
            'HIDE_MODERATED' => 'Y',
            'USE_CAPTCHA' => 'Y',
            'CAPTCHA_PUBLIC' => RECAPTCHA_PUBLIC_KEY,
            'CAPTCHA_PRIVATE' => RECAPTCHA_PRIVATE_KEY,
            'PROPERTIES' => [
                'PREVIEW_TEXT',
                'NAME',
                'PROPERTY_EMAIL',
                'PROPERTY_ITEM',
                'PROPERTY_RATING',
            ],
            'PROPERTIES_REQUIRE' => [
                'PREVIEW_TEXT',
                'NAME',
                'PROPERTY_EMAIL',
                'PROPERTY_ITEM',
                'PROPERTY_RATING',
            ],
            'MAIL_EVENT_NAME' => 'REVIEW_FORM_SEND_EVENT',
            'PAGER_TEMPLATE' => 'reviews',
            'DISPLAY_TOP_PAGER' => 'N',
            'DISPLAY_BOTTOM_PAGER' => 'Y',
            'PAGER_TITLE' => '',
            'PAGER_SHOW_ALWAYS' => 'N',
            'PAGER_DESC_NUMBERING' => 'N',
            'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
            'PAGER_SHOW_ALL' => 'N',
        ],
        true
    );
    if ($reviewsCount > 0) {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let button = document.getElementById('jsElementReviewsButton');
                if (button) {
                    button.innerHTML = <?=json_encode($reviewsCount)?>;
                }
            });
        </script>
        <?php
    }
    ?>
</div>
