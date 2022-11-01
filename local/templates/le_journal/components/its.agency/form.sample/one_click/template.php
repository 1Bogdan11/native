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

if (empty($arResult['FIELDS'])) {
    return;
}

$this->SetViewTarget('MODAL_ONE_CLICK');
?>
<section class="modal modal--buy-in-click modal--right" data-modal="buy-in-click">
    <button class="modal__overlay" type="button" data-modal-close="buy-in-click">
        <button class="modal__mobile-close" data-modal-close="buy-in-click"></button>
    </button>
    <div class="modal__container">
        <div class="modal__content">
            <form class="buy-in-click js-form" id="oneClickForm">
                <h2 class="buy-in-click__title">
                    <?=Loc::getMessage('ONE_CLICK_HEAD')?>
                </h2>
                <div class="buy-in-click__part">
                    <?php
                    foreach ($arResult['FIELDS'] as $code => $arField) {
                        switch ($code) {
                            case 'NAME':
                                ?>
                                <div class="input">
                                    <input type="text"
                                        autocomplete="off"
                                        name="<?=$arField['FIELD_NAME']?>"
                                        value="<?=$arField['VALUE']?>"
                                        <?=($arField['REQUIRE'] === 'Y' ? 'required' : '')?>
                                    />
                                    <label class="input__label">
                                        <?=Loc::getMessage("ONE_CLICK_FIELD_{$arField['FIELD_NAME']}")?>
                                    </label>
                                    <div class="input__bar"></div>
                                </div>
                                <?php
                                break;

                            case 'PHONE':
                                ?>
                                <div class="unique-phone js-unique-phone" data-selected-country-code="RU">
                                    <div class="unique-phone__select js-unique-phone-select"><select></select></div>
                                    <input type="tel"
                                        class="unique-phone__input js-unique-phone-input"
                                        autocomplete="off"
                                        name="<?=$arField['FIELD_NAME']?>"
                                        value="<?=$arField['VALUE']?>"
                                        <?=($arField['REQUIRE'] === 'Y' ? 'required' : '')?>
                                    />
                                </div>

                                <div class="input">
                                    <label class="input__label">
                                        <?=Loc::getMessage("FEEDBACK_FIELD_{$arField['FIELD_NAME']}")?>
                                    </label>
                                    <div class="input__bar"></div>
                                </div>
                                <?php
                                break;

                            case 'ITEM':
                                ?>
                                <input type="hidden" name="<?=$arField['FIELD_NAME']?>" value="<?=$arParams['ITEM_ID']?>"/>
                                <?php
                                break;

                            case 'OFFER':
                                ?>
                                <input type="hidden" id="jsCurrentOneClickOffer" name="<?=$arField['FIELD_NAME']?>" value="<?=$arParams['ITEM_ID']?>"/>
                                <?php
                                break;
                        }
                    }
                    ?>
                    <button class="btn" type="submit">
                        <span class="btn__bg"></span>
                        <span class="btn__text">
                            <?=Loc::getMessage('ONE_CLICK_SEND')?>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
    document.getElementById('oneClickForm').addEventListener('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();
        let form = this;
        grecaptcha.ready(function() {
            grecaptcha.execute(<?=json_encode($arParams['CAPTCHA_PUBLIC'])?>, {action: 'submit'}).then(function(token) {
                let data = new FormData(form),
                    phone = data.get('PROPERTY_PHONE') || '';
                data.delete('PROPERTY_PHONE');
                data.append('PROPERTY_PHONE', phone.replace(/[^0-9+]/gm, ''));
                axios({
                    url: <?=json_encode($APPLICATION->GetCurPage(false))?>,
                    method: 'post',
                    params: {
                        <?=$arParams['BUTTON_NAME']?>: 'Y',
                        sessid: <?=json_encode(bitrix_sessid())?>,
                        print_json: 'Y',
                        'g-recaptcha-response': token,
                    },
                    data: data,
                    timeout: 0,
                    responseType: 'json',
                }).then(function (response) {
                    let data = response.data;
                    if (!data.status) {
                        showMessage({
                            title: <?=json_encode(Loc::getMessage('ONE_CLICK_ERROR'))?>,
                            text: data.message,
                            time: 10000,
                        })
                    } else {
                        let inputs = document.getElementById('oneClickForm').querySelectorAll('input:not([type="hidden"])');
                        if (inputs) {
                            for (let i = 0; i < inputs.length; i++) {
                                inputs[i].value = '';
                            }
                        }
                        showMessage({
                            title: <?=json_encode(Loc::getMessage('ONE_CLICK_SUCCESS'))?>,
                            text: <?=json_encode(Loc::getMessage('ONE_CLICK_SUCCESS_MESSAGE'))?>,
                        });
                        document.dispatchEvent(new CustomEvent('modal:close', {detail: {name: 'buy-in-click'}}))
                    }
                });
            });
        });
    });
</script>
<style>
    .grecaptcha-badge {
        display: none !important;
    }
</style>
<?php
$this->EndViewTarget();
?>
