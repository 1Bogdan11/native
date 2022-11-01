<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/404.php')) {
        require $_SERVER['DOCUMENT_ROOT'] . '/404.php';
    }
    die();
}

/** @global $APPLICATION */

$APPLICATION->IncludeComponent(
    'its.agency:personal.section',
    '',
    [
        'ORDERS_ELEMENT_COUNT' => 5,
        'ONLY_MENU' => 'N',
        'PATH_TO_BASKET' => '',
        'PATH_TO_CATALOG' => '/catalog/',
        'ACTIVE_DATE_FORMAT' => 'd F Y, H:i:s',
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/en/personal/',
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => '86400',
    ],
    false
);
