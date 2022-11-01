<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

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

$this->setFrameMode(false);

?>
<div class="s-lk__main-info-like-input"><?=$arParams['CURRENT_VALUE']?></div>
<a id="changeNumber" href="javascript:void(0)" style="display:<?=(empty($arParams['FIELD_VALUE']) ? 'block' : 'none')?>;">Изменить номер</a>
<form action="#" method="post" id="auth">
    <div id="error" style="color:red;padding-bottom:15px;"></div>
    <div id="phoneBlock" style="padding-bottom:20px;display:<?=(!empty($arParams['FIELD_VALUE']) ? 'block' : 'none')?>;">
        <div>Телефон</div>
        <input type="text" name="<?=$arParams['FIELD_NAME']?>" id="phone" value="" style="border: 1px solid;">
        <input type="button" id="phoneButton" value="Получить код" style="border: 1px solid;display:<?=($arResult['CONFIRM_PHONE'] !== 'Y' ? 'block' : 'none')?>;">
    </div>
    <div id="codeBlock" style="padding-bottom:20px;display:<?=($arResult['SEND_CODE'] === 'Y' ? 'block' : 'none')?>;">
        <div>Код подтверждения</div>
        <input type="text" name="code" id="code" value="" style="border: 1px solid;">
        <div id="message" style="margin: 15px 0;">Новый код подтверждения можно запросить через <span id="counter"><?=intval($arParams['RESEND_LIMIT'])?></span> сек.</div>
        <a id="repeat" href="javascript:void(0);" style="display: none;margin: 15px 0;">Запросить новый код</a>
        <input type="button" id="codeButton" value="Подтвердить" style="border: 1px solid;">
    </div>
</form>
<?
$params = [
    'sessId' => bitrix_sessid(),
    'changeNumber' => 'changeNumber',
    'error' => 'error',
    'phoneBlock' => 'phoneBlock',
    'phone' => 'phone',
    'phoneButton' => 'phoneButton',
    'codeBlock' => 'codeBlock',
    'code' => 'code',
    'codeButton' => 'codeButton',
    'message' => 'message',
    'counter' => 'counter',
    'repeat' => 'repeat',
];
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.phoneAuth = new PhoneAuthComponent(<?=json_encode($params)?>)
    });
</script>
