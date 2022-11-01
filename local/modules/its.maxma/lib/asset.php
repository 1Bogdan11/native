<?php

namespace Its\Maxma;

use AdminConstructor\Helper\Url;
use Bitrix\Main\Page\Asset as AssetManager;

class Asset
{
    public static function setAssets()
    {
        if (!defined('ADMIN_SECTION') || !ADMIN_SECTION) {
            return;
        }

        $assetsRoot = Url::systemToRelative(realpath(__DIR__ . '/../assets'));
        global $APPLICATION;
        $APPLICATION->SetAdditionalCSS($assetsRoot . '/style.css');

        $assets = AssetManager::getInstance();
        $assets->addJs($assetsRoot . '/script.js');

        \CJSCore::Init('jquery3');
    }
}
