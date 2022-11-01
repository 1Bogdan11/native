<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arCurrentValues
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

$arComponentParameters = [
    'GROUPS' => [
        'PARAMS' => [
            'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PERSONAL_SECTION_GROUP_PARAMS'),
            'SORT' => '200'
        ],
    ],
];

try {
    if (!Loader::includeModule('iblock')) {
        throw new \Exception();
    }

    $arParameters['ORDERS_ELEMENT_COUNT'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PERSONAL_SECTION_PARAM_ORDERS_ELEMENT_COUNT'),
        'TYPE' => 'STRING',
        'DEFAULT' => '12',
    ];

    $arParameters['ONLY_MENU'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PERSONAL_SECTION_PERSONAL_PARAM_ONLY_MENU'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
    ];

    $arParameters['PATH_TO_BASKET'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PERSONAL_SECTION_PARAM_PATH_TO_BASKET'),
        'TYPE' => 'STRING',
        'DEFAULT' => '/basket/',
    ];

    $arParameters['PATH_TO_CATALOG'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PERSONAL_SECTION_PARAM_PATH_TO_CATALOG'),
        'TYPE' => 'STRING',
        'DEFAULT' => '/catalog/',
    ];

    $arParameters['ACTIVE_DATE_FORMAT'] = \CIBlockParameters::GetDateFormat(
        Loc::getMessage('ITS_AGENCY_COMPONENT_PERSONAL_SECTION_PARAM_ACTIVE_DATE_FORMAT'),
        'PARAMS'
    );

    $arParameters['SEF_MODE'] = [];
    $arParameters['CACHE_TIME'] = ['DEFAULT' => 86400];

    $arComponentParameters['PARAMETERS'] = $arParameters;
} catch (\Throwable $e) {
    $arComponentParameters['PARAMETERS'] = [];
}
