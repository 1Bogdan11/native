<?php

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
$APPLICATION->AddViewContent('MAIN_ATTRIBUTE', 'class="no-padding" id="js-scroll"');
$APPLICATION->AddViewContent('HEADER_CLASSES', 'is-inview');
$APPLICATION->AddViewContent('FOOTER_CLASSES', 'media-min--tab');
$APPLICATION->AddViewContent('HEADER_LEFT_MENU_ATTRIBUTE', 'style="display:none"');

$this->SetViewTarget('HEADER_LEFT_ADD_CONTENT');
?>
<a class="back-link media-max--tab" href="<?=$arParams['SEF_FOLDER']?>">
    <div class="back">
        <svg class="i-arrow-left"><use xlink:href="#i-arrow-left"></use></svg>
    </div>
    <span><?=Loc::getMessage('SHOPS_MAP_BACK')?></span>
</a>
<?php
$this->EndViewTarget();


$arResult['VARIABLES']['CURRENT_SECTION_ID'] = $APPLICATION->IncludeComponent(
    'bitrix:catalog.section',
    'empty',
    [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_FILTER' => $arParams['CACHE_FILTER'],
        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
        'DISPLAY_TOP_PAGER' => 'N',
        'DISPLAY_BOTTOM_PAGER' => 'N',
        'SET_LAST_MODIFIED' => 'Y',
        'SET_TITLE' => 'Y',
        'PAGE_ELEMENT_COUNT' => 1,
        'ADD_SECTIONS_CHAIN' => 'Y',
        'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
        'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
    ],
    false
);

?>
<div class="contacts contacts--shop js-contacts">
    <div class="contacts__map_close js-contacts-modal-close"></div>
    <div class="contacts__aside">
        <div class="aside-tabs aside-tabs--partial">
            <div class="aside-tabs__area">
                <div class="aside-tabs__content">
                    <div class="contacts__head">
                        <h1 class="contacts__head_title">
                            <?php $APPLICATION->ShowTitle(false)?>
                        </h1>
                        <?php $APPLICATION->ShowViewContent('SHOPS_MAP_HEAD')?>
                    </div>
                    <div class="contacts__content">
                        <?php $APPLICATION->IncludeComponent(
                            'bitrix:news.list',
                            'shops_map',
                            [
                                'CURRENT_ELEMENT_ID' => $arResult['VARIABLES']['CURRENT_ELEMENT_ID'],
                                'CURRENT_SECTION_ID' => $arResult['VARIABLES']['CURRENT_SECTION_ID'],

                                'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                'NEWS_COUNT' => $arParams['NEWS_COUNT'],
                                'SORT_BY1' => $arParams['SORT_BY1'],
                                'SORT_ORDER1' => $arParams['SORT_ORDER1'],
                                'SORT_BY2' => $arParams['SORT_BY2'],
                                'SORT_ORDER2' => $arParams['SORT_ORDER2'],
                                'FIELD_CODE' => $arParams['LIST_FIELD_CODE'],
                                'PROPERTY_CODE' => $arParams['LIST_PROPERTY_CODE'],
                                'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['detail'],
                                'SECTION_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['section'],
                                'IBLOCK_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['news'],
                                'DISPLAY_PANEL' => $arParams['DISPLAY_PANEL'],
                                'SET_TITLE' => 'N',
                                'SET_LAST_MODIFIED' => $arParams['SET_LAST_MODIFIED'],
                                'MESSAGE_404' => $arParams['MESSAGE_404'],
                                'SET_STATUS_404' => $arParams['SET_STATUS_404'],
                                'SHOW_404' => $arParams['SHOW_404'],
                                'FILE_404' => $arParams['FILE_404'],
                                'INCLUDE_IBLOCK_INTO_CHAIN' => $arParams['INCLUDE_IBLOCK_INTO_CHAIN'],
                                'ADD_SECTIONS_CHAIN' => 'N',
                                'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                'CACHE_TIME' => $arParams['CACHE_TIME'],
                                'CACHE_FILTER' => $arParams['CACHE_FILTER'],
                                'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                                'DISPLAY_TOP_PAGER' => $arParams['DISPLAY_TOP_PAGER'],
                                'DISPLAY_BOTTOM_PAGER' => $arParams['DISPLAY_BOTTOM_PAGER'],
                                'PAGER_TITLE' => $arParams['PAGER_TITLE'],
                                'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
                                'PAGER_SHOW_ALWAYS' => $arParams['PAGER_SHOW_ALWAYS'],
                                'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
                                'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
                                'PAGER_SHOW_ALL' => $arParams['PAGER_SHOW_ALL'],
                                'PAGER_BASE_LINK_ENABLE' => $arParams['PAGER_BASE_LINK_ENABLE'],
                                'PAGER_BASE_LINK' => $arParams['PAGER_BASE_LINK'],
                                'PAGER_PARAMS_NAME' => $arParams['PAGER_PARAMS_NAME'],
                                'DISPLAY_DATE' => $arParams['DISPLAY_DATE'],
                                'DISPLAY_NAME' => 'Y',
                                'DISPLAY_PICTURE' => $arParams['DISPLAY_PICTURE'],
                                'DISPLAY_PREVIEW_TEXT' => $arParams['DISPLAY_PREVIEW_TEXT'],
                                'PREVIEW_TRUNCATE_LEN' => $arParams['PREVIEW_TRUNCATE_LEN'],
                                'ACTIVE_DATE_FORMAT' => $arParams['LIST_ACTIVE_DATE_FORMAT'],
                                'USE_PERMISSIONS' => $arParams['USE_PERMISSIONS'],
                                'GROUP_PERMISSIONS' => $arParams['GROUP_PERMISSIONS'],
                                'FILTER_NAME' => $arParams['FILTER_NAME'],
                                'HIDE_LINK_WHEN_NO_DETAIL' => $arParams['HIDE_LINK_WHEN_NO_DETAIL'],
                                'CHECK_DATES' => $arParams['CHECK_DATES'],
                                'STRICT_SECTION_CHECK' => $arParams['STRICT_SECTION_CHECK'],
                                'PARENT_SECTION' => '',
                                'PARENT_SECTION_CODE' => '',
                            ],
                            $component
                        )?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="contacts__map media-max--tab">
        <div class="map" data-map-platform="desktop"></div>
    </div>
</div>
