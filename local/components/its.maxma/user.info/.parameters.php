<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arCurrentValues
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = [
    'GROUPS' => [
        'PARAMS' => [
            'NAME' => Loc::getMessage('ITS_MAXMA_COMPONENT_USER_INFO_GROUP_PARAMS'),
            'SORT' => '200'
        ],
    ],
];

try {
    $arParameters['USER_ID'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_MAXMA_COMPONENT_USER_INFO_PARAM_USER_ID'),
        'TYPE' => 'STRING',
        'DEFAULT' => 0,
    ];

    $arComponentParameters['PARAMETERS'] = $arParameters;
} catch (\Throwable $e) {
    $arComponentParameters['PARAMETERS'] = [];
}
