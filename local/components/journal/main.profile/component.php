<?php

/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @global CUserTypeManager $USER_FIELD_MANAGER
 * @var array $arParams
 * @var CBitrixComponent $this
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(false);
$arResult['ID'] = intval($USER->GetID());

if (!($arParams['CHECK_RIGHTS'] !== 'Y' || $USER->CanDoOperation('edit_own_profile')) || $arResult['ID'] <= 0) {
    return;
}

/*

$arResult["GROUP_POLICY"] = CUser::GetGroupPolicy($arResult["ID"]);
$arParams['SEND_INFO'] = $arParams['SEND_INFO'] == 'Y' ? 'Y' : 'N';

$arResult["PHONE_REGISTRATION"] = (COption::GetOptionString("main", "new_user_phone_auth", "N") == "Y");
$arResult["PHONE_REQUIRED"] = ($arResult["PHONE_REGISTRATION"] && COption::GetOptionString("main", "new_user_phone_required", "N") == "Y");
$arResult["EMAIL_REGISTRATION"] = (COption::GetOptionString("main", "new_user_email_auth", "Y") <> "N");
$arResult["EMAIL_REQUIRED"] = ($arResult["EMAIL_REGISTRATION"] && COption::GetOptionString("main", "new_user_email_required", "Y") <> "N");
$arResult["PHONE_CODE_RESEND_INTERVAL"] = CUser::PHONE_CODE_RESEND_INTERVAL;

*/

$arResult['ERRORS'] = [];
$arResult['DATA_SAVED'] = 'N';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_REQUEST['save_profile'] && check_bitrix_sessid()) {
    if (empty($arResult['ERRORS'])) {
        $user = new CUser();

        $arPERSONAL_PHOTO = $_FILES['PERSONAL_PHOTO'];
        $arWORK_LOGO = $_FILES['WORK_LOGO'];

        $arUser = CUser::GetByID($arResult['ID'])->Fetch();
        if ($arUser) {
            $arPERSONAL_PHOTO['old_file'] = $arUser['PERSONAL_PHOTO'];
            $arPERSONAL_PHOTO['del'] = $_REQUEST['PERSONAL_PHOTO_del'];
            $arWORK_LOGO['old_file'] = $arUser['WORK_LOGO'];
            $arWORK_LOGO['del'] = $_REQUEST['WORK_LOGO_del'];
        }

        $arEditFields = [
            'TITLE',
            'NAME',
            'LAST_NAME',
            'SECOND_NAME',
            'EMAIL',
            'LOGIN',
            'PERSONAL_PROFESSION',
            'PERSONAL_WWW',
            'PERSONAL_ICQ',
            'PERSONAL_GENDER',
            'PERSONAL_BIRTHDAY',
            'PERSONAL_PHONE',
            'PERSONAL_FAX',
            'PERSONAL_MOBILE',
            'PERSONAL_PAGER',
            'PERSONAL_STREET',
            'PERSONAL_MAILBOX',
            'PERSONAL_CITY',
            'PERSONAL_STATE',
            'PERSONAL_ZIP',
            'PERSONAL_COUNTRY',
            'PERSONAL_NOTES',
            'WORK_COMPANY',
            'WORK_DEPARTMENT',
            'WORK_POSITION',
            'WORK_WWW',
            'WORK_PHONE',
            'WORK_FAX',
            'WORK_PAGER',
            'WORK_STREET',
            'WORK_MAILBOX',
            'WORK_CITY',
            'WORK_STATE',
            'WORK_ZIP',
            'WORK_COUNTRY',
            'WORK_PROFILE',
            'WORK_NOTES',
            'TIME_ZONE',
        ];

        $arFields = [];
        foreach ($arEditFields as $field) {
            if (!in_array($field, $arParams['USER_FIELDS'])) {
                continue;
            }

            $requestValue = trim(strval($_REQUEST[$field]));

            if ($field === 'PERSONAL_PHONE') {
                \CBitrixComponent::includeComponentClass('its.agency:phone.check');
                $phoneNumber = \PhoneCheckComponent::getPhoneNumber($requestValue, '');

                if (!$phoneNumber->isValid()) {
                    $arResult['ERRORS'][] = Loc::getMessage('PROFILE_FIELD_PERSONAL_PHONE_VALID');
                } elseif (!\PhoneCheckComponent::checkConfirmPhoneForParentComponent($phoneNumber, '')) {
                    $arResult['ERRORS'][] = Loc::getMessage('PROFILE_FIELD_PERSONAL_PHONE_CONFIRM');
                }
            }

            if (empty($requestValue) && in_array($field, $arParams['USER_FIELDS_REQUIRED'])) {
                $arResult['ERRORS'][$field] = Loc::getMessage('PROFILE_FIELD_REQUIRED');
            }

            if ($requestValue && in_array($field, ['PERSONAL_BIRTHDAY'])) {
                $date = \DateTime::createFromFormat('d.m.Y', $requestValue);
                if ($date) {
                    $requestValue = DateTime::createFromPhp($date);
                } else {
                    $requestValue = '';
                }
            }

            $arFields[$field] = $requestValue;
        }

        if (!empty($_REQUEST['NEW_PASSWORD'])) {
            $arFields['PASSWORD'] = $_REQUEST['NEW_PASSWORD'];
            $arFields['CONFIRM_PASSWORD'] = $_REQUEST['NEW_PASSWORD_CONFIRM'];
        }

        $arFields['PERSONAL_PHOTO'] = $arPERSONAL_PHOTO;
        $arFields['WORK_LOGO'] = $arWORK_LOGO;

        if (empty($arResult['ERRORS'])) {
            if (!$user->Update($arResult['ID'], $arFields)) {
                $arResult['ERRORS'][] = $user->LAST_ERROR;
            } else {
                $arResult['DATA_SAVED'] = 'Y';
            }
        }
    }
}

