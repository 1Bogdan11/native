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
<div class="contacts__head">
    <h1 class="contacts__head_title">
        <?=Loc::getMessage('FEEDBACK_HEAD')?>
    </h1>
    <div class="contacts__head_description">
        <?=Loc::getMessage('FEEDBACK_SUBHEAD')?>
    </div>
</div>
<div class="contacts__content">
    <div class="contacts__content_part">
        <form class="contacts__form" id="feedbackForm">
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

                    case 'EMAIL':
                        ?>
                        <div class="input">
                            <input type="email"
                                data-validation-rules="[&quot;email&quot;]"
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
            <div class="form__bottom-wrapper">
                <button class="button-black"><?=Loc::getMessage('FEEDBACK_SEND')?></button>
                <div class="form__answer"></div>
                <div class="form__confidentiality">
                    <?=Loc::getMessage(
                        'FEEDBACK_TERMS',
                        ['#LINK#' => SITE_DIR . 'support/politic/']
                    )?>
                </div>
            </div>
            
        </form>
    </div>
    <div class="contacts__content_bottom">
        <div class="contacts__links">
            <div class="contacts__links">
                <?php
                $APPLICATION->IncludeComponent(
                    'bitrix:main.include',
                    'contacts_phone',
                    [
                        'AREA_FILE_SHOW' => 'file',
                        'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/template_phone.php',
                    ],
                    true
                );
                $APPLICATION->IncludeComponent(
                    'bitrix:main.include',
                    'contacts_email',
                    [
                        'AREA_FILE_SHOW' => 'file',
                        'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/template_email.php',
                    ],
                    true
                );
                ?>
            </div>
        </div>
    </div>
</div>

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
