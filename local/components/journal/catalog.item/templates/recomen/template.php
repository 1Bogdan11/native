<?php

use Journal\Tool\Template;
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

?>
<div class="basket-item" data-entity="item">
    <a class="basket-item__picture" href="<?= $arResult['DETAIL_PAGE_URL'] ?>" title="<?= $arResult['NAME'] ?>"
       data-entity="image-wrapper" data-select-item="<?=$arResult['ID']?>">
        <div class="basket-item__picture_overlay">
            <button class="basket-item__favourite js-toggle-item" data-favorite-item="<?=$arResult["ID"]?>" data-favorite-item-name="<?=htmlspecialchars($arResult['NAME'])?>">
                <svg class="i-bookmark">
                    <use xlink:href="#i-bookmark"></use>
                </svg>
            </button>
        </div>
        <?php
        $pictures = Template::selectPictures($arResult);
        $resize = new Resize(
            reset($pictures), [300, 300], BX_RESIZE_IMAGE_PROPORTIONAL, [Resize::SHARPEN_OFF]
        );
        echo $resize->getPictureTag(
            [
                'alt' => htmlspecialchars($arResult['NAME']),
                'no_photo' => '/assets/img/no_photo_sku.svg',
            ]
        );
        ?>
    </a>
    <div class="basket-item__content">
        <div class="basket-item__content_row">
            <div class="basket-item__props">
                <a class="basket-item__title" href="<?= $arResult['DETAIL_PAGE_URL'] ?>" data-select-item="<?=$arResult['ID']?>"><?= $arResult["NAME"] ?></a>
                <ul class="basket-item__colors">
                    <?php
                    $colors = $arResult['ADD_DATA']['SKU_PROPS_VARIANT'][\Journal\GlobalStorage::get('SKU_PROP_COLOR')];
                    foreach ($colors as $color) {
                        ?>
                        <li class="basket-item__color"><?= $color['display_value'] ?></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <div class="basket-item__price"><span class="basket-item__price_main">
               <?php
               $arPrice = [];
               if (!empty($arResult['OFFERS'])) {
                   foreach ($arResult['OFFERS'] as $offer) {
                       $currentPrice = $offer['ITEM_PRICES'][$offer['ITEM_PRICE_SELECTED']];
                       if ($currentPrice['PRICE'] > 0 && (empty($arPrice) || $currentPrice['PRICE'] < $arPrice['PRICE'])) {
                           $arPrice = $currentPrice;
                       }
                   }
               } else {
                   $arPrice = $arResult['ITEM_PRICES'][$arResult['ITEM_PRICE_SELECTED']];
               }
               if (!empty($arPrice)) {
                   echo $arPrice['PRINT_PRICE'];
               }
               ?>
            </span></div>
        </div>
        <div class="basket-item__content_row">
            <ul class="sizes js-toggle-list sizes--inline">
                <?
                foreach (
                    $arResult['ADD_DATA']['SKU_PROPS_VARIANT'][\Journal\GlobalStorage::get(
                        'SKU_PROP_SIZE'
                    )] as $size
                ) { ?>
                    <div class="sizes__item <?= ($size['can_buy'] ? 'js-toggle-item' : 'is-disabled') ?>"><?= $size['display_value'] ?></div>
                    <?
                } ?>
            </ul>
        </div>
    </div>
</div>


