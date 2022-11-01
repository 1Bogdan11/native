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
<div class="about__slider about__content media-max--tab">
    <div class="about__slider-area">
        <button class="about__slider-prev js-about-prev"></button>
        <button class="about__slider-next js-about-next"></button>
        <div class="about__slider-list js-about-slider">
            <?php
            foreach ($arResult['ITEMS'] as $arItem) {
                $resize = new Resize(
                    intval($arItem['PREVIEW_PICTURE']['ID']),
                    [500, 500],
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    [Resize::SHARPEN_OFF]
                );
                ?>
                <div class="about__slider-item">
                    <?=$resize->getPictureTag(
                        [
                            'alt' => '',
                            'no_photo' => '/assets/img/no_photo.svg',
                        ]
                    )?>
                    <div class="about__slider-item_content">
                        <div class="about__slider-item_title">
                            <?=$arItem['NAME']?>
                        </div>
                        <div class="about__slider-item_description">
                            <?=$arItem['PREVIEW_TEXT']?>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>
