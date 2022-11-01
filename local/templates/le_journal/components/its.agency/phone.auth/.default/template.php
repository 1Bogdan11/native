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

foreach ($arResult['ERRORS'] as $key => &$message) {
    if (intval($key) !== $key) {
        $message = str_replace(
            '#FIELD#',
            Loc::getMessage("PHONE_AUTH_TEMPLATE_FIELD_{$key}"),
            $message
        );
    }
}

$unique = 'phone_reg'
?>

<div class="profile-modal js-profile-modal" id="<?="wrap_$unique"?>">
    <div class="profile-modal__title">
        <?=Loc::getMessage('PHONE_AUTH_TEMPLATE_TITLE')?>
    </div>
    <div class="profile-modal__part">
        <div class="profile-modal__inner">

            <form class="profile-modal__form profile-modal__form--final js-form" style="display:none" id="<?="success_block_$unique"?>">
                <div class="profile-modal__head">
                    <div class="profile-modal__head_title">
                        <?=Loc::getMessage('PHONE_AUTH_TEMPLATE_STEP_4_TITLE')?>
                    </div>
                    <div class="profile-modal__form_part">
                        <a class="btn btn--lines"
                            href="javascript:document.location.reload()"
                            id="<?="back_url_$unique"?>">
                            <span class="btn__bg"></span>
                            <span class="btn__text">
                                <?=Loc::getMessage('PHONE_AUTH_TEMPLATE_STEP_4_BACK')?>
                            </span>
                        </a>
                        <a class="btn"
                            href="<?=SITE_DIR?>personal/">
                            <span class="btn__bg"></span>
                            <span class="btn__text">
                                <?=Loc::getMessage('PHONE_AUTH_TEMPLATE_STEP_4_PERSONAL')?>
                            </span>
                        </a>
                    </div>
                </div>
            </form>

            <?php
            if (!empty($arResult['ERRORS'])) {
                ?>
                <script data-skip-moving="true">
                    showMessage({
                        title: <?=json_encode(Loc::getMessage('PHONE_AUTH_TEMPLATE_ERROR'))?>,
                        text: <?=json_encode(implode('<br>', $arResult['ERRORS']))?>,
                        time: 10000,
                    });
                </script>
                <?php
            }

            if ($arResult['TEMPLATE_TYPE'] === 'SUCCESS') {
                ?>
                <p><?=Loc::getMessage('PHONE_AUTH_TEMPLATE_ALREADY_REGISTER')?></p>
                <?php
            } elseif ($arResult['TEMPLATE_TYPE'] === 'LAST_STEP') {
                ?>
                <form class="profile-modal__form js-form" id="<?="form_block_$unique"?>">
                    <input type="hidden" name="SAVE_ADDITIONAL_FIELDS" value="Y">
                    <div class="profile-modal__head">
                        <a class="profile-modal__head_back" href="javascript:void(0)" id="<?="reset_button_$unique"?>">
                            <svg class="i-arrow"><use xlink:href="#i-arrow"></use></svg>
                        </a>
                        <div class="profile-modal__head_title">
                            <?=Loc::getMessage('PHONE_AUTH_TEMPLATE_STEP_3_TITLE')?>
                        </div>
                        <div class="profile-modal__form_part">
                            <?php
                            foreach ($arResult['FIELDS'] as $code => $arField) {
                                switch ($code) {
                                    case 'EMAIL':
                                        ?>
                                        <div class="input">
                                            <input type="email"
                                                value="<?=$arField['VALUE']?>"
                                                name="<?=$arField['FIELD_NAME']?>"
                                                <?=($arField['REQUIRED'] === 'Y' ? 'required' : '')?>
                                            />
                                            <label class="input__label">
                                                <?=Loc::getMessage("PHONE_AUTH_TEMPLATE_FIELD_{$code}")?>
                                                <?=($arField['REQUIRED'] === 'Y' ? '*' : '')?>
                                            </label>
                                            <div class="input__bar"></div>
                                        </div>
                                        <?php
                                        break;

                                    default:
                                        ?>
                                        <div class="input">
                                            <input type="text"
                                                value="<?=$arField['VALUE']?>"
                                                name="<?=$arField['FIELD_NAME']?>"
                                                <?=($arField['REQUIRED'] === 'Y' ? 'required' : '')?>
                                            />
                                            <label class="input__label">
                                                <?=Loc::getMessage("PHONE_AUTH_TEMPLATE_FIELD_{$code}")?>
                                                <?=($arField['REQUIRED'] === 'Y' ? '*' : '')?>
                                            </label>
                                            <div class="input__bar"></div>
                                        </div>
                                        <?php
                                        break;
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="profile-modal__bottom">
                        <button class="btn" type="submit" id="<?="form_button_$unique"?>">
                            <span class="btn__bg"></span>
                            <span class="btn__text">
                                <?=Loc::getMessage('PHONE_AUTH_TEMPLATE_STEP_3_REGISTER')?>
                            </span>
                        </button>
                        <div class="form__confidentiality">
                            <?=Loc::getMessage(
                                    'PHONE_AUTH_TEMPLATE_POLITIC',
                                ['#LINK#' => SITE_DIR . 'support/politic/']
                            )?>
                        </div>
                    </div>
                </form>
                <?php
            }else {
                ?>
                <form class="profile-modal__form js-form" id="<?="phone_block_$unique"?>">
                    <div class="profile-modal__head">
                        <div class="profile-modal__head_title">
                            <span>
                                <?=Loc::getMessage('PHONE_AUTH_TEMPLATE_STEP_1_TITLE')?>
                            </span>
                        </div>
                        <div class="profile-modal__head_description">
                            <?=Loc::getMessage('PHONE_AUTH_TEMPLATE_STEP_1_SUBTITLE')?>
                        </div>
                        <div class="profile-modal__form_part">
                            <div class="unique-phone js-unique-phone" data-selected-country-code="">
                                <div class="unique-phone__select js-unique-phone-select">
                                    <select id="<?="country_$unique"?>"></select>
                                </div>
                                <input class="unique-phone__input js-unique-phone-input"
                                    type="tel"
                                    id="<?="phone_$unique"?>"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="profile-modal__bottom">
                        <button class="btn" type="submit" id="<?="phone_button_$unique"?>">
                            <span class="btn__bg"></span>
                            <span class="btn__text">
                                <?=Loc::getMessage('PHONE_AUTH_TEMPLATE_STEP_1_GET_CODE')?>
                            </span>
                        </button>
                        <div class="form__confidentiality">
                            <?=Loc::getMessage(
                                'PHONE_AUTH_TEMPLATE_POLITIC',
                                ['#LINK#' => SITE_DIR . 'support/politic/']
                            )?>
                        </div>
                    </div>
                </form>
                <form class="profile-modal__form js-form" style="display:none" id="<?="code_block_$unique"?>">
                    <div class="profile-modal__head">
                        <a class="profile-modal__head_back" href="javascript:void(0)" id="<?="change_button_$unique"?>">
                            <svg class="i-arrow"><use xlink:href="#i-arrow"></use></svg>
                        </a>
                        <div class="profile-modal__head_title">
                            <?=Loc::getMessage('PHONE_AUTH_TEMPLATE_STEP_2_TITLE')?>
                        </div>
                        <div class="profile-modal__head_description" id="<?="number_view_$unique"?>"></div>
                        <div class="profile-modal__form_part">
                            <div class="input">
                                <div class="input__sms">
                                    <div class="input__phone">
                                        <div class="unique-phone">
                                            <input type="text"
                                                id="<?="code_$unique"?>"
                                            />
                                            <label class="input__label">
                                                Код из смс
                                            </label>
                                            <div class="input__bar"></div>
                                        </div>
                                    </div>
                                    <div class="input__timer" id="<?="message_$unique"?>">
                                        <?=Loc::getMessage('PHONE_AUTH_TEMPLATE_STEP_2_TIMEOUT')?>
                                        <span id="<?="counter_$unique"?>">0</span>
                                        <?=Loc::getMessage('PHONE_AUTH_TEMPLATE_STEP_2_SECONDS')?>
                                    </div>
                                    <div class="input__timer" id="<?="repeat_$unique"?>">
                                        <a href="javascript:void(0)">
                                            <?=Loc::getMessage('PHONE_AUTH_TEMPLATE_STEP_2_REPEAT')?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="profile-modal__bottom">
                        <button class="btn" type="submit" id="<?="code_button_$unique"?>">
                            <span class="btn__bg"></span>
                            <span class="btn__text">
                                <?=Loc::getMessage('PHONE_AUTH_TEMPLATE_STEP_2_CHECK_NUMBER')?>
                            </span>
                        </button>
                    </div>
                </form>
                <?php
            }

            ?>
        </div>
    </div>
</div>

<?php
$arJsIds = [
    'wrap' => "wrap_$unique",

    'message' => "message_$unique",
    'counter' => "counter_$unique",
    'repeat' => "repeat_$unique",

    'country' => "country_$unique",
    'phone' => "phone_$unique",
    'code' => "code_$unique",

    'backUrl' => "back_url_$unique",
    'numberView' => "number_view_$unique",

    'phoneBlock' => "phone_block_$unique",
    'codeBlock' => "code_block_$unique",
    'formBlock' => "form_block_$unique",
    'successBlock' => "success_block_$unique",
    'repeatBlock' => '',

    'phoneButton' => "phone_button_$unique",
    'formButton' => "form_button_$unique",
    'changeButton' => "change_button_$unique",
    'codeButton' => "code_button_$unique",
    'resetButton' => "reset_button_$unique",
];
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        new PhoneAuth(
            {
                devMode: <?=json_encode($arParams['DEV_MODE'] === 'Y')?>,
                sessId: <?=json_encode(bitrix_sessid())?>,
                currentUrl: <?=json_encode($APPLICATION->GetCurPageParam('', ['change_number']))?>,
                errorCallback: function (message) {
                    showMessage({
                        title: <?=json_encode(Loc::getMessage('PHONE_AUTH_TEMPLATE_ERROR'))?>,
                        text: message,
                        time: 10000,
                    });
                },
            },
            <?=json_encode($arJsIds)?>
        );
    });
</script>
