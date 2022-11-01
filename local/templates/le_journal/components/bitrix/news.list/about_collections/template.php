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
<div class="about__collections js-booster-mobile-x">
    <div class="about__collections-inner js-booster__inner" data-scroll>
        <?php
        foreach ($arResult['ITEMS'] as $arItem) {
            $resize = new Resize(
                intval($arItem['PREVIEW_PICTURE']['ID']),
                [800, 800],
                BX_RESIZE_IMAGE_PROPORTIONAL,
                [Resize::SHARPEN_OFF]
            );
            ?>
            <a class="about__collections-item" href="<?=htmlspecialchars($arItem['PROPERTIES']['LINK']['VALUE'])?>">
                <div class="about__collections-item_title">
                    <b><?=$arItem['NAME']?></b>
                    <i>Collection</i>
                </div>
                <div class="about__collections-item_image">
                    <?=$resize->getPictureTag(
                        [
                            'alt' => '',
                            'no_photo' => '/assets/img/no_photo.svg',
                        ]
                    )?>
                </div>
            </a>
            <?php
        }
        ?>
    </div>
</div>
