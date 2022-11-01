<?php

use Bitrix\Main\Loader;
use Bitrix\Main\SiteTable;
use Bitrix\Main\Localization\LanguageTable;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Sale\Internals\SiteCurrencyTable;

class SiteSelectorComponent extends \CBitrixComponent
{
    public function executeComponent()
    {
        Loader::includeModule('sale');

        $resSites = SiteTable::getList([
            'order' => ['SORT' => 'ASC'],
            'filter' => ['ACTIVE' => 'Y'],
            'runtime' => [
                new ReferenceField(
                    'LANGUAGE',
                    LanguageTable::class,
                    ['=this.LANGUAGE_ID' => 'ref.LID'],
                    ['join_type' => 'LEFT']
                ),
                new ReferenceField(
                    'CURRENCY',
                    SiteCurrencyTable::class,
                    ['=this.LID' => 'ref.LID'],
                    ['join_type' => 'LEFT']
                ),
            ],
            'select' => [
                'ID' => 'LID',
                'PATH' => 'DIR',
                'LANGUAGE_ID',
                'LANGUAGE_NAME' => 'LANGUAGE.NAME',
                'CURRENCY_ID' => 'CURRENCY.CURRENCY'
            ],
        ]);

        $this->arResult['SITES'] = [];
        $this->arResult['SITE_SELECTED'] = 0;

        while ($arSite = $resSites->fetch()) {
            $format = \CCurrencyLang::getFormatDescription($arSite['CURRENCY_ID']);
            $format['DECIMALS'] = 0;
            $format['HIDE_ZERO'] = 'Y';
            $arSite['CURRENCY_SYMBOL'] = trim(\CCurrencyLang::formatValue(0, $format), ' 0');
            if ($arSite['ID'] === SITE_ID) {
                $this->arResult['SITE_SELECTED'] = count($this->arResult['SITES']);
            }
            $this->arResult['SITES'][] = $arSite;
        }

        $this->includeComponentTemplate();
    }
}
