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
<form class="product-reviews__form" id="reviewsForm">
    <?php
    foreach ($arResult['FIELDS'] as $code => $arField) {
        switch ($code) {
            case 'PREVIEW_TEXT':
                ?>
                <div class="input__textarea">
                    <label class="input__label"><?=Loc::getMessage("REVIEWS_ADD_FIELD_{$arField['FIELD_NAME']}")?></label>
                    <textarea name="<?=$arField['FIELD_NAME']?>" id="REVIEW_<?=$arField['FIELD_NAME']?>"<?=($arField['REQUIRE'] === 'Y' ? ' required' : '')?>><?=$arField['VALUE']?></textarea>
                </div>
                <?php
                break;
            default:
                break;
        }
    }
    ?>
    <div class="product-reviews__form_part">
        <?php
        foreach ($arResult['FIELDS'] as $code => $arField) {
            switch ($code) {
                case 'NAME':
                    if ($USER->IsAuthorized()) {
                        ?>
                        <input type="hidden" name="<?=$arField['FIELD_NAME']?>" value="<?=$USER->GetFormattedName(false)?>">
                        <?php
                        break;
                    } else {
                        ?>
                        <div class="input">
                            <input type="text"
                                name="<?=$arField['FIELD_NAME']?>"
                                id="REVIEW_<?=$arField['FIELD_NAME']?>"
                                value="<?=$arField['VALUE']?>"
                                <?=($arField['REQUIRE'] === 'Y' ? 'required' : '')?>
                            />
                            <label class="input__label" for="REVIEW_<?=$arField['FIELD_NAME']?>">
                                <?=Loc::getMessage("REVIEWS_ADD_FIELD_{$arField['FIELD_NAME']}")?>
                            </label>
                            <div class="input__bar"></div>
                        </div>
                        <?
                    }
                    break;
                case 'EMAIL':
                    if ($USER->IsAuthorized()) {
                        ?>
                        <input type="hidden" name="<?=$arField['FIELD_NAME']?>" value="<?=$USER->GetEmail()?>">
                        <?php
                        break;
                    } else {
                        ?>
                        <div class="input">
                            <input type="email"
                                name="<?=$arField['FIELD_NAME']?>"
                                id="REVIEW_<?=$arField['FIELD_NAME']?>"
                                value="<?=$arField['VALUE']?>"
                                <?=($arField['REQUIRE'] === 'Y' ? 'required' : '')?>
                            />
                            <label class="input__label" for="REVIEW_<?=$arField['FIELD_NAME']?>">
                                <?=Loc::getMessage("REVIEWS_ADD_FIELD_{$arField['FIELD_NAME']}")?>
                            </label>
                            <div class="input__bar"></div>
                        </div>
                        <?
                    }
                    break;
                case 'ITEM':
                    ?><input type="hidden" name="<?=$arField['FIELD_NAME']?>" value="<?=$arParams['FOR_ID']?>"><?php
                    break;
                case 'RATING':
                    ?><input type="hidden" name="<?=$arField['FIELD_NAME']?>" value="5"><?php
                    break;
                default:
                    break;
            }
        }
        ?>
        <button class="button-black">
            <?=Loc::getMessage('REVIEWS_ADD_SEND')?>
        </button>
    </div>
    <?php if (!$USER->IsAuthorized()) : ?>
        <div class="form__confidentiality">
            <?=Loc::getMessage(
                'REVIEWS_ADD_TERMS',
                ['#LINK#' => SITE_DIR . 'support/politic/']
            )?>
        </div>
    <?php endif; ?>
    <div class="product-reviews__message">
        <b><?=Loc::getMessage('REVIEWS_ADD_SUCCESS')?></b>
        <p><?=Loc::getMessage('REVIEWS_ADD_SUCCESS_MESSAGE')?></p>
    </div>
</form>
<script>
    document.getElementById('reviewsForm').addEventListener('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();
        let form = this;
        grecaptcha.ready(function() {
            grecaptcha.execute(<?=json_encode($arParams['CAPTCHA_PUBLIC'])?>, {action: 'submit'}).then(function(token) {
                let params = {
                    <?=$arParams['BUTTON_NAME']?>: 'Y',
                    sessid: <?=json_encode(bitrix_sessid())?>,
                    print_json: 'Y',
                    'g-recaptcha-response': token,
                };
                params['<?=$arParams['FOR_PROPERTY']?>'] = <?=json_encode($arParams['FOR_ID'])?>;
                params['<?=$arParams['RATING_PROPERTY']?>'] = 5;
                axios({
                    url: <?=json_encode($APPLICATION->GetCurPage(false))?>,
                    method: 'post',
                    params: params,
                    data: new FormData(form),
                    timeout: 0,
                    responseType: 'json',
                }).then(function (response) {
                    let data = response.data;
                    if (!data.status) {
                        showMessage({
                            title: <?=json_encode(Loc::getMessage('REVIEWS_ADD_ERROR'))?>,
                            text: data.message,
                            time: 10000,
                        })
                    } else {
                        let inputs = document.getElementById('reviewsForm').querySelectorAll('input');
                        if (inputs) {
                            for (let i = 0; i < inputs.length; i++) {
                                if (!Tool.closest(inputs[i], '[type="hidden"]')) {
                                    inputs[i].value = '';
                                }
                            }
                        }
                        let textarea = document.getElementById('reviewsForm').querySelectorAll('textarea');
                        if (textarea) {
                            for (let i = 0; i < textarea.length; i++) {
                                textarea[i].value = '';
                            }
                        }
                        document.getElementById('reviewsForm').classList.add('is-success');
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