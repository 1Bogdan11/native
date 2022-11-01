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
            'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_GROUP_PARAMS'),
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
    $arIblocks = [Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_NO_SELECT')];
    while ($arIblock = $resIblock->fetch()) {
        $arIblocks[$arIblock['ID']] = "[{$arIblock['ID']}] {$arIblock['NAME']}";
    }

    $arProperties = [
        Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_NO_SELECT'),
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_FIELD_NAME'),
        'PREVIEW_TEXT' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_FIELD_PREVIEW_TEXT'),
        'PREVIEW_PICTURE' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_FIELD_PREVIEW_PICTURE'),
        'DETAIL_TEXT' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_FIELD_DETAIL_TEXT'),
        'DETAIL_PICTURE' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_FIELD_DETAIL_PICTURE'),
    ];
    $arForProperties = [
        Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_NO_SELECT'),
    ];
    $arRatingProperties = [
        Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_NO_SELECT'),
    ];
    $resProperty = PropertyTable::getList([
        'filter' => ['=IBLOCK_ID' => intval($arCurrentValues['IBLOCK_ID'])],
        'select' => ['ID', 'CODE', 'NAME', 'PROPERTY_TYPE'],
    ]);
    while ($arProperty = $resProperty->fetch()) {
        $arProperties["PROPERTY_{$arProperty['CODE']}"] = "[{$arProperty['ID']}][{$arProperty['CODE']}] {$arProperty['NAME']}";
        if (in_array($arProperty['PROPERTY_TYPE'], [PropertyTable::TYPE_ELEMENT, PropertyTable::TYPE_NUMBER, PropertyTable::TYPE_STRING])) {
            $arForProperties[$arProperty['CODE']] = "[{$arProperty['ID']}][{$arProperty['CODE']}] {$arProperty['NAME']}";
        }
        if (in_array($arProperty['PROPERTY_TYPE'], [PropertyTable::TYPE_NUMBER, PropertyTable::TYPE_STRING])) {
            $arRatingProperties[$arProperty['CODE']] = "[{$arProperty['ID']}][{$arProperty['CODE']}] {$arProperty['NAME']}";
        }
    }

    $resEvents = EventTypeTable::getList([
        'filter' => [
            '!EVENT_NAME' => null,
            'EVENT_TYPE' => EventTypeTable::TYPE_EMAIL,
        ],
        'order' => ['ID' => 'ASC'],
        'select' => ['EVENT_NAME', 'NAME'],
    ]);
    $arEvents = [Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_NO_SELECT')];
    while ($arEvent = $resEvents->fetch()) {
        $arEvents[$arEvent['EVENT_NAME']] = "[{$arEvent['EVENT_NAME']}] {$arEvent['NAME']}";
    }

    $arParameters['IBLOCK_ID'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_PARAM_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arIblocks,
        'DEFAULT' => '',
        'REFRESH' => 'Y',
    ];

    $arParameters['FOR_ID'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_PARAM_FOR_ID'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ];

    $arParameters['FOR_PROPERTY'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_PARAM_FOR_PROPERTY'),
        'TYPE' => 'LIST',
        'VALUES' => $arForProperties,
        'DEFAULT' => '',
        'MULTIPLE' => 'N',
    ];

    $arParameters['RATING_PROPERTY'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_PARAM_RATING_PROPERTY'),
        'TYPE' => 'LIST',
        'VALUES' => $arRatingProperties,
        'DEFAULT' => '',
        'MULTIPLE' => 'N',
    ];

    $arParameters['SHOW_ALL'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_PARAM_SHOW_ALL'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
    ];

    $arParameters['PAGE_ELEMENT_COUNT'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_PARAM_PAGE_ELEMENT_COUNT'),
        'TYPE' => 'STRING',
        'DEFAULT' => '5',
    ];

    $arParameters['USE_CAPTCHA'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_PARAM_USE_CAPTCHA'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y',
    ];

    if ($arCurrentValues['USE_CAPTCHA'] === 'Y') {
        $arParameters['CAPTCHA_PUBLIC'] = [
            'PARENT' => 'PARAMS',
            'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_PARAM_CAPTCHA_PUBLIC'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ];
        $arParameters['CAPTCHA_PRIVATE'] = [
            'PARENT' => 'PARAMS',
            'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_PARAM_CAPTCHA_PRIVATE'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ];
    }

    $arParameters['PROPERTIES'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_PARAM_PROPERTIES'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties,
        'DEFAULT' => [],
        'MULTIPLE' => 'Y',
    ];

    $arParameters['PROPERTIES_REQUIRE'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_PARAM_PROPERTIES_REQUIRE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties,
        'DEFAULT' => [],
        'MULTIPLE' => 'Y',
    ];

    $arParameters['MAIL_EVENT_NAME'] = [
        'PARENT' => 'PARAMS',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_IBLOCK_REVIEW_PARAM_MAIL_EVENT_NAME'),
        'TYPE' => 'LIST',
        'ADDITIONAL_VALUES' => 'N',
        'VALUES' => $arEvents,
        'DEFAULT' => '',
    ];

    $arComponentParameters['PARAMETERS'] = $arParameters;
    \CIBlockParameters::AddPagerSettings($arComponentParameters, '', true, true);
} catch (\Throwable $e) {
    $arComponentParameters['PARAMETERS'] = [];
}
