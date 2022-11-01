<?php

use Bitrix\Main\Loader;

class CatalogItemComponent extends \CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        $this->arResult = $arParams['ITEM'];
        unset($arParams['ITEM']);

        return $arParams;
    }

    public function executeComponent()
    {
        if (!Loader::includeModule('iblock')) {
            return;
        }

        $this->includeComponentTemplate();
    }
}