$rsUser = CUser::GetByID($arResult['ID']);
if (!$arResult['USER'] = $rsUser->GetNext(false)) {
    $arResult['ID'] = 0;
}

if (!empty($arResult['ERRORS'])) {
    static $skip = ['PERSONAL_PHOTO' => 1, 'WORK_LOGO' => 1];
    foreach ($_POST as $k => $val) {
        if (!isset($skip[$k])) {
            if (!is_array($val)) {
                $val = htmlspecialcharsex($val);
            }

            $arResult['USER'][$k] = $val;
        }
    }
}

$arResult['FORM_TARGET'] = $APPLICATION->GetCurPage();

$arResult['USER']['PERSONAL_PHOTO_INPUT'] = CFile::InputFile('PERSONAL_PHOTO', 20, $arResult['USER']['PERSONAL_PHOTO'], false, 0, 'IMAGE');
if ($arResult['USER']['PERSONAL_PHOTO'] <> '') {
    $arResult['USER']['PERSONAL_PHOTO_HTML'] = CFile::ShowImage($arResult['USER']['PERSONAL_PHOTO'], 150, 150, 'border=0', '', true);
}

$arResult['USER']['WORK_LOGO_INPUT'] = CFile::InputFile('WORK_LOGO', 20, $arResult['USER']['WORK_LOGO'], false, 0, 'IMAGE');
if ($arResult['USER']['WORK_LOGO'] <> '') {
    $arResult['USER']['WORK_LOGO_HTML'] = CFile::ShowImage($arResult['USER']['WORK_LOGO'], 150, 150, 'border=0', '', true);
}

$arCountries = GetCountryArray();
$arResult['COUNTRY_SELECT'] = SelectBoxFromArray('PERSONAL_COUNTRY', $arCountries, $arResult['USER']['PERSONAL_COUNTRY'], GetMessage('USER_DONT_KNOW'));
$arResult['COUNTRY_SELECT_WORK'] = SelectBoxFromArray('WORK_COUNTRY', $arCountries, $arResult['USER']['WORK_COUNTRY'], GetMessage('USER_DONT_KNOW'));

$arResult['SOCSERV_ENABLED'] = IsModuleInstalled('socialservices');
if ($arResult['USER']['PERSONAL_BIRTHDAY']) {
    try {
        $arResult['USER']['PERSONAL_BIRTHDAY'] = (new DateTime($arResult['USER']['PERSONAL_BIRTHDAY']))->format('d.m.Y');
    } catch (\Throwable $e) {
        $arResult['USER']['PERSONAL_BIRTHDAY'] = '';
    }
}

$this->IncludeComponentTemplate();
