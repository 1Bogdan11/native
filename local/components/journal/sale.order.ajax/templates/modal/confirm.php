<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Order;
use Bitrix\Sale\BasketItem;
use Journal\Tool\Template;
use Its\Library\Image\Resize;

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

if (intval($arResult['ORDER']['ID']) <= 0) {
    return;
}
$order = Order::load($arResult['ORDER']['ID']);
?>
<section class="modal modal--ordering-success modal--center modal--beige" data-modal="ordering-success">
    <script data-skip-moving="true">
        ga4(
            'purchase',
            {
                currency: 'RUB',
                transaction_id: <?=json_encode($arResult['ORDER']['ACCOUNT_NUMBER'])?>,
                shipping: <?=json_encode(floatval($order->getDeliveryPrice()))?>,
                value: <?=json_encode(floatval($order->getPrice()))?>,
                items: <?=json_encode($arResult['GA4_ITEMS'])?>,
            }
        );
    </script>
    <button class="modal__overlay" type="button" data-modal-close="ordering-success">
        <button class="modal__mobile-close"></button>
    </button>
    <div class="modal__container">
        <div class="modal__content">
            <div class="ordering-success">
                <div class="ordering-success__part">
                    <div class="ordering-success__head">
                        <a class="is-dark logo" href="/">
                            <div class="logo__inner">
                                <div class="logo__part">
                                    <svg class="i-logo"><use xlink:href="#i-logo"></use></svg>
                                </div>
                                <div class="logo__part">
                                    <svg class="i-logo"><use xlink:href="#i-logo"></use></svg>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="ordering-success__content">
                        <h1 class="star-title" data-observe="fade-y" data-scroll>
                            <svg class="i-subtract"><use xlink:href="#i-subtract"></use></svg>
                            <span>
                                <?=Loc::getMessage('MODAL_ORDER_SUCCESS_HEAD')?>
                            </span>
                        </h1>
                        <?php
                        $arPayments = is_array($arResult['PAYMENT']) ? $arResult['PAYMENT'] : [];
                        $arPaySystem = $arResult['PAY_SYSTEM_LIST_BY_PAYMENT_ID'][reset($arPayments)['ID']];
                        ?>
                        <div class="ordering-success__description">
                            <?=Loc::getMessage('MODAL_ORDER_SUCCESS_DESCRIPTION')?>
                            <?php
                            if ($arResult['ORDER']['IS_ALLOW_PAY'] === 'Y' && $arPaySystem['IS_CASH'] !== 'Y') {
                                echo Loc::getMessage('MODAL_ORDER_SUCCESS_DESCRIPTION_PAY');
                            }
                            ?>
                        </div>
                        <?php
                        ?>
                    </div>
                </div>
                <div class="ordering-success__footer">
                    <a class="card-billet" href="/personal/orders/?order=<?=$arResult['ORDER']['ACCOUNT_NUMBER']?>">
                        <div class="card-billet__image">
                            <?php
                            /* Далее код только ради картинки на кнопке заказа! */

                            $order = Order::load(intval($arResult['ORDER']['ID']));
                            $basket = $order->getBasket();
                            $basketItems = $basket->getBasketItems();
                            /** @var BasketItem $firstItem */
                            $firstItem = reset($basketItems);
                            $arItem = [];
                            if ($firstItem) {
                                $productId = $firstItem->getProductId();
                                $productInfo = \CCatalogSku::GetProductInfo($productId);
                                if (empty($productInfo)) {
                                    $resElements = CIBlockElement::GetList(
                                        ['ID' => 'ASC'],
                                        ['ID' => $productId],
                                        false,
                                        false,
                                        ['ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'DETAIL_PICTURE']
                                    );

                                    if ($objElement = $resElements->GetNextElement()) {
                                        $arElement = $objElement->GetFields();
                                        $arElement['PROPERTIES'] = $objElement->GetProperties();
                                        unset($arElement['ID']);
                                        $arItem = $arElement;
                                    }
                                } else {
                                    $resParentElements = CIBlockElement::GetList(
                                        ['ID' => 'ASC'],
                                        ['ID' => $productInfo['ID'], 'IBLOCK_ID' => $productInfo['IBLOCK_ID']],
                                        false,
                                        false,
                                        ['ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'DETAIL_PICTURE']
                                    );

                                    if ($objParentElement = $resParentElements->GetNextElement()) {
                                        $arParent = $objParentElement->GetFields();
                                        $arParent['PROPERTIES'] = $objParentElement->GetProperties();
                                        $arParent['OFFER_SELECTED_ID'] = $productId;
                                        $arParent['OFFERS'] = \CCatalogSKU::getOffersList(
                                            [$arParent['PRODUCT_ID']],
                                            0,
                                            [],
                                            ['ID', 'IBLOCK_ID', 'NAME', 'CATALOG_AVAILABLE'],
                                            ['CODE' => ['MORE_PHOTO']]
                                        )[$arParent['ID']];

                                        $arItem = $arParent;
                                    }
                                }
                            }
                            $pictures = Template::selectPictures(
                                $arItem,
                                false,
                                count($arItem['OFFERS']) ? $arItem['OFFERS'][$arItem['OFFER_SELECTED_ID']] : []
                            );
                            $resize = new Resize(
                                intval(reset($pictures)),
                                [200, 200],
                                BX_RESIZE_IMAGE_EXACT
                            );
                            echo $resize->getPictureTag([
                                'no_photo' => '/assets/img/no_photo.svg',
                            ]);
                            ?>
                        </div>
                        <div class="card-billet__content">
                            <div class="card-billet__title">
                                <?=Loc::getMessage('MODAL_ORDER_SUCCESS_ORDER', [
                                    '#ORDER_ID#' => $arResult['ORDER']['ACCOUNT_NUMBER'],
                                ])?>
                            </div>
                            <div class="card-billet__status">
                                <?=Loc::getMessage('MODAL_ORDER_SUCCESS_ORDER_STATUS')?>
                            </div>
                        </div>
                    </a>
                    <?php
                    if ($arResult['ORDER']['IS_ALLOW_PAY'] === 'Y' && $arPaySystem['IS_CASH'] !== 'Y') {
                        if (!empty($arPaySystem['ERROR'])) {
                            ?>
                            <p style='color:red'>
                                <?=Loc::getMessage('MODAL_ORDER_SUCCESS_PAYMENT_ERROR')?>
                            </p>
                            <?php
                        } else {
                            echo $arPaySystem['BUFFERED_OUTPUT'];
                        }
                    }
                    ?>
                    <button class="modal__mobile-close" data-modal-close="ordering-success"></button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.dispatchEvent(new CustomEvent('custom_basket_reload'));
    </script>
</section>
