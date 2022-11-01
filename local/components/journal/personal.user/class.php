<?php

use Bitrix\Main\UserTable;

class PersonalUserComponent extends \CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;

        if (!$USER->IsAuthorized()) {
            return;
        }

        $this->arResult = UserTable::getById(intval($USER->GetID()))->fetch();
        $this->arResult['FORMATTED_NAME'] = \CUser::FormatName(
            \CSite::GetNameFormat(),
            $this->arResult
        );

        $this->includeComponentTemplate();
    }
}
