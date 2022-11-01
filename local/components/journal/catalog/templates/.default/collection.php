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

$APPLICATION->AddChainItem(
    Loc::getMessage('COLLECTION_CHAIN'),
    "{$arResult['FOLDER']}collections/"
);
$arResult['DISABLE_SECTIONS'] = 'Y';
$arResult['DISABLE_SECTIONS_BUTTON'] = 'Y';

if (intval($arResult['COLLECTION']['ID']) > 0) {
    $collection = \CIBlockElement::GetByID(intval($arResult['COLLECTION']['ID']))->GetNextElement();
    $arCollection = $collection->GetFields();
    $arCollection['PROPERTIES'] = $collection->GetProperties();

    $arResult['SMART_FILTER_CUSTOM_TEMPLATE'] = $arResult['COLLECTION']['FILTER_TEMPLATE'];
    $arResult['SMART_FILTER_CUSTOM_PREFILTER'] = [
        'PROPERTY_COLLECTIONS_VALUE' => $arCollection['PROPERTIES']['COLLECTION']['VALUE'] ?: false,
    ];

    $APPLICATION->IncludeComponent(
        'bitrix:news.detail',
        'empty',
        [
            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
            'IBLOCK_ID' => Iblock::getInstance()->get('collections'),
            'ELEMENT_ID' => $arResult['COLLECTION']['ID'],
            'ELEMENT_CODE' => '',
            'SECTION_ID' => '',
            'SECTION_CODE' => '',
            'META_KEYWORDS' => $arParams['META_KEYWORDS'],
            'META_DESCRIPTION' => $arParams['META_DESCRIPTION'],
            'BROWSER_TITLE' => $arParams['BROWSER_TITLE'],
            'SET_CANONICAL_URL' => 'N',
            'SET_LAST_MODIFIED' => $arParams['SET_LAST_MODIFIED'],
            'SET_TITLE' => $arParams['SET_TITLE'],
            'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
            'ADD_SECTIONS_CHAIN' => 'N',
            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
            'CACHE_TIME' => $arParams['CACHE_TIME'],
            'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
            'DISPLAY_TOP_PAGER' => 'N',
            'DISPLAY_BOTTOM_PAGER' => 'N',
            'ADD_ELEMENT_CHAIN' => 'Y',
        ],
        $component
    );

    $this->SetViewTarget('CATALOG_SECTION_HEAD');
    ?>
    <div class="collection-head">
        <h1 class="star-title" data-observe="fade-y" data-scroll>
            <svg class="i-subtract"><use xlink:href="#i-subtract"></use></svg>
            <span><?=$arCollection['NAME']?></span>
        </h1>
        <?php
        if (!empty($arCollection['DETAIL_TEXT'])) {
            ?>
            <div class="collection-head__content js-toggle is-active">
                <div class="collection-head__text js-text-overflow" data-observe="fade-y" data-scroll>
                    <?=$arCollection['DETAIL_TEXT']?>
                </div>
                <div class="collection-head__button js-toggle__btn" data-observe="fade-y" data-scroll>
                    <span data-default="<?=Loc::getMessage('COLLECTION_MORE')?>" data-active="<?=Loc::getMessage('COLLECTION_HIDE')?>"></span>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
    $this->EndViewTarget();
} else {
    $arResult['SMART_FILTER_CUSTOM_TEMPLATE'] = 'smart_filter_collections';
    $arResult['SMART_FILTER_CUSTOM_PREFILTER'] = [
        '!PROPERTY_COLLECTIONS_VALUE' => false,
    ];
}

require __DIR__ . '/section.php';
