<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_NAME'),
    'DESCRIPTION' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_DESCRIPTION'),
    'PATH' => array(
        'ID' => 'its.agency',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_CATEGORY'),
    ),
];
