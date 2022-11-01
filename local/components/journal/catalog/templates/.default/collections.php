<?php

use Bitrix\Main\Localization\Loc;
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
$APPLICATION->AddViewContent('MAIN_ATTRIBUTE', 'id="js-scroll-native" data-direction="vertical"');
$APPLICATION->SetTitle(Loc::getMessage('CATALOG_COLLECTIONS_TITLE'));
$APPLICATION->SetPageProperty('title', Loc::getMessage('CATALOG_COLLECTIONS_SEO_TITLE'));
$APPLICATION->SetPageProperty('description', Loc::getMessage('CATALOG_COLLECTIONS_SEO_DESCRIPTION'));

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
    <section class="catalog-section catalog-section--collections">
        <div class="catalog-section__head is-inview" data-scroll>
            <div class="catalog-section__head-row catalog-section__head-row--single">
                <button class="catalog__categories-btn" data-modal-open="collections">
                    <h1>
                        <?=Loc::getMessage('CATALOG_COLLECTIONS_TITLE')?>
                    </h1>
                </button>
            </div>
        </div>
        <?php $APPLICATION->IncludeComponent(
            'bitrix:news.list',
            'collections_catalog',
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
    </section>
</div>
