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

if (empty($arResult['ITEMS'])) {
    return;
}

?>
<div class="circles-section__inner swiper-container js-slider-mobile" data-preview="1">
    <ul class="circles-section__list swiper-wrapper" data-scroll>
        <?php
        foreach ($arResult["ITEMS"] as $arItem) {
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
            $resize = new Resize(
                intval($arItem['PREVIEW_PICTURE']['ID']),
                [240, 240],
                BX_RESIZE_IMAGE_EXACT,
                [Resize::SHARPEN_OFF]
            );
            ?>
            <li class="circles-section__item swiper-slide" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
                <?php
                if (!empty($arItem['DISPLAY_PROPERTIES']['LINK']['VALUE'])) {
                    ?>
                    <a href="<?=$arItem['DISPLAY_PROPERTIES']['LINK']['VALUE']?>">
                        <span><?=$arItem['NAME']?></span>
                        <?=$resize->getPictureTag(
                            [
                                'alt' => '',
                                'no_photo' => '/assets/img/no_photo.svg',
                                'img_attribute' => 'data-scroll data-scroll-speed="-0.4"',
                            ]
                        )?>
                    </a>
                    <?php
                } else {
                    ?>
                    <sapn>
                        <span><?=$arItem['NAME']?></span>
                        <?=$resize->getPictureTag(
                            [
                                'alt' => '',
                                'no_photo' => '/assets/img/no_photo.svg',
                                'img_attribute' => 'data-scroll data-scroll-speed="-0.4"',
                            ]
                        )?>
                    </sapn>
                    <?php
                }
                ?>
            </li>
            <?php
        }
        ?>
    </ul>
    <div class="progress swiper-pagination media-min--tab"></div>
</div>
