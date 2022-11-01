<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arCurrentValues
 */

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Internal\EventTypeTable;

Loc::loadMessages(__FILE__);

$arComponentParameters = [
    'GROUPS' => [
        'PARAMS' => [
            'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_GROUP_PARAMS'),
            'SORT' => '200'
        ],
    ],
];

try {
    if (!Loader::includeModule('iblock')) {
        throw new \Exception();
    }

    $resIblock = \Bitrix\Iblock\IblockTable::getList([
        'select' => ['ID', 'NAME'],
    ]);
    $arIblocks = [Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_NO_SELECT')];
    while ($arIblock = $resIblock->fetch()) {
        $arIblocks[$arIblock['ID']] = "[{$arIblock['ID']}] {$arIblock['NAME']}";
    }

    $arProperties = [
        Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_NO_SELECT'),
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_FIELD_NAME'),
        'PREVIEW_TEXT' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_FIELD_PREVIEW_TEXT'),
        'PREVIEW_PICTURE' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_FIELD_PREVIEW_PICTURE'),
        'DETAIL_TEXT' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_FIELD_DETAIL_TEXT'),
        'DETAIL_PICTURE' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_FIELD_DETAIL_PICTURE'),
    ];
    $resProperty = PropertyTable::getList([
        'filter' => ['=IBLOCK_ID' => intval($arCurrentValues['IBLOCK_ID'])],
        'select' => ['ID', 'CODE', 'NAME'],
    ]);
    while ($arProperty = $resProperty->fetch()) {
        $arProperties["PROPERTY_{$arProperty['CODE']}"] = "[{$arProperty['ID']}][{$arProperty['CODE']}] {$arProperty['NAME']}";
    }

    $resEvents = EventTypeTable::getList([
        'filter' => [
            '!EVENT_NAME' => null,
            'EVENT_TYPE' => EventTypeTable::TYPE_EMAIL,
        ],
        'order' => ['ID' => 'ASC'],
        'select' => ['EVENT_NAME', 'NAME'],
    ]);
    $arEvents = [Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_NO_SELECT')];
    while ($arEvent = $resEvents->fetch()) {
        $arEvents[$arEvent['EVENT_NAME']] = "[{$arEvent['EVENT_NAME']}] {$arEvent['NAME']}";
    }

    $arParameters['IBLOCK_ID'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_PARAM_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arIblocks,
        'DEFAULT' => '',
        'REFRESH' => 'Y',
    ];

    $arParameters['USE_CAPTCHA'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_PARAM_USE_CAPTCHA'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y',
    ];

    if ($arCurrentValues['USE_CAPTCHA'] === 'Y') {
        $arParameters['CAPTCHA_PUBLIC'] = [
            'PARENT' => 'PARAMS',
            'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_PARAM_CAPTCHA_PUBLIC'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ];
        $arParameters['CAPTCHA_PRIVATE'] = [
            'PARENT' => 'PARAMS',
            'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_PARAM_CAPTCHA_PRIVATE'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ];
    }

    $arParameters['PROPERTIES'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_PARAM_PROPERTIES'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties,
        'DEFAULT' => [],
        'MULTIPLE' => 'Y',
    ];

    $arParameters['PROPERTIES_REQUIRE'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_PARAM_PROPERTIES_REQUIRE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties,
        'DEFAULT' => [],
        'MULTIPLE' => 'Y',
    ];

    $arParameters['MAIL_EVENT_NAME'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_PARAM_MAIL_EVENT_NAME'),
        'TYPE' => 'LIST',
        'ADDITIONAL_VALUES' => 'N',
        'VALUES' => $arEvents,
        'DEFAULT' => '',
    ];

    $arComponentParameters['PARAMETERS'] = $arParameters;
} catch (\Throwable $e) {
    $arComponentParameters['PARAMETERS'] = [];
}
