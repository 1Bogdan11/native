<?php

use Its\Library\Asset\AssetManager;

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
    <div class="diary-section diary-section--full">
        <div class="diary-section__head" data-scroll data-observe="fade">
            <div class="diary-section__part">
                <h1 class="diary-section__title">
                    <?php $APPLICATION->ShowTitle(false)?>
                </h1>
                <div class="diary-section__date">
                    <?=CIBlockFormatProperties::DateFormat("j F Y", mktime())?>
                </div>
            </div>
            <div class="diary-section__part">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:catalog.section.list',
                    'articles_sections',
                    [
                        'ADD_SECTIONS_CHAIN' => 'N',
                        'CACHE_FILTER' => 'N',
                        'CACHE_GROUPS' => 'Y',
                        'CACHE_TIME' => '36000000',
                        'CACHE_TYPE' => 'A',
                        'COUNT_ELEMENTS' => 'N',
                        'COUNT_ELEMENTS_FILTER' => 'CNT_ACTIVE',
                        'FILTER_NAME' => 'sectionsFilterArticles',
                        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                        'SECTION_CODE' => '',
                        'SECTION_FIELDS' => [],
                        'SECTION_ID' => '',
                        'SECTION_URL' => '',
                        'SECTION_USER_FIELDS' => [],
                        'SHOW_PARENT_NAME' => 'Y',
                        'TOP_DEPTH' => '1',
                        'VIEW_MODE' => 'LINE',
                        'ACTIVE_SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
                        'SET_TITLE' => 'N',
                    ],
                    false
                )?>
                <?php $APPLICATION->IncludeComponent(
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
                        'ADD_SECTIONS_CHAIN' => 'N',
                        'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
                        'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
                    ],
                    false
                )?>
            </div>
        </div>
        <div id="jsBlogSectionWrap">
            <?php $APPLICATION->IncludeComponent(
                'bitrix:news.list',
                'blog',
                [
                    'YEAR_CACHE' => (new \DateTime())->format('Y'),

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
                    'ADD_SECTIONS_CHAIN' => $arParams['ADD_SECTIONS_CHAIN'],
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
                    'PARENT_SECTION' => $arResult['VARIABLES']['SECTION_ID'],
                    'PARENT_SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
                ],
                $component
            )?>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.section = new SectionPage({}, {
            wrapId: 'jsBlogSectionWrap',
            pageNavigationClass: 'jsPageNavigationWrap',
            showMoreClass: 'jsPageNavigationShowMoreButton',
            elementListClass: 'jsBlogSectionElementListWrap',
        });
    });
</script>
<?php
AssetManager::getInstance()->addJs(SITE_TEMPLATE_PATH . '/section.js');




