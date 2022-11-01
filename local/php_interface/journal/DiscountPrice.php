<?php

namespace Journal;

use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;

class DiscountPrice
{
    private int $iblockId = 0;
    private string $pricePropertyCode = '';
    private array $parentPrice = [];

    public function __construct(int $iblockId, string $pricePropertyCode)
    {
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');
        Loader::includeModule('sale');

        $this->iblockId = $iblockId;
        $this->pricePropertyCode = $pricePropertyCode;
    }

    public function calculate(): void
    {
        $resource = ElementTable::getList([
            'filter' => [
                '=ACTIVE' => 'Y',
                '=IBLOCK_ID' => $this->iblockId
            ],
            'select' => ['ID', 'IBLOCK_ID'],
        ]);
        while ($elementData = $resource->fetch()) {
            $this->calculateElement($elementData);
        }
    }

    private function calculateElement(array $elementData): void
    {
        $priceData = \CCatalogProduct::GetOptimalPrice($elementData['ID']);
        $discountPrice = floatval($priceData['RESULT_PRICE']['DISCOUNT_PRICE']);
        \CIBlockElement::SetPropertyValuesEx(
            $elementData['ID'],
            $elementData['IBLOCK_ID'],
            [$this->pricePropertyCode => $discountPrice]
        );

        $parentData = \CCatalogSku::GetProductInfo($elementData['ID']);
        if (
            !empty($parentData)
            && (
                !isset($this->parentPrice[$parentData['ID']])
                || $this->parentPrice[$parentData['ID']] > $discountPrice
            )
        ) {
            $this->parentPrice[$parentData['ID']] = $discountPrice;
            \CIBlockElement::SetPropertyValuesEx(
                $parentData['ID'],
                $parentData['IBLOCK_ID'],
                [$this->pricePropertyCode => $discountPrice]
            );
        }
    }
}
