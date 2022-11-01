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

if ($arResult['SUCCESS'] === 'Y') {
    LocalRedirect('Success url!');
}
?>
<section class="contacts-page__form">
    <div class="contacts-page__form-container">
        <div class="contacts-page__form-left-side">
            <div class="contacts-page__form-title t-h2">
                <?=Loc::getMessage('FEEDBACK_HEAD')?>
            </div>
            <div class="contacts-page__form-text">
                <?=Loc::getMessage('FEEDBACK_TITLE')?>
            </div>
        </div>
        <form class="contacts-page__form-right-side" id="feedbackForm">
            <?php
            echo bitrix_sessid_post();

            foreach ($arResult['FIELDS'] as $arField) {
                switch ($arField['FIELD_NAME']) {
                    case 'NAME':
                        ?>
                        <input class="input <?=($arField['REQUIRE'] === 'Y' ? 'validate' : '')?>"
                            type="text"
                            name="<?=$arField['FIELD_NAME']?>"
                            placeholder="<?=Loc::getMessage("FEEDBACK_FIELD_{$arField['FIELD_NAME']}")?>"
                            value="<?=$arField['VALUE']?>"
                        />
                        <?php
                        break;

                    case 'PROPERTY_PHONE':
                        ?>
                        <input class="input <?=($arField['REQUIRE'] === 'Y' ? 'validate' : '')?>"
                            type="tel"
                            name="<?=$arField['FIELD_NAME']?>"
                            placeholder="<?=Loc::getMessage("FEEDBACK_FIELD_{$arField['FIELD_NAME']}")?>"
                            value="<?=$arField['VALUE']?>"
                        />
                        <?php
                        break;

                    case 'PROPERTY_EMAIL':
                        ?>
                        <input class="input <?=($arField['REQUIRE'] === 'Y' ? 'validate' : '')?>"
                            type="email"
                            name="<?=$arField['FIELD_NAME']?>"
                            placeholder="<?=Loc::getMessage("FEEDBACK_FIELD_{$arField['FIELD_NAME']}")?>"
                            value="<?=$arField['VALUE']?>"
                        />
                        <?php
                        break;

                    case 'PREVIEW_TEXT':
                        ?>
                        <input class="input input--grey <?=($arField['REQUIRE'] === 'Y' ? 'validate' : '')?>"
                            type="text"
                            name="<?=$arField['FIELD_NAME']?>"
                            placeholder="<?=Loc::getMessage('FEEDBACK_TEXT_PLACEHOLDER')?>"
                            value="<?=$arField['VALUE']?>"
                        />
                        <?php
                        break;
                }
            }
            ?>
            <button class="btn btn--green contacts-page__form-btn js-submit-btn" type="submit">
                <?=Loc::getMessage('FEEDBACK_SEND')?>
            </button>
            <div class="confidentiality">
                <input class="confidentiality__checkbox validate" type="checkbox" id="confidentiality" name="confidentiality" checked="checked">
                <label class="confidentiality__label" for="confidentiality">
                    <?=Loc::getMessage('FEEDBACK_PRIVACY')?>
                </label>
            </div>
        </form>
    </div>
</section>

<script>
    document.getElementById('feedbackForm').addEventListener('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();
        axios({
            url: <?=json_encode($APPLICATION->GetCurPage(false))?>,
            method: 'post',
            params: {
                <?=$arParams['BUTTON_NAME']?>: 'Y',
                print_json: 'Y',
            },
            data: new FormData(this),
            timeout: 0,
            responseType: 'json',
        }).then(function (response) {
            let data = response.data;
            if (!data.status) {
                showFormMessage({
                    title: <?=json_encode(Loc::getMessage('FEEDBACK_ERROR'))?>,
                    text: data.message,
                });
            } else {
                let inputs = document.getElementById('feedbackForm').querySelectorAll('input');
                if (inputs) {
                    for (let i = 0; i < inputs.length; i++) {
                        inputs[i].value = '';
                    }
                }
                showFormMessage({
                    title: <?=json_encode(Loc::getMessage('FEEDBACK_SUCCESS'))?>,
                    text: <?=json_encode(Loc::getMessage('FEEDBACK_SUCCESS_MESSAGE'))?>,
                });
            }
        });
    });
</script>
