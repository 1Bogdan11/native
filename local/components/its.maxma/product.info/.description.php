<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    'NAME' => Loc::getMessage('ITS_MAXMA_COMPONENT_PRODUCT_INFO_NAME'),
    'DESCRIPTION' => Loc::getMessage('ITS_MAXMA_COMPONENT_PRODUCT_INFO_DESCRIPTION'),
    'PATH' => array(
        'ID' => 'its.maxma',
        'NAME' => Loc::getMessage('ITS_MAXMA_COMPONENT_PRODUCT_INFO_CATEGORY'),
    ),
];
