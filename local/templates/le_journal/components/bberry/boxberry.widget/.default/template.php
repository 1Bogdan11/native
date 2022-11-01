<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}


/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

Loc::loadMessages(__FILE__);

$moduleId = 'up.boxberrydelivery';

if ($arOrder = \CSaleOrder::GetByID($_REQUEST['orderId'])) {
    if (strpos($arOrder['DELIVERY_ID'], 'boxberry') === false) {
        return;
    }
}

$widgetUrl = trim(Option::get($moduleId, 'WIDGET_URL'));
if (!$widgetUrl) {
    $widgetUrl = 'https://points.boxberry.de/js/boxberry.js';
}

$buttonWrap = Option::get($moduleId, 'BB_CUSTOM_LINK', 'boxberrySelectPvzWrap');

?>
<script data-skip-moving="true" src="<?=$widgetUrl?>"></script>
<script data-skip-moving="true">
    window.modalOrderBoxberryScriptLoaded = function () {
        window.BoxberryDeliveryInstance = new BoxberryDelivery({
            pvzDelivery: <?=json_encode(\CDeliveryBoxberry::getPvzDeliveryIds())?>,
            addressPropCode: <?=json_encode(Option::get($moduleId, 'BB_ADDRESS', 'ADDRESS'))?>,
            selectButtonParams: <?="['" . \CDeliveryBoxberry::getLinkParams() . "']"?>,
            selectButtonContent: <?=json_encode(Loc::getMessage('BOXBERRY_SELECT_LINK'))?>,
            selectButtonWrap: <?=json_encode(strlen($buttonWrap) > 0 ? $buttonWrap : 'boxberrySelectPvzWrap')?>,
        });
    }
</script>
<script
        data-skip-moving="true"
        data-call="modalOrderBoxberryScriptLoaded"
        src="<?="{$templateFolder}/script.js?v=" . filemtime(__DIR__ . '/script.js')?>">
</script>
<style>
    .boxberry_container {
        position: fixed !important;
    }
</style>
