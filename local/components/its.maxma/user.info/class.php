<?php

use Bitrix\Main\Loader;
use Its\Maxma\Api\Maxma;

class ItsMaxmaUserInfoComponent extends \CBitrixComponent
{
    public function executeComponent()
    {
        if (!Loader::includeModule('its.maxma')) {
            return;
        }

        $maxma = Maxma::getInstance();
        $this->arResult['USER_ID'] = intval($this->arParams['USER_ID']);
        $result = $maxma->getUser($this->arResult['USER_ID']);

        if (!$result->isSuccess()) {
            $maxma->addUser($this->arResult['USER_ID'], \CUser::GetByID($this->arResult['USER_ID'])->Fetch());
            $result = $maxma->getUser($this->arResult['USER_ID']);
        }

        $this->arResult['USER'] = $result->getData();
        $this->arResult['ERRORS'] = !$result->isSuccess() ? $result->getErrors() : [];

        $this->includeComponentTemplate();
    }
}
