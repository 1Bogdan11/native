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

$this->setFrameMode(false);
$this->SetViewTarget('MODAL_CATALOG_COLLECTIONS');
?>
<section class="modal modal--collections modal--center modal--beige" data-modal="collections"><button class="modal__overlay" type="button" data-modal-close="collections"><button class="modal__mobile-close"></button></button>
    <div class="modal__container">
        <div class="modal__content">
            <div class="categories-modal">
                <ul class="categories-modal__list">
                    <?php
                    foreach ($arResult['ITEMS'] as $arItem) {
                        ?>
                        <li class="categories-modal__item">
                            <a href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                <span><?=$arItem['NAME']?></span>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</section>
<?php
$this->EndViewTarget();

?>
<ul class="catalog-section__list" data-scroll>
    <?php
    foreach ($arResult['ITEMS'] as $arItem) {
        $resize = new Resize(
            intval($arItem['PREVIEW_PICTURE']['ID']),
            [1000, 1000],
            BX_RESIZE_IMAGE_PROPORTIONAL,
            [Resize::SHARPEN_OFF]
        );
        ?>
        <li class="catalog-section__item">
            <a class="collections-item" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                <div class="collections-item__image">
                    <?=$resize->getPictureTag(
                        [
                            'alt' => htmlspecialchars($arItem['NAME']),
                            'img_attribute' => 'data-scroll data-scroll-speed="-0.8"',
                            'no_photo' => '/assets/img/no_photo.svg',
                        ]
                    )?>
                </div>
                <div class="collections-item__title">
                    <?=$arItem['NAME']?>
                </div>
            </a>
        </li>
        <?php
    }
    ?>
</ul>
