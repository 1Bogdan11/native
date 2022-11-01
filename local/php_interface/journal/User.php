<?php

namespace Journal;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;
use Bitrix\Main\UserTable;

Loc::loadMessages(__FILE__);

class User
{
    public static function beforeUserSaveAction(&$arFields): bool
    {
        global $APPLICATION;

        if (isset($arFields['PERSONAL_PHONE']) && !empty($arFields['PERSONAL_PHONE'])) {
            $phoneNumber = Parser::getInstance()->parse(strval($arFields['PERSONAL_PHONE']), '');
            if (!$phoneNumber->isValid()) {
                $APPLICATION->ThrowException(Loc::getMessage('PHP_INTERFACE_USER_CLASS_INVALID_PHONE_NUMBER'));
                return false;
            }
            $arFields['PERSONAL_PHONE'] = $phoneNumber->format(Format::E164);

            $checkUsersFilter = [
                'PERSONAL_PHONE' => $arFields['PERSONAL_PHONE'],
            ];

            if (intval($arFields['ID']) > 0) {
                $checkUsersFilter['!ID'] = intval($arFields['ID']);
            }

            $checkUsers = UserTable::getList([
                'filter' => $checkUsersFilter,
                'select' => ['ID']
            ])->fetch();

            if ($checkUsers) {
                $APPLICATION->ThrowException(Loc::getMessage('PHP_INTERFACE_USER_CLASS_UNIQUE_PHONE_NUMBER'));
                return false;
            }
        }

        return true;
    }

    public static function afterUserLogout($arParams): bool
    {
        \CBitrixComponent::includeComponentClass('its.agency:phone.auth');
        \PhoneAuthComponent::onAfterUserLogoutActions();
        return true;
    }
}
