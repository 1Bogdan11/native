<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class its_sendpulse extends CModule
{
    public function __construct()
    {
        $arModuleVersion = array();

        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'its.sendpulse';
        $this->MODULE_NAME = Loc::getMessage('ITS_SENDPULSE_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('ITS_SENDPULSE_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('ITS_SENDPULSE_MODULE_NAME');
        $this->PARTNER_URI = 'http://its.agency';
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function doUninstall()
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

}
