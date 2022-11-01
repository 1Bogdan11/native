<?php

if (!empty($_REQUEST['siteId'])) {
    define('SITE_ID', htmlspecialchars(strval($_REQUEST['siteId'])));
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

/** @global CMain $APPLICATION */

$APPLICATION->IncludeComponent(
    'journal:element.set',
    '',
    [
        'ELEMENT_ID' => intval($_REQUEST['id']),
    ]
);
