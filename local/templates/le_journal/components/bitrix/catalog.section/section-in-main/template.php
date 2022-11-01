<?

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
?>
<div class="cards-line swiper-container js-slider-mobile"
     data-preview="2" <?= $arParams["ADD_DATA_SCROLL"] ? $arParams["ADD_DATA_SCROLL"] : ""; ?> >
    <ul class="cards-line__list swiper-wrapper">
        <?
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
