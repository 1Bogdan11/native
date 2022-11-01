<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 */

$this->setFrameMode(true);
$editParams = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');

if (empty($arResult['ITEMS'])) {
    return;
}

$this->SetViewTarget('ELEMENT_RECOMMEND_TAB_BUTTON');
?>
<div class="card-recently__tab js-tabs__btn is-active">
    <?=Loc::getMessage('ELEMENT_RECOMMEND_HEAD')?>
</div>
<?php
$this->EndViewTarget();

?>
<div class="cards-line swiper-container js-slider-mobile" data-preview="1.6">
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
