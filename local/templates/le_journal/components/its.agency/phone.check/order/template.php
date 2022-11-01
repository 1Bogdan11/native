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
$unique = md5($arParams['FIELD_NAME']);

?>
<div class="ordering__form_part ordering__form_part--full ordering__form_part--first is-reloadable is-active" data-form-step="1">
    <div class="input">
        <div class="input__phone js-input-phone" id="<?="phone_block_$unique"?>">
            <div class="unique-phone js-unique-phone" data-selected-country-code="<?=$arResult['FIELD_VALUE_COUNTRY']?>">
                <div class="unique-phone__title"><?=$arParams['PLACEHOLDER']?></div>
                <div class="unique-phone__select js-unique-phone-select">
                    <select id="<?="country_$unique"?>"></select>
                </div>
                <input class="unique-phone__input js-unique-phone-input"
                    type="tel"
                    required
                    name="<?=$arParams['FIELD_NAME']?>"
                    value="<?=$arResult['FIELD_VALUE']?>"
                    id="<?="phone_$unique"?>"
                />
            </div>
            <button type="button" class="button-black is-active" id="<?="change_button_$unique"?>" style="display:none">
                <?=Loc::getMessage('PHONE_CHECK_TEMPLATE_CHANGE_NUMBER')?>
            </button>
            <button type="button" class="button-black is-active" id="<?="phone_button_$unique"?>">
                <?=Loc::getMessage('PHONE_CHECK_TEMPLATE_GET_CODE')?>
            </button>
        </div>
        <div class="input__sms" id="<?="code_block_$unique"?>" style="display:none">
            <div class="input__phone">
                <div class="unique-phone">
                    <input type="text"
                        name="sms-code"
                        value=""
                        placeholder=""
                        id="<?="code_$unique"?>"
                    />
                    <label class="input__label" for="<?="code_$unique"?>">
                        <?=Loc::getMessage('PHONE_CHECK_TEMPLATE_SMS_CODE')?>
                    </label>
                    <div class="input__bar"></div>
                </div>
            </div>
            <div class="input__timer" id="<?="message_$unique"?>">
                <?=Loc::getMessage('PHONE_CHECK_TEMPLATE_SEND_CODE')?>
                <span id="<?="counter_$unique"?>">0</span>
                <?=Loc::getMessage('PHONE_CHECK_TEMPLATE_SEND_CODE_TIME')?>
            </div>
            <div class="input__sms_links">
                <button class="link__underline" type="button" id="<?="code_button_$unique"?>">
                    <?=Loc::getMessage('PHONE_CHECK_TEMPLATE_CHECK_CODE')?>
                </button>
                <a class="link__underline" href="javascript:void(0)" id="<?="repeat_$unique"?>">
                    <?=Loc::getMessage('PHONE_CHECK_TEMPLATE_GET_CODE_NOW')?>
                </a>
            </div>
        </div>
    </div>
</div>
<?php
$arJsIds = [
    'message' => "message_$unique",
    'counter' => "counter_$unique",
    'repeat' => "repeat_$unique",
    'country' => "country_$unique",
    'phone' => "phone_$unique",
    'code' => "code_$unique",
    'changeButton' => "change_button_$unique",
    'phoneButton' => "phone_button_$unique",
    'codeButton' => "code_button_$unique",
    'phoneBlock' => "phone_block_$unique",
    'codeBlock' => "code_block_$unique",
    'repeatBlock' => '',
];
?>

<script data-skip-moving="true">
    window.modalOrderPhoneCheckScriptLoaded = function () {
        window.ModalOrderPhoneCheckInstance = new OrderPhoneCheck(
            {
                devMode: <?=json_encode($arParams['DEV_MODE'] === 'Y')?>,
                sessId: <?=json_encode(bitrix_sessid())?>,
                sendCode: <?=json_encode($arResult['SEND_CODE'] === 'Y')?>,
                confirmPhone: <?=json_encode($arResult['CONFIRM_PHONE'] === 'Y')?>,
                checkedPhoneClass: 'is-correct',
                errorCallback: function (message) {
                    showMessage({
                        title: <?=json_encode(Loc::getMessage('PHONE_CHECK_TEMPLATE_ERROR'))?>,
                        text: message,
                        time: 10000,
                    })
                },
            },
            <?=json_encode($arJsIds)?>
        );
    }
</script>
<script
    data-skip-moving="true"
    data-call="modalOrderPhoneCheckScriptLoaded"
    src="<?="{$templateFolder}/script.js?v=" . filemtime(__DIR__ . '/script.js')?>">
</script>
