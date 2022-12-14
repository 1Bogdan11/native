<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponent $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
/** @var array $templateData */

\Its\Library\Asset\AssetManager::getInstance()->addJs($templateFolder . '/script.js')->bitrix();

$arSubscribedIds = [];
foreach ($_SESSION['SUBSCRIBE_PRODUCT']['LIST_PRODUCT_ID'] ?? [] as $id => $status) {
    $arSubscribedIds[strval(intval($id))] = boolval($status);
}
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.subscribeElement.setSubscribed(<?=json_encode($arSubscribedIds)?>);
        document.subscribeElement.init(<?=json_encode(bitrix_sessid())?>);
    });
</script>
