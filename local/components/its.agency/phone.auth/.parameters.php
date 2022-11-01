<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arCurrentValues
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Internal\EventTypeTable;

Loc::loadMessages(__FILE__);

$arComponentParameters = [
    'GROUPS' => [
        'PARAMS' => [
            'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_GROUP_PARAMS'),
            'SORT' => '200'
        ],
    ],
];

try {
    $resEvents = EventTypeTable::getList([
        'filter' => [
            '!EVENT_NAME' => null,
            'EVENT_TYPE' => EventTypeTable::TYPE_SMS,
        ],
        'order' => ['ID' => 'ASC'],
        'select' => ['EVENT_NAME', 'NAME'],
    ]);

    $arEvents = [Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_NO_SELECT')];
    while ($arEvent = $resEvents->fetch()) {
        $arEvents[$arEvent['EVENT_NAME']] = "[{$arEvent['EVENT_NAME']}] {$arEvent['NAME']}";
    }

    $arParameters['USER_PHONE_FIELD'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_PARAM_USER_PHONE_FIELD'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'PERSONAL_PHONE',
    ];

    $arParameters['USER_PHONE_ADDITIONAL_FIELDS'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_PARAM_USER_PHONE_ADDITIONAL_FIELDS'),
        'TYPE' => 'STRING',
        'DEFAULT' => [],
        'MULTIPLE' => 'Y',
    ];

    $arParameters['USER_PHONE_ADDITIONAL_FIELDS_REQUIRED'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_PARAM_USER_PHONE_ADDITIONAL_FIELDS_REQUIRED'),
        'TYPE' => 'STRING',
        'DEFAULT' => [],
        'MULTIPLE' => 'Y',
    ];

    $arParameters['CONFIRM_CODE_LENGTH'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_PARAM_CONFIRM_CODE_LENGTH'),
        'TYPE' => 'STRING',
        'DEFAULT' => '5',
    ];

    $arParameters['RESEND_LIMIT'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_PARAM_RESEND_LIMIT'),
        'TYPE' => 'STRING',
        'DEFAULT' => '60',
    ];

    $arParameters['USE_BACK_URL'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_PARAM_USE_BACK_URL'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
    ];

    $arParameters['SMS_EVENT_CODE'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_PARAM_SMS_EVENT_CODE'),
        'TYPE' => 'LIST',
        'ADDITIONAL_VALUES' => 'N',
        'VALUES' => $arEvents,
        'DEFAULT' => 'SMS_USER_CONFIRM_NUMBER',
    ];

    $arComponentParameters['PARAMETERS'] = $arParameters;
} catch (\Throwable $e) {
    $arComponentParameters['PARAMETERS'] = [];
}
