<?php

use Bitrix\Main\Localization\Loc;
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

if (empty($arResult['COLLECTIONS'])) {
    return;
}
?>
<div class="card-recently__content_inner">
    <script>
        document.querySelector('[data-element-set-tab]').removeAttribute('style');
        document.querySelector('[data-element-set-tab]').click();
        document.dispatchEvent(new CustomEvent('mobileSwiper:init'));
    </script>
    <?php
    $jsSets = [];
    foreach ($arResult['COLLECTIONS'] as $collectionData) {
        ?>
        <div class="fashion-finish cards-line js-slider-mobile swiper-container" data-preview="1.6">
            <ul class="fashion-finish__inner swiper-wrapper">
                <?php
                $counter = 0;
                foreach ($collectionData['BASKET']['ITEMS'] as $basketItemData) {
                    $itemData = $collectionData['ITEMS'][$basketItemData['PRODUCT_ID']];
                    $counter++;
                    ?>
                    <li class="fashion-finish__item cards-line__item swiper-slide">
                        <div class="card card--line">
                            <div class="card__icon">
                                <?php
                                if (count($collectionData['BASKET']['ITEMS']) === $counter) {
                                    ?>
                                    <svg class="i-plus"><use xlink:href="#i-equals"></use></svg>
                                    <?php
                                } else {
                                    ?>
                                    <svg class="i-plus"><use xlink:href="#i-plus"></use></svg>
                                    <?php
                                }
                                ?>
                            </div>
                            <a class="card__image is-item" href="<?=$itemData['DETAIL_PAGE_URL']?>">
                                <?php
                                $pictures = Template::selectPictures($itemData);
                                if (!empty($itemData['OFFERS'])) {
                                    $offerData = $itemData['OFFERS'][$itemData['OFFER_SELECTED']];
                                    $offerPictures = Template::selectPictures($offerData);
                                    if (!empty($offerPictures)) {
                                        $pictures = $offerPictures;
                                    }
                                }
                                $resize = new Resize(
                                    intval(reset($pictures)),
                                    [1000, 1000],
                                    BX_RESIZE_IMAGE_PROPORTIONAL,
                                    [Resize::SHARPEN_OFF]
                                );
                                echo $resize->getPictureTag(
                                    [
                                        'alt' => htmlspecialchars($itemData['NAME']),
                                        'img_attribute' => $arResult['PROPERTIES']['PREVIEW_ITEM_TYPE']['VALUE_XML_ID'] == 'FULL'
                                            ? 'data-scroll и data-scroll-speed="-0.8"'
                                            : '',
                                        'no_photo' => '/assets/img/no_photo.svg',
                                    ]
                                );
                                ?>
                                
                            </a>
                            <?php
                            if (!empty($itemData['OFFERS'])) {
                                $offerSelectedData = $itemData['OFFERS'][$itemData['OFFER_SELECTED']];
                                $offersWrap = implode(
                                    '_',
                                    [
                                        'offer',
                                        $collectionData['ID'],
                                        $itemData['ID'],
                                    ]
                                );
                                ?>
                                <div id="<?=$offersWrap?>" class="card__filters">
                                    <input
                                        type="hidden"
                                        name="<?="collection_{$collectionData['ID']}_product_{$itemData['ID']}"?>"
                                        value="<?=$offerSelectedData['ID']?>"
                                        data-offer-selected
                                    />
                                    <?php
                                    foreach ($itemData['SKU_PROPS'] as $propertyData) {
                                        ?>
                                        <select class="select js-select"
                                            name="<?=$offersWrap?>_<?=$propertyData['ID']?>"
                                            data-show="up"
                                            data-property="<?=$propertyData['ID']?>">
                                            <?php
                                            foreach ($propertyData['VALUES'] as $valueData) {
                                                $isChecked = intval($offerSelectedData['TREE']["PROP_{$propertyData['ID']}"]) === intval($valueData['ID']);
                                                ?>
                                                <option
                                                    <?=($isChecked ? 'selected' : '')?>
                                                    value="<?=$valueData['ID']?>"
                                                    data-value="<?=$valueData['ID']?>">
                                                    <?=$valueData['NAME']?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                                $jsParams = [
                                    'selectedOffer' => $itemData['OFFER_SELECTED'],
                                    'offersCount' => count($itemData['OFFERS']),
                                    'offers' => [],
                                ];
                                foreach ($itemData['OFFERS'] as $offerKey => $offerData) {
                                    $offerData['SKU_TREE'] = [];
                                    foreach ($offerData['TREE'] as $property => $value) {
                                        $propertyId = str_replace('PROP_', '', $property);
                                        $offerData['SKU_TREE'][intval($propertyId)] = intval($value);
                                    }
                                    $offerParams = [
                                        'id' => $offerData['ID'],
                                        'canBuy' => true,
                                        'tree' => $offerData['SKU_TREE'],
                                        'selectedTree' => [
                                            'value' => reset($offerData['SKU_TREE']),
                                            'property' => key($offerData['SKU_TREE'])
                                        ],
                                    ];
                                    $jsParams['offers'][$offerKey] = $offerParams;
                                }
                                $jsSets[] = [
                                    'WRAP' => $offersWrap,
                                    'PARAMS' => $jsParams,
                                ];
                            }
                            ?>

                            <div class="card__content">
                                <a class="card__title" href="<?=$itemData['DETAIL_PAGE_URL']?>">
                                    <?=$itemData['NAME']?>
                                </a>
                                <div class="card__price">
                                    <?php
                                    if (floatval($basketItemData['BASE_PRICE']) > floatval($basketItemData['PRICE'])) {
                                        ?>
                                        <span class="card__price_old"><?=$basketItemData['BASE_PRICE_FORMATTED']?></span>
                                        <?php
                                    }
                                    ?>
                                    <span class="card__price_new"><?=$basketItemData['PRICE_FORMATTED']?></span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php
                }
                ?>
                <li class="fashion-finish__item cards-line__item swiper-slide">
                    <div class="fashion-finish__total">
                        <div class="fashion-finish__price">
                            <span>
                                <?=$collectionData['BASKET']['SUM_FORMATTED']?>
                            </span>
                            <?php
                            if ($collectionData['BASKET']['BASE_SUM'] > $collectionData['BASKET']['SUM']) {
                                ?>
                                <span class="fashion-finish__oldprice">
                                    <?=$collectionData['BASKET']['BASE_SUM_FORMATTED']?>
                                </span>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                        if ($collectionData['IN_BASKET'] === 'Y') {
                            ?>
                            <button type="button" disabled class="button-bordered">
                                <span><?=Loc::getMessage('ELEMENT_SET_IN_BASKET')?></span>
                            </button>
                            <?php
                        } else {
                            ?>
                            <button type="button" class="button-bordered" data-set-add-to-basket="<?=$collectionData['ID']?>">
                                <span><?=Loc::getMessage('ELEMENT_SET_ADD_TO_BASKET')?></span>
                            </button>
                            <?php
                        }
                        ?>
                    </div>
                </li>
            </ul>
            <div class="progress swiper-pagination media-min--tab"></div>
        </div>
        <?php
    }
    ?>
    <script data-skip-moving="true">
        window.elementSetScriptLoaded = function () {
            <?php
            foreach ($jsSets as $jsSet) {
                ?>
                new ElementSetItem(
                    <?=json_encode($jsSet['WRAP'])?>,
                    <?=json_encode($jsSet['PARAMS'])?>
                );
                <?php
            }
            ?>
        }
    </script>
    <script
        data-skip-moving="true"
        data-call="elementSetScriptLoaded"
        src="<?="{$templateFolder}/script.js?v=" . filemtime(__DIR__ . '/script.js')?>">
    </script>
</div>
