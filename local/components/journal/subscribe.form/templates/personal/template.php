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
<div class="profile__info is-edit">
    <div class="profile__info-header">
        <p class="profile__info-title">
            <?=Loc::getMessage('SUBSCRIBE_PERSONAL_FORM_AVAILABLE_MAILINGS')?>
        </p>
    </div>
    <div class="profile__info-content">
        <form class="profile__option-wrapper" id="jsPersonalSubscribeForm">
            <input type="hidden" name="SUBSCRIBE" value="N">
            <label class="checkbox-circle" for="subscribe">
                <input type="checkbox"
                    name="SUBSCRIBE"
                    value="Y"
                    class="checkbox-circle__input"
                    id="subscribe"
                    data-checkbox="subscribe"
                    <?=($arResult['SUBSCRIBED'] == 'Y' ? 'checked' : '')?>
                />
                <span class="checkbox-circle__block">
                    <span class="checkbox-circle__switch"></span>
                </span>
                <div class="checkbox-circle__text">
                    <?=Loc::getMessage('SUBSCRIBE_PERSONAL_FORM_MAILING_NAME')?>
                </div>
            </label>
        </form>
        <p class="profile__info-text">
            <?=Loc::getMessage('SUBSCRIBE_PERSONAL_FORM_MAILING_INFO')?>
        </p>
    </div>
</div>
<script>
    document.addEventListener('checkbox:changed', function (event) {
        let form = document.getElementById('jsPersonalSubscribeForm');
        axios({
            url: <?=json_encode($APPLICATION->GetCurPageParam())?>,
            method: 'post',
            params: {
                <?=$arParams['FORM_NAME'] ?? 'subscribe_form'?>: 'Y',
                print_json: 'Y',
                sessid: <?=json_encode(bitrix_sessid())?>
            },
            data: new FormData(form),
            timeout: 0,
            responseType: 'json',
        }).then(function (response) {
            let data = response.data;
            if (!data.status) {
                showMessage({
                    title: <?=json_encode(Loc::getMessage('SUBSCRIBE_PERSONAL_FORM_ERROR'))?>,
                    text: data.message,
                    time: 10000
                });
            } else {
                if (data.action === 'SUBSCRIBE') {
                    showMessage({
                        title: <?=json_encode(Loc::getMessage('SUBSCRIBE_PERSONAL_FORM_SUCCESS_SUBSCRIBE'))?>,
                        text: <?=json_encode(Loc::getMessage('SUBSCRIBE_PERSONAL_FORM_SUCCESS_SUBSCRIBE_MESSAGE'))?>,
                        time: 10000
                    });
                } else {
                    showMessage({
                        title: <?=json_encode(Loc::getMessage('SUBSCRIBE_PERSONAL_FORM_SUCCESS_UNSUBSCRIBE'))?>,
                        text: <?=json_encode(Loc::getMessage('SUBSCRIBE_PERSONAL_FORM_SUCCESS_UNSUBSCRIBE_MESSAGE'))?>,
                        time: 10000
                    });
                }
            }
        });
    });
</script>
