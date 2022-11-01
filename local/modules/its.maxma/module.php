<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return [
    'MODULE_ID' => 'its.maxma',
    'MODULE_VERSION' => '1.0.0',
    'MODULE_VERSION_DATE' => '27.04.2022',
    'MODULE_NAME' => Loc::getMessage('ITS_MAXMA_MODULE_NAME'),
    'MODULE_DESCRIPTION' => Loc::getMessage('ITS_MAXMA_MODULE_DESCRIPTION'),
    'PARTNER_NAME' => 'its.agency',
    'PARTNER_URI' => 'https://its.agency',
    'MODULE_SORT' => 100,
    'MODULE_GROUP_RIGHTS' => 'Y',
    'SHOW_SUPER_ADMIN_GROUP_RIGHTS' => 'Y'
];
