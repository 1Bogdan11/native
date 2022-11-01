<?php

namespace Its\Area;

use AdminConstructor\Helper\Url;
use Bitrix\Main\Page\Asset as AssetManager;

class Asset
{
    public static function setAssets()
    {
        $assetsRoot = Url::systemToRelative(realpath(__DIR__ . '/../assets'));
        global $APPLICATION;
        $APPLICATION->SetAdditionalCSS($assetsRoot . '/style.css');

        $assets = AssetManager::getInstance();
        $assets->addJs($assetsRoot . '/script.js');

        \CJSCore::Init('jquery3');
    }
}
