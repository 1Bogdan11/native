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
<div class="card card--favourites">
    <a class="card__image <?=($arResult['PROPERTIES']['PREVIEW_ITEM_TYPE']['VALUE_XML_ID'] == 'FULL' ? 'is-full' : 'is-item')?>"
        href="<?= $arResult['DETAIL_PAGE_URL'] ?>" data-select-item="<?=$arResult['ID']?>">
        <?php
        $pictures = Template::selectPictures($arResult);
        $resize = new Resize(
            reset($pictures), [1000, 1000], BX_RESIZE_IMAGE_PROPORTIONAL, [Resize::SHARPEN_OFF]
        );
        echo $resize->getPictureTag(
            [
                'alt' => htmlspecialchars($arResult['NAME']),
                'img_attribute' => $arResult['PROPERTIES']['PREVIEW_ITEM_TYPE']['VALUE_XML_ID'] == 'FULL'
                    ? 'data-scroll и data-scroll-speed="-0.8"'
                    : '',
                'no_photo' => '/assets/img/no_photo.svg',
            ]
        ); ?>
    </a>
    <div class="card__content">
        <a class="card__title" href="<?= $arResult['DETAIL_PAGE_URL'] ?>" data-select-item="<?=$arResult['ID']?>"><?= $arResult['NAME'] ?></a>
        <div class="card__price">
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
                if (floatval($arPrice['BASE_PRICE']) > floatval($arPrice['PRICE'])) {
                    ?>
                    <span class="card__price_old"><?=$arPrice['PRINT_BASE_PRICE']?></span>
                    <?php
                }
                ?>
                <span class="card__price_new"><?=$arPrice['PRINT_PRICE']?></span>
                <?php
            }
            ?>
        </div>
    </div>
</div>
