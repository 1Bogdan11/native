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

?>

<form class="js-form" id="feedbackForm">
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
                        <?=Loc::getMessage("FEEDBACK_FIELD_{$arField['FIELD_NAME']}")?>
                    </label>
                    <div class="input__bar"></div>
                </div>
                <?php
                break;

            case 'PREVIEW_TEXT':
                ?>
                <div class="input">
                    <input type="text"
                        name="<?=$arField['FIELD_NAME']?>"
                        value="<?=$arField['VALUE']?>"
                        <?=($arField['REQUIRE'] === 'Y' ? 'required' : '')?>
                    />
                    <label class="input__label">
                        <?=Loc::getMessage("FEEDBACK_FIELD_{$arField['FIELD_NAME']}")?>
                    </label>
                    <div class="input__bar"></div>
                </div>
                <?php
                break;
        }
    }
    ?>
    <button type="submit" class="button-black">
        <?=Loc::getMessage('FEEDBACK_SEND')?>
    </button>
</form>
<script>
    document.getElementById('feedbackForm').addEventListener('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();
        let form = this;
        grecaptcha.ready(function() {
            grecaptcha.execute(<?=json_encode($arParams['CAPTCHA_PUBLIC'])?>, {action: 'submit'}).then(function(token) {
                axios({
                    url: <?=json_encode($APPLICATION->GetCurPage(false))?>,
                    method: 'post',
                    params: {
                        <?=$arParams['BUTTON_NAME']?>: 'Y',
                        sessid: <?=json_encode(bitrix_sessid())?>,
                        print_json: 'Y',
                        'g-recaptcha-response': token,
                    },
                    data: new FormData(form),
                    timeout: 0,
                    responseType: 'json',
                }).then(function (response) {
                    let data = response.data;
                    if (!data.status) {
                        showMessage({
                            title: <?=json_encode(Loc::getMessage('FEEDBACK_ERROR'))?>,
                            text: data.message,
                            time: 10000,
                        })
                    } else {
                        let inputs = document.getElementById('feedbackForm').querySelectorAll('input');
                        if (inputs) {
                            for (let i = 0; i < inputs.length; i++) {
                                inputs[i].value = '';
                            }
                        }
                        showMessage({
                            title: <?=json_encode(Loc::getMessage('FEEDBACK_SUCCESS'))?>,
                            text: <?=json_encode(Loc::getMessage('FEEDBACK_SUCCESS_MESSAGE'))?>,
                        });
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
