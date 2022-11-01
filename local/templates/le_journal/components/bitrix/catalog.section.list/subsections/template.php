<?php

use Its\Library\Image\Resize;

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
$arEditParams = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_EDIT');

if (!$arResult['SECTIONS']) {
    return;
}

?>
<div class="subcategories catalog-section__subcategories">
    <div class="subcategories__inner">
        <?php
        foreach ($arResult['SECTIONS'] as $arSection) {
            $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $arEditParams);
            $resize = new Resize(intval($arSection['~PICTURE']), [300, 300], BX_RESIZE_IMAGE_EXACT);
            ?>
            <a class="subcategories__link" href="<?=$arSection['SECTION_PAGE_URL']?>" id="<?=$this->GetEditAreaId($arSection['ID'])?>">
                <div class="subcategories__img">
                    <?=$resize->getPictureTag([
                        'alt' => htmlspecialchars($arSection['NAME']),
                        'no_photo' => '/assets/img/no_photo.svg',
                    ])?>
                </div>
                <div class="subcategories__text">
                    <?=$arSection['NAME']?>
                </div>
            </a>
            <?php
        }
        ?>
    </div>
</div>
