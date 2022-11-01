<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return [
    'MODULE_ID' => 'its.area',
    'MODULE_VERSION' => '1.3.0',
    'MODULE_VERSION_DATE' => '17.12.2021',
    'MODULE_NAME' => Loc::getMessage('ITS_AREA_MODULE_NAME'),
    'MODULE_DESCRIPTION' => Loc::getMessage('ITS_AREA_MODULE_DESCRIPTION'),
    'PARTNER_NAME' => 'its.agency',
    'PARTNER_URI' => 'https://its.agency',
    'MODULE_SORT' => 100,
    'MODULE_GROUP_RIGHTS' => 'Y',
    'SHOW_SUPER_ADMIN_GROUP_RIGHTS' => 'Y'
];
