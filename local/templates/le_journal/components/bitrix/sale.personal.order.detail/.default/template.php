<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Delivery\Services\Manager as DeliveryManager;
use Bitrix\Sale\PaySystem\Service as PaySystemService;
use Bitrix\Sale\PaySystem\Manager as PaySystemManager;
use Bitrix\Sale\PaySystem\BaseServiceHandler;
use Bitrix\Sale\Order;
use Its\Library\Tool\Declension;
use Its\Library\Image\Resize;
use Journal\Tool\Template;
use Bitrix\Main\Loader;
use Its\Maxma\Entity\CertificateTable;

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
$order = Order::load($arResult['ID']);

?>
<section class="modal modal--active-order modal--aside" data-modal="active-order">
    <button class="modal__overlay" type="button" data-modal-close="active-order">
        <button class="modal__mobile-close"></button>
    </button>
    <div class="modal__container">
        <div class="modal__content">
            <div class="order-modal">
                <button class="order-modal__close-m mobile-close"></button>
                <div class="order-modal__content">
                    <p class="order-modal__title">
                        <?=Loc::getMessage('ORDER_DETAIL_ITEM_NAME', ['#NUMBER#' => $arResult['ACCOUNT_NUMBER']])?>
                    </p>
                    <div class="order-modal__list">
                        <div class="order-modal__item">
                            <div class="order-modal__item_content">
                                <p class="order-modal__subtitle">
                                    <?=Loc::getMessage('ORDER_DETAIL_DATE')?>
                                </p>
                                <p class="order-modal__text">
                                    <?=FormatDate($arParams['ACTIVE_DATE_FORMAT'], $arResult['DATE_INSERT'])?>
                                </p>
                            </div>
                        </div>
                        <?php
                        foreach ($arResult['SHIPMENT'] as $arShipment) {
                            if (empty($arShipment)) {
                                continue;
                            }
                            $arDelivery = DeliveryManager::getById($arShipment['DELIVERY_ID']);
                            ?>
                            <div class="order-modal__item">
                                <div class="order-modal__item_content">
                                    <p class="order-modal__subtitle">
                                        <?=Loc::getMessage('ORDER_DETAIL_DELIVERY')?>
                                    </p>
                                    <p class="order-modal__text"><?=$arDelivery['NAME']?> / <?=$arShipment['PRICE_DELIVERY_FORMATTED']?></p>
                                    <?php
                                    foreach ($arResult['ORDER_PROPS'] as $arProperty) {
                                        if (empty($arProperty['VALUE'])) {
                                            continue;
                                        }
                                        if ($arProperty['CODE'] === 'ADDRESS') {
                                            ?>
                                            <p class="order-modal__text"><?=$arProperty['VALUE']?></p>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }

                        $isAllowPay = $arResult['IS_ALLOW_PAY'] !== 'N' && $arResult['CANCELED'] !== 'Y';
                        foreach ($arResult['PAYMENT'] as $arPayment) {
                            ?>
                            <div class="order-modal__item">
                                <div class="order-modal__item_content">
                                    <p class="order-modal__subtitle">
                                        <?=Loc::getMessage('ORDER_DETAIL_PAYMENT')?>
                                    </p>
                                    <p class="order-modal__text">
                                        <?=$arPayment['PAY_SYSTEM_NAME']?>
                                        <?php
                                        if ($arPayment['PAID'] === 'Y') {
                                            echo Loc::getMessage('ORDER_DETAIL_PAYED');
                                        }
                                        ?>
                                    </p>
                                </div>
                                <?php
                                if ($arPayment['PAID'] !== 'Y' && $isAllowPay) {
                                    if ($arPayment['IS_CASH'] !== 'Y' && $arPayment['ACTION_FILE'] !== 'cash') {
                                        if ($arPayment['PSA_NEW_WINDOW'] === 'Y') {
                                            ?>
                                            <a class="button-black" target="_blank" href="<?=$arPayment['PSA_ACTION_FILE']?>">
                                                <?=Loc::getMessage('ORDER_DETAIL_PAY')?>
                                            </a>
                                            <?php
                                        } else {
                                            if (!defined('PAY_SYSTEM_TEMPLATE_PERSONAL')) {
                                                define('PAY_SYSTEM_TEMPLATE_PERSONAL', 'Y');
                                            }
                                            $service = new PaySystemService(
                                                PaySystemManager::getById($arPayment['PAY_SYSTEM_ID'])
                                            );
                                            /** @var \Bitrix\Sale\Payment $paymentItem */
                                            $paymentItem = $order->getPaymentCollection()->getItemById($arPayment['ID']);
                                            $initResult = $service->initiatePay(
                                                $paymentItem,
                                                null,
                                                BaseServiceHandler::STRING
                                            );
                                            if ($initResult->isSuccess()) {
                                                echo $initResult->getTemplate();
                                            } else {
                                                ?>
                                                <p><?=implode('<br>', $initResult->getErrorMessages())?></p>
                                                <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="basket__content">
                        <div class="basket__head">
                            <div class="basket__quantity">
                                <?php
                                $declension = new Declension(
                                    Loc::getMessage('ORDER_BASKET_COUNT_ONE'),
                                    Loc::getMessage('ORDER_BASKET_COUNT_TWO'),
                                    Loc::getMessage('ORDER_BASKET_COUNT_MANY')
                                );
                                echo count($arResult['BASKET']) . " {$declension->getMessage(count($arResult['BASKET']))}";
                                ?>
                            </div>
                        </div>
                        <div class="basket__content_inner">
                            <ul class="basket__list">
                                <?php
                                foreach ($arResult['BASKET'] as $arItem) {
                                    ?>
                                    <li class="basket__list_item">
                                        <div class="basket-item">
                                            <?php
                                            $pictures = Template::selectPictures(
                                                $arItem,
                                                false,
                                                count($arItem['OFFERS']) ? $arItem['OFFERS'][$arItem['OFFER_SELECTED_ID']] : []
                                            );
                                            $resize = new Resize(
                                                intval(reset($pictures)),
                                                [300, 300],
                                                BX_RESIZE_IMAGE_PROPORTIONAL
                                            );
                                            ?>
                                            <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="basket-item__picture">
                                                <?=$resize->getPictureTag([
                                                    'alt' => htmlspecialchars($arItem['NAME']),
                                                    'no_photo' => '/assets/img/no_photo_sku.svg',
                                                ])?>
                                            </a>
                                            <?php
                                            ?>
                                            <div class="basket-item__content">
                                                <div class="basket-item__content_row">
                                                    <div class="basket-item__props">
                                                        <a class="basket-item__title" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                                            <?=$arItem['NAME']?>
                                                        </a>
                                                        <div class="basket-item__color">
                                                            <?=$arItem['QUANTITY']?>
                                                            <?=$arItem['MEASURE_NAME']?>
                                                        </div>
                                                        <?php
                                                        if (!empty($arItem['OFFERS'])) {
                                                            $arOffer = $arItem['OFFERS'][$arItem['OFFER_SELECTED_ID']];
                                                            foreach ($arOffer['PROPERTIES'] as $arProperty) {
                                                                if ($arProperty['CODE'] === 'MORE_PHOTO') {
                                                                    continue;
                                                                }
                                                                ?>
                                                                <div class="basket-item__color">
                                                                    <?=$arProperty['NAME']?>: <?=$arProperty['VALUE']?>
                                                                </div>
                                                                <?php
                                                            }
                                                        }
                                                        if (Loader::includeModule('its.maxma')) {
                                                            $resCards = CertificateTable::getList([
                                                                'filter' => [
                                                                    '=ORDER_ID' => $arResult['ID'],
                                                                    '=ELEMENT_ID' => $arItem['PRODUCT_ID'],
                                                                ]
                                                            ]);
                                                            $cards = [];
                                                            while ($arCard = $resCards->fetch()) {
                                                                $cards[] = $arCard['CODE'] . ($arCard['EXPIRE_INF'] !== 'Y' ? " ({$arCard['EXPIRE']})" : '');
                                                            }
                                                            if (!empty($cards)) {
                                                                ?>
                                                                <div class="basket-item__color">
                                                                    <?=Loc::getMessage('ORDER_BASKET_ITEM_CARD')?>
                                                                    <?=implode(', ', $cards)?>
                                                                </div>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="basket-item__price">
                                                        <span class="basket-item__price_main">
                                                            <?=$arItem['FORMATED_SUM']?>
                                                        </span>
                                                    </div>
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

                </div>
                <div class="order-modal__footer">

                    <?php if ($arResult['CAN_CANCEL'] === 'Y') :?>
                        <a class="order-modal__submit-button"
                           href="<?=$arResult['URL_TO_CANCEL']?>"
                           data-text="<?=Loc::getMessage('ORDER_DETAIL_CANCEL')?>"
                           data-mobile-text="<?=Loc::getMessage('ORDER_DETAIL_CANCEL')?>">
                        </a>
                    <?php endif?>

                    <div class="order-modal__total">
                        <p class="order-modal__total-title"><?=Loc::getMessage('ORDER_DETAIL_TOTAL')?></p>
                        <p class="order-modal__total-text"><?=\CCurrencyLang::CurrencyFormat($arResult['PRICE'], $arResult['CURRENCY'])?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
