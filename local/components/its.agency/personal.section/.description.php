<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
	'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PERSONAL_SECTION_NAME'),
	'DESCRIPTION' => Loc::getMessage('ITS_AGENCY_COMPONENT_PERSONAL_SECTION_DESCRIPTION'),
	'PATH' => array(
		'ID' => 'its.agency',
        'NAME' => Loc::getMessage('ITS_AGENCY_COMPONENT_PERSONAL_SECTION_CATEGORY'),
	),
];