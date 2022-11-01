<?php

use Its\Library\Asset\AssetManager;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}


/**
 * @var array $templateData
 * @var array $arParams
 * @var string $templateFolder
 * @global CMain $APPLICATION
 */

AssetManager::getInstance()->addJs("https://www.google.com/recaptcha/api.js?render={$arParams['CAPTCHA_PUBLIC']}");
