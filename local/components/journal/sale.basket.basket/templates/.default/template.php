<?php

use Bitrix\Main\Localization\Loc;
use Journal\Tool\Template;
use Its\Library\Tool\Declension;
use Its\Library\Image\Resize;
use Journal\GlobalStorage;
use Bitrix\Main\Loader;
use Its\Maxma\Order\Coupon;

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

?>
<div class="basket js-basket" id="jsBasketWrap">
    <?php
    if (empty($arResult['ERROR_MESSAGE'])) {
        ?>
        <div class="basket__content">
            <div class="basket__head">
                <div class="basket__quantity js-basket-quantity">
                    <?php
                    $count = $arResult["ORDERABLE_BASKET_ITEMS_COUNT"];
                    $countDeclension = new Declension(
                        Loc::getMessage('LJ_BASKET_COUNT_ONE'),
                        Loc::getMessage('LJ_BASKET_COUNT_FOUR'),
                        Loc::getMessage('LJ_BASKET_COUNT_FIVE')
                    );
                    echo $count . " " . $countDeclension->getMessage($count);
                    ?>
                </div>
                <button class="basket__clear js-basket-clear jsBasketRemoveAll">
                    <?=Loc::getMessage('LJ_BASKET_CLEARE')?>
                </button>
            </div>
            <div class="basket__content_inner">
                <ul class="basket__list js-toggle-free">
                    <?php
                    $sSkuColorPropCode = GlobalStorage::get('SKU_PROP_COLOR');
                    $sSkuSizePropCode = GlobalStorage::get('SKU_PROP_SIZE');
                    foreach ($arResult['ITEMS'] as $key => $arItem) {
                        if ($arItem['CAN_BUY'] !== 'Y') {
                            continue;
                        }
                        if (!empty($arItem['OFFERS'])) {
                            $arPictures = Template::selectPictures(
                                $arItem,
                                false,
                                $arItem['OFFERS'][$arItem['OFFER_SELECTED_ID']]
                            );
                        } else {
                            $arPictures = Template::selectPictures($arItem);
                        }
                        $resize = new Resize(
                            intval(reset($arPictures)),
                            [300, 300],
                            BX_RESIZE_IMAGE_PROPORTIONAL,
                            [Resize::SHARPEN_OFF]
                        );
                        ?>
                        <li class="basket__list_item">
                            <div class="basket-item" data-item="<?=$arItem['ID']?>">
                                <a class="basket-item__picture" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                    <div class="basket-item__picture_overlay">
                                        <button class="basket-item__favourite js-toggle-item" data-favorite-item="<?=$arItem["PRODUCT_ID"]?>" data-favorite-item-name="<?=htmlspecialchars($arResult['NAME'])?>">
                                            <svg class="i-bookmark"><use xlink:href="#i-bookmark"></use></svg>
                                        </button>
                                    </div>
                                    <?=$resize->getPictureTag([
                                        'alt' => htmlspecialchars($arItem['NAME']),
                                        'no_photo' => '/assets/img/no_photo_sku.svg',
                                    ])?>
                                </a>
                                <div class="basket-item__content">
                                    <div class="basket-item__content_row">
                                        <div class="basket-item__props">
                                            <a class="basket-item__title" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                                <?=$arItem['NAME']?>
                                            </a>
                                            <?php
                                            if (!empty($arItem['OFFERS'])) {
                                                $currentOffer = $arItem['OFFERS'][$arItem['OFFER_SELECTED_ID']];
                                                $colorSelected = $currentOffer['PROPERTIES'][$sSkuColorPropCode]['VALUE'];
                                                if ($colorSelected) {
                                                    ?>
                                                    <div class="basket-item__color"><?=$colorSelected?></div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <?php
                                        if ($arItem['CAN_BUY'] == 'Y') {
                                            ?>
                                            <div class="basket-item__price">
                                                <span class="basket-item__price_new"><?=$arItem['SUM']?></span>
                                                <?php
                                                if (floatval($arItem['PRICE']) < floatval($arItem['FULL_PRICE'])) {
                                                    ?>
                                                    <span class="basket-item__price_old"><?=$arItem['SUM_FULL_PRICE_FORMATED']?></span>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="basket-item__content_row">
                                        <div class="basket-item__sizes">
                                            <?php
                                            if (!empty($arItem['OFFERS'])) {
                                                ?>
                                                <select class="select js-select jsBasketOffer" data-item="<?=$arItem['ID']?>">
                                                    <?php
                                                    foreach ($arItem['OFFERS'] as $id => $arOffer) {
                                                        if ($arOffer['PROPERTIES'][$sSkuColorPropCode]['VALUE'] != $colorSelected || $arOffer['PROPERTIES'][$sSkuSizePropCode]['VALUE'] == '') {
                                                            continue;
                                                        }
                                                        $uniqueOffer = "{$arItem['ID']}_{$arOffer['ID']}";
                                                        $checked = $arOffer['ID'] == $currentOffer['ID'] ? 'selected' : '';
                                                        $disabled = ($arOffer['CATALOG_AVAILABLE'] == 'N' && $arOffer['CATALOG_CAN_BUY_ZERO'] == 'N') ? 'disabled' : '';
                                                        ?>
                                                        <option
                                                            id="<?=$uniqueOffer?>"
                                                            data-property="<?=$arOffer['PROPERTIES'][$sSkuSizePropCode]['CODE']?>"
                                                            data-offer="<?=$arOffer['ID']?>"
                                                            value="<?=$arOffer['PROPERTIES'][$sSkuSizePropCode]['~VALUE']?>"
                                                            name="offers_list_<?=$arOffer['ID']?>"
                                                            <?=$disabled?>
                                                            <?=$checked?>>
                                                            <?=$arOffer['PROPERTIES'][$sSkuSizePropCode]['VALUE']?>
                                                        </option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="basket-item__counter-wrapper jsBasketQuantity">
                                            <button class="basket-item__button basket-item__button--minus jsBasketQuantityMinus" data-action="decrement"></button>
                                            <span class="basket-item__counter jsBasketQuantityCount">
                                                <?=$arItem['QUANTITY']?>
                                            </span>
                                            <button class="basket-item__button basket-item__button--plus jsBasketQuantityPlus" data-action="increment"></button>
                                        </div>
                                        <button class="basket-item__button basket-item__button--restore">
                                            <svg class="i-repeat"><use xlink:href="#i-repeat"></use></svg>
                                        </button>
                                        <button class="basket-item__button basket-item__button--remove jsBasketRemove" data-action="delete"></button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="basket__footer">
            <?php
            if (Loader::includeModule('its.maxma') && $arResult['MAXMA']) {
                $coupon = Coupon::getFromSession();
                $couponStatus = '';
                if (!empty($coupon)) {
                    $couponStatus = $arResult['MAXMA']['PROMOCODE']['APPLY'] == 'Y' ? 'is-success' : 'is-error';
                }
                ?>
                <form class="basket__form js-form <?=$couponStatus?>">
                    <div class="input">
                        <input type="text"
                            class="jsBasketPromoCode"
                            value="<?=$coupon?>"
                            id="basket_promocode_field"
                        />
                        <label for="basket_promocode_field" class="input__label">
                            <?=Loc::getMessage('LJ_BASKET_PROMOCODE')?>
                        </label>
                        <div class="input__bar"></div>
                    </div>
                    <button type="button" class="form__button">
                        <svg class="i-arrow-thin"><use xlink:href="#i-arrow-thin"></use></svg>
                    </button>
                    <div class="promocode-success">
                        <div class="promocode-success__part">
                            <svg class="i-success"><use xlink:href="#i-success"></use></svg>
                            <span>
                                <?=Loc::getMessage(
                                    'LJ_BASKET_PROMOCODE_APPLY',
                                    ['#CODE#' => $coupon]
                                )?>
                            </span>
                        </div>
                        <button type="button" class="promocode-success__close js-promocode__close">
                            <svg class="i-close"><use xlink:href="#i-close"></use></svg>
                        </button>
                    </div>
                    <div class="promocode-error">
                        <svg class="i-error"><use xlink:href="#i-error"></use></svg>
                        <span><?=$arResult['MAXMA']['PROMOCODE']['ERROR']['DESCRIPTION']?></span>
                    </div>
                </form>
                <?php
            }
            ?>
            <div class="basket__footer_inner">
                <div class="basket__footer_part">
                    <span>
                        <?=Loc::getMessage('SBB_TOTAL')?>
                    </span>
                    <?php
                    if (floatval($arResult['allSum']) < floatval($arResult['allSum_BASE'])) {
                        ?>
                        <div class="basket__price--crossed"><?=$arResult['allSum_BASE_FORMATED']?></div>
                        <?php
                    }
                    ?>
                    <div class="basket__price js-basket-price">
                        <?=$arResult['allSum_FORMATED']?>
                    </div>
                </div>
                <button class="button-black" data-modal-url="/local/ajax/order.php?siteId=<?=SITE_ID?>" data-modal-nested="data-modal-nested">
                    <?=Loc::getMessage('SBB_ORDER')?>
                </button>
            </div>
        </div>
        <?php
    } elseif ($arResult['EMPTY_BASKET']) {
        include __DIR__ . '/empty.php';
    } else { ?>
        <div class="basket-modal__empty">
            <p><?=$arResult['ERROR_MESSAGE']?></p>
        </div>
        <?php
    }
    ?>
</div>

<script data-skip-moving="true">
    window.modalBasketScriptLoaded = function () {
        const basketInited = document.querySelector(".js-basket").dataset.inited;

        if(!basketInited){
            window.ModalBasketInstance = new Basket(
                'jsBasketWrap',
                {
                    currentPage: '<?=$APPLICATION->GetCurPage(false)?>',
                    site: '<?=$component->getSiteId()?>',
                    sessid: '<?=bitrix_sessid()?>',
                    url: '<?="{$component->getPath()}/ajax.php"?>',
                    actionVariable: '<?=$arParams['ACTION_VARIABLE']?>',
                    messageError: '<?=Loc::getMessage('BASKET_ERROR')?>',
                },
                {
                    basketRemove: '.jsBasketRemove',
                    basketQuantity: '.jsBasketQuantity',
                    basketQuantityCount: '.jsBasketQuantityCount',
                    basketQuantityMinus: '.jsBasketQuantityMinus',
                    basketQuantityPlus: '.jsBasketQuantityPlus',
                    basketOffer: '.jsBasketOffer',
                    basketRemoveAll: '.jsBasketRemoveAll',
                    basketPromoCode: '.jsBasketPromoCode',
                }
            );
        }
    }
    ga4(
        'view_cart',
        {
            currency: 'RUB',
            value: <?=json_encode(floatval($arResult['allSum']))?>,
            items: <?=json_encode($arResult['GA4_ITEMS'])?>,
        }
    );
</script>
<script
    data-skip-moving="true"
    data-call="modalBasketScriptLoaded"
    src="<?= "{$templateFolder}/script.js?v=" . filemtime(__DIR__ . '/script.js') ?>">
</script>
