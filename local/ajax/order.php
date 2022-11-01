<?php

if (!empty($_REQUEST['siteId'])) {
    define('SITE_ID', htmlspecialchars(strval($_REQUEST['siteId'])));
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

/** @global CMain $APPLICATION */

$APPLICATION->IncludeComponent(
    'journal:sale.order.ajax',
    'modal',
    [
        'ACTION_VARIABLE' => 'soa-action',
        'ALLOW_APPEND_ORDER' => 'N',
        'ALLOW_AUTO_REGISTER' => 'Y',
        'COMPATIBLE_MODE' => 'Y',
        'DELIVERY_NO_AJAX' => 'Y',
        'DELIVERY_NO_SESSION' => 'Y',
        'DISABLE_BASKET_REDIRECT' => 'Y',
        'PATH_TO_AUTH' => '/personal/',
        'PATH_TO_BASKET' => '/basket/',
        'PATH_TO_PAYMENT' => '/personal/payment.php',
        'PATH_TO_PERSONAL' => '/personal/',
        'PATH_TO_CATALOG' => '/catalog/',
        'PAY_FROM_ACCOUNT' => 'N',
        'ONLY_FULL_PAY_FROM_ACCOUNT' => 'Y',
        'PRODUCT_COLUMNS_VISIBLE' => [],
        'SEND_NEW_USER_NOTIFY' => 'N',
        'SET_TITLE' => 'N',
        'SHOW_NOT_CALCULATED_DELIVERIES' => 'Y',
        'SHOW_VAT_PRICE' => 'Y',
        'SPOT_LOCATION_BY_GEOIP' => 'Y',
        'TEMPLATE_LOCATION' => 'popup',
        'USER_CONSENT' => 'N',
        'USE_PHONE_NORMALIZATION' => 'Y',
        'USE_PRELOAD' => 'Y',
        'USE_PREPAYMENT' => 'N',
    ],
    true
);

\Bitrix\Main\Application::getInstance()->getManagedCache()::finalize();
