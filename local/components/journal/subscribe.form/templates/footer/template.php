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
<form class="footer__feedback" id="jsFooterSubscribeForm">
    <div class="media-max--tab footer__text--b">
        <?=Loc::getMessage('SUBSCRIBE_FOOTER_FORM_HEAD')?>
    </div>
    <div class="footer__feedback-input">
        <div class="input">
            <input type="email" name="EMAIL" required>
            <label class="input__label">
                <?=Loc::getMessage('SUBSCRIBE_FOOTER_FORM_FIELD_EMAIL')?>
            </label>
            <div class="input__bar"></div>
        </div>
        <button type="submit" class="form__button">
            <svg class="i-arrow-thin"><use xlink:href="#i-arrow-thin"></use></svg>
        </button>
    </div>
    <div class="form__confidentiality">
        <?=Loc::getMessage(
            'SUBSCRIBE_FOOTER_FORM_TERMS',
            ['#LINK#' => SITE_DIR . 'support/politic/']
        )?>
    </div>
</form>
<script>
    document.getElementById('jsFooterSubscribeForm').addEventListener('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();

        axios({
            url: <?=json_encode($APPLICATION->GetCurPageParam())?>,
            method: 'post',
            params: {
                <?=$arParams['FORM_NAME'] ?? 'subscribe_form'?>: 'Y',
                print_json: 'Y',
                sessid: <?=json_encode(bitrix_sessid())?>
            },
            data: new FormData(this),
            timeout: 0,
            responseType: 'json',
        }).then(function (response) {
            let data = response.data;
            if (!data.status) {
                showMessage({
                    title: <?=json_encode(Loc::getMessage('SUBSCRIBE_FOOTER_FORM_ERROR'))?>,
                    text: data.message,
                    time: 10000
                });
            }
            else{
                showMessage({
                    title: <?=json_encode(Loc::getMessage('SUBSCRIBE_FOOTER_FORM_SUCCES'))?>,
                    text: <?=json_encode(Loc::getMessage('SUBSCRIBE_FOOTER_FORM_SUCCES_MESSAGE'))?>,
                    time: 10000
                });
            }
        });
    });
</script>
