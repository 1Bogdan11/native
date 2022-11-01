<?php

use Bitrix\Main\Loader;
use Its\Maxma\Api\Maxma;
use Its\Maxma\Api\Tool;
use Bitrix\Sale\Basket;
use Bitrix\Currency\CurrencyManager;
use Bitrix\Sale\BasketItemBase;

class ItsMaxmaProductInfoComponent extends \CBitrixComponent
{
    public function executeComponent()
    {
        if (
            !Loader::includeModule('its.maxma')
            || !Loader::includeModule('iblock')
            || !Loader::includeModule('catalog')
            || !Loader::includeModule('sale')
        ) {
            return;
        }

        $this->arResult['PRODUCT_ID'] = intval($this->arParams['PRODUCT_ID']);

        if ($this->arResult['PRODUCT_ID'] <= 0) {
            return;
        }

        $productIds = [];
        if (!\CCatalogSku::IsExistOffers($this->arResult['PRODUCT_ID'])) {
            $this->arResult['HAVE_OFFERS'] = 'N';
            $productIds[] = $this->arResult['PRODUCT_ID'];
        } else {
            $this->arResult['HAVE_OFFERS'] = 'Y';
            $productIds = array_column(\CCatalogSKU::getOffersList(
                [$this->arResult['PRODUCT_ID']],
                0,
                ['ACTIVE' => 'Y'],
                ['ID'],
                []
            )[$this->arResult['PRODUCT_ID']], 'ID');
        }


        $maxma = Maxma::getInstance();
        $basket = Basket::create(SITE_ID);
        foreach ($productIds as $productId) {
            $basket->createItem('catalog', $productId)->setFields([
                'QUANTITY' => 1,
                'CURRENCY' => CurrencyManager::getBaseCurrency(),
                'LID' => SITE_ID,
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
            ]);
        }
        $result = $maxma->calculateOrder($basket, 0, '', 0, Tool::getDefaultPhone())->getData();
        $this->arResult['ROWS'] = [];
        foreach ($basket->getBasketItems() as $basketItem) {
            /** @var BasketItemBase $basketItem */
            $this->arResult['ROWS'][$basketItem->getProductId()] = $result['ROWS'][$basketItem->getBasketCode()];
        }

        $this->includeComponentTemplate();
    }
}
