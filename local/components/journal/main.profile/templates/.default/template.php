<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;

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

if (!function_exists('PrintUserFields')) {
    function PrintUserFields(array $arFieldCodes, array $arResult, array $arParams): void
    {
        global $APPLICATION, $USER;

        foreach ($arFieldCodes as $fieldCode) {
            $isRequired = in_array($fieldCode, $arParams['USER_FIELDS_REQUIRED']);
            $fieldName = Loc::getMessage("FIELD_{$fieldCode}") . ($isRequired ? '*' : '');
            $fieldValue = htmlspecialchars($arResult['USER'][$fieldCode]);
            switch ($fieldCode) {
                case 'LAST_NAME':
                case 'NAME':
                case 'SECOND_NAME':
                    ?>
                    <div class="input">
                        <input type="text"
                            id="<?=$fieldCode?>"
                            name="<?=$fieldCode?>"
                            value="<?=$fieldValue?>"
                            <?=($isRequired ? 'required' : '')?>
                        />
                        <label class="input__label" for="<?=$fieldCode?>"><?=$fieldName?></label>
                        <div class="input__bar"></div>
                    </div>
                    <?php
                    break;

                case 'EMAIL':
                    ?>
                    <div class="input">
                        <input type="email"
                            id="<?=$fieldCode?>"
                            name="<?=$fieldCode?>"
                            value="<?=$fieldValue?>"
                            <?=($isRequired ? 'required' : '')?>
                        />
                        <label class="input__label" for="<?=$fieldCode?>"><?=$fieldName?></label>
                        <div class="input__bar"></div>
                    </div>
                    <?php
                    break;
            }
        }

        ?>
        <div class="profile__form-row">
            <?php
            foreach ($arFieldCodes as $fieldCode) {
                $isRequired = in_array($fieldCode, $arParams['USER_FIELDS_REQUIRED']);
                $fieldName = Loc::getMessage("FIELD_{$fieldCode}") . ($isRequired ? '*' : '');
                $fieldValue = htmlspecialchars($arResult['USER'][$fieldCode]);
                switch ($fieldCode) {
                    case 'PERSONAL_BIRTHDAY':
                        ?>
                        <div class="input">
                            <div class="input__date">
                                <div class="input__date-label" for="<?=$fieldCode?>"><?=$fieldName?></div>
                                <input
                                    class="js-input-date"
                                    type="text"
                                    id="<?=$fieldCode?>"
                                    name="<?=$fieldCode?>"
                                    value="<?=$fieldValue?>"
                                    <?=($isRequired ? 'required' : '')?>
                                />
                            </div>
                            <div class="input__bar"></div>
                        </div>
                        <?php
                        break;

                    case 'PERSONAL_GENDER':
                        ?>
                        <div class="input">
                            <div class="input__gender">
                                <div class="input__gender-label"><?=$fieldName?></div>
                                <div class="input__gender-options">
                                    <div class="radio">
                                        <input
                                            class="radio__input"
                                            type="radio"
                                            id="<?=$fieldCode?>_MALE"
                                            value="M"
                                            name="<?=$fieldCode?>"
                                            <?=($arResult['USER']['PERSONAL_GENDER'] == 'M' ? 'checked' : '')?>
                                        />
                                        <label class="radio__label" for="<?=$fieldCode?>_MALE">
                                            <?=Loc::getMessage('PERSONAL_GENDER_MALE')?>
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <input
                                            class="radio__input"
                                            type="radio"
                                            id="<?=$fieldCode?>_FEMALE"
                                            value="F"
                                            name="<?=$fieldCode?>"
                                            <?=($arResult['USER']['PERSONAL_GENDER'] == 'F' ? 'checked' : '')?>
                                        />
                                        <label class="radio__label" for="<?=$fieldCode?>_FEMALE">
                                            <?=Loc::getMessage('PERSONAL_GENDER_FEMALE')?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="input__bar"></div>
                        </div>
                        <?php
                        break;
                }
            }
            ?>
        </div>
        <?php

        foreach ($arFieldCodes as $fieldCode) {
            $isRequired = in_array($fieldCode, $arParams['USER_FIELDS_REQUIRED']);
            $fieldName = Loc::getMessage("FIELD_{$fieldCode}") . ($isRequired ? '*' : '');
            $fieldValue = htmlspecialchars($arResult['USER'][$fieldCode]);
            switch ($fieldCode) {
                case 'PERSONAL_PHONE':
                    $userPhoneNumber = UserTable::getById(intval($USER->GetID()))->fetch()['PERSONAL_PHONE'];
                    $APPLICATION->IncludeComponent(
                        'its.agency:phone.check',
                        '',
                        [
                            'DEV_MODE' => 'N',
                            'PLACEHOLDER' => $fieldName,
                            'USER_PHONES' => [$userPhoneNumber],
                            'FIELD_NAME' => $fieldCode,
                            'FIELD_VALUE' => $fieldValue,
                            'FIELD_VALUE_COUNTRY' => '',
                            'CONFIRM_CODE_LENGTH' => 4,
                            'RESEND_LIMIT' => 30,
                            'SMS_EVENT_CODE' => 'SMS_USER_CONFIRM_NUMBER',
                        ],
                        false,
                        ['HIDE_ICONS' => 'Y']
                    );
                    break;
            }
        }
    }
}

?>
<form class="profile__form" action="<?=$arResult['FORM_TARGET']?>" method="post" enctype="multipart/form-data">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <input type="hidden" name="ID" value=<?=$arResult['ID']?>>

    <?php
    if (!empty($arResult['ERRORS'])) {
        foreach ($arResult['ERRORS'] as $code => &$message) {
            $message = str_replace('#FIELD#', Loc::getMessage("FIELD_{$code}"), $message);
        }
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                showMessage({
                    title: <?=json_encode(Loc::getMessage('PROFILE_DATA_ERROR'))?>,
                    text: <?=json_encode(implode('<br>', $arResult['ERRORS']))?>,
                    time: 10000,
                });
            });
        </script>
        <?php
    }

    if ($arResult['DATA_SAVED'] == 'Y') {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                showMessage({
                    title: <?=json_encode(Loc::getMessage('PROFILE_DATA_SUCCESS'))?>,
                    text: <?=json_encode(Loc::getMessage('PROFILE_DATA_SAVED'))?>,
                    time: 30000,
                });
            });
        </script>
        <?php
    }
    ?>

    <?php PrintUserFields($arParams['USER_FIELDS'], $arResult, $arParams)?>

    <?php $APPLICATION->IncludeComponent(
        'its.maxma:user.info',
        '',
        [
            'USER_ID' => $USER->GetID(),
        ],
        false,
        ['HIDE_ICONS' => 'Y']
    )?>

    <button class="button-black profile__form_submit" type="submit" name="save_profile" value="Y">
        <?=Loc::getMessage('PROFILE_SAVE')?>
    </button>
</form>
