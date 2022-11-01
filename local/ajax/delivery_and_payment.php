<?php

use Bitrix\Main\Localization\Loc;

if (!empty($_REQUEST['siteId'])) {
    define('SITE_ID', htmlspecialchars(strval($_REQUEST['siteId'])));
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

/** @global CMain $APPLICATION */
Loc::loadMessages(__FILE__);

?>
<section class="modal modal--payment-modal modal--right" data-modal="delivery-and-payment-modal" data-tabs-modal>
    <button class="modal__overlay" type="button" data-modal-close="delivery-and-payment-modal">
        <button class="modal__mobile-close"></button>
    </button>
    <div class="modal__container">
        <div class="modal__content">
            <button class="modal__close media-min--tab" data-modal-close="delivery-and-payment-modal"></button>
            <div class="aside-tabs">
                <div class="aside-tabs__links js-modal__tabs">
                    <button class="aside-tabs__link" data-modal-tab="delivery">
                        <?=Loc::getMessage('DELIVERY_AND_PAYMENT_MODAL_DELIVERY')?>
                    </button>
                    <button class="aside-tabs__link" data-modal-tab="payment">
                        <?=Loc::getMessage('DELIVERY_AND_PAYMENT_MODAL_PAYMENT')?>
                    </button>
                </div>
                <div class="aside-tabs__area js-modal__contents">
                    <div class="aside-tabs__content" data-modal-content="delivery">
                        <div class="product-modal__main">
                            <div class="product-modal__body">
                                <div class="product-modal__title">
                                    <?=Loc::getMessage('DELIVERY_AND_PAYMENT_MODAL_DELIVERY_TITLE')?>
                                </div>
                                <div class="product-list">
                                    <?php $APPLICATION->IncludeComponent(
                                        'bitrix:main.include',
                                        '',
                                        [
                                            'AREA_FILE_SHOW' => 'file',
                                            'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/product_modal_delivery.php',
                                        ],
                                        true
                                    )?>
                                </div>
                                <div class="product-modal__footer">
                                    <?php $APPLICATION->IncludeComponent(
                                        'bitrix:main.include',
                                        'delivery_return_link',
                                        [
                                            'AREA_FILE_SHOW' => 'file',
                                            'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/product_modal_delivery_return_link.php',
                                        ],
                                        true
                                    )?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="aside-tabs__content" data-modal-content="payment">
                        <div class="product-modal__main">
                            <div class="product-modal__body">
                                <div class="product-modal__title">
                                    <?=Loc::getMessage('DELIVERY_AND_PAYMENT_MODAL_PAYMENT_TITLE')?>
                                </div>
                                <div class="product-list">
                                    <?php $APPLICATION->IncludeComponent(
                                        'bitrix:main.include',
                                        '',
                                        [
                                            'AREA_FILE_SHOW' => 'file',
                                            'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/product_modal_payment.php',
                                        ],
                                        true
                                    )?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
