<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
	'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_NAME'),
	'DESCRIPTION' => Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_DESCRIPTION'),
	'PATH' => array(
		'ID' => 'its.agency',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_CATEGORY'),
	),
];