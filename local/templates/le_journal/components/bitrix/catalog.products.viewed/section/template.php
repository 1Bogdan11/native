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

$editParams = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');

if (empty($arResult['ITEMS'])) {
    return;
}

?>
<section class="recently-section">
    <div class="recently-section__title" data-scroll data-observe="fade-y"><?=Loc::getMessage('PRODUCT_VIEWED_TITLE')?></div>
    <div class="cards-line swiper-container js-slider-mobile" data-preview="2" data-scroll>
        <ul class="cards-line__list swiper-wrapper">
            <?php
            foreach ($arResult['ITEMS'] as $arItem) {
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $editParams);
                $APPLICATION->IncludeComponent(
                    'journal:catalog.item',
                    'slider',
                    [
                        'ITEM' => $arItem,
                        'AREA_ID' => $this->GetEditAreaId($arItem['ID']),
                    ],
                    $component,
                    ['HIDE_ICONS' => 'Y']
                );
            }
            ?>
        </ul>
        <div class="progress swiper-pagination media-min--tab"></div>
    </div>
</section>
<?php
