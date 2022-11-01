<?php

namespace Journal;

use Bitrix\Catalog\Product\PropertyCatalogFeature;
use Its\Library\Iblock\Iblock;
use Bitrix\Main\Loader;

class Analytics
{
    protected static function getCatalogId(): int
    {
        return Iblock::getInstance()->get('catalog');
    }

    public static function getGa4Item(
        int $id,
        ?float $quantity = null,
        ?int $priceId = null,
        ?float $price = null,
        ?float $discount = null
    ): array {
        $data = static::getItemData($id, $priceId);
        $item = [
            'item_id' => $data['ID'],
            'item_name' => $data['NAME'],
        ];

        foreach (array_values($data['SECTION_CHAIN']) as $i => $category) {
            $i = $i > 0 ? $i : '';
            $item["item_category{$i}"] = $category;
        }

        if (!empty($data['OFFERS']) && is_array($data['OFFERS'][$data['OFFER_SELECTED_ID']])) {
            $item['item_variant'] = static::getOfferStringVariant($data['OFFERS'][$data['OFFER_SELECTED_ID']]);
        }

        $item['quantity'] = $quantity ?? 1;
        $item['price'] = $price ?? $data['PRICE'] ?? 0;
        $item['discount'] = $discount ?? $data['DISCOUNT'] ?? 0;
        $item['currency'] = $data['CURRENCY'];

        return $item;
    }

    protected static function getOfferStringVariant(array $offer, array $propsFilter = []): string
    {
        $values = ["ID-{$offer['ID']}"];
        foreach ($offer['PROPERTIES'] as $property) {
            $value = $property['VALUE'];

            if (
                ($propsFilter && !in_array($property['CODE'], $propsFilter))
                || empty($value)
                || !is_string($value)
            ) {
                continue;
            }
            $values[] = $value;
        }
        return implode(', ', $values);
    }

    protected static function getItemData(int $itemId, int $priceId = null): array
    {
        global $USER;

        Loader::includeModule('iblock');
        Loader::includeModule('catalog');
        Loader::includeModule('sale');

        $cacheId = ['item', $itemId, $priceId, $USER->GetGroups()];
        $cache = new \CPHPCache();

        if ($cache->initCache(36000, serialize($cacheId), '/catalog/ga4')) {
            $data = $cache->getVars();
        } else {
            $cache->startDataCache();

            $data = [];
            $price = 0.0;
            $discount = 0.0;
            $currency = 'RUB';

            if (!$priceId) {
                $arGroup = \CCatalogGroup::GetList(
                    ['SORT' => 'ASC'],
                    ['BASE' => 'Y']
                )->Fetch();
                $priceId = intval($arGroup['ID']);
            }

            $resPrice = \CPrice::GetList(
                ['QUANTITY_FROM' => 'ASC', 'QUANTITY_TO' => 'ASC', 'SORT' => 'ASC'],
                [
                    'PRODUCT_ID' => $itemId,
                    'CATALOG_GROUP_ID' => $priceId,
                ],
                false,
                false,
                ['ID', 'PRICE', 'CURRENCY']
            );
            if ($arPrice = $resPrice->Fetch()) {
                $arDiscounts = \CCatalogDiscount::GetDiscountByPrice(
                    $arPrice['ID'],
                    $USER->GetUserGroupArray(),
                    'N',
                    SITE_ID
                );
                $discountPrice = \CCatalogProduct::CountPriceWithDiscount(
                    $arPrice['PRICE'],
                    $arPrice['CURRENCY'],
                    $arDiscounts
                );
                $currency = $arPrice['CURRENCY'];
                $price = round(floatval($discountPrice), 2);
                $discount = round(floatval($arPrice['PRICE']) - $price, 2);
            }

            $productInfo = \CCatalogSku::GetProductInfo($itemId);
            if (empty($productInfo)) {
                $resElements = \CIBlockElement::GetList(
                    ['ID' => 'ASC'],
                    ['ID' => $itemId],
                    false,
                    false,
                    ['ID', 'IBLOCK_ID', 'NAME', 'IBLOCK_SECTION_ID', 'PROPERTY_BRAND.NAME']
                );

                if ($objElement = $resElements->GetNextElement()) {
                    $data = $objElement->GetFields();
                }
            } else {
                $resParentElements = \CIBlockElement::GetList(
                    ['ID' => 'ASC'],
                    ['ID' => $productInfo['ID'], 'IBLOCK_ID' => $productInfo['IBLOCK_ID']],
                    false,
                    false,
                    ['ID', 'IBLOCK_ID', 'NAME', 'IBLOCK_SECTION_ID', 'PROPERTY_BRAND.NAME']
                );

                if ($objParentElement = $resParentElements->GetNextElement()) {
                    $data = $objParentElement->GetFields();
                    $data['OFFER_SELECTED_ID'] = $itemId;
                    $arOfferPropertyCodes = PropertyCatalogFeature::getOfferTreePropertyCodes(
                        $productInfo['OFFER_IBLOCK_ID'],
                        ['CODE' => 'Y']
                    );
                    $data['OFFERS'] = \CCatalogSKU::getOffersList(
                        [$data['ID']],
                        0,
                        [],
                        ['ID', 'IBLOCK_ID', 'NAME'],
                        ['CODE' => is_array($arOfferPropertyCodes) ? $arOfferPropertyCodes : false]
                    )[$data['ID']];
                }
            }

            if ($data) {
                $data['PRICE'] = $price;
                $data['DISCOUNT'] = $discount;
                $data['CURRENCY'] = $currency;

                $resChain = \CIBlockSection::GetNavChain(
                    $data['IBLOCK_ID'],
                    $data['IBLOCK_SECTION_ID'],
                );
                $arChain = [];
                while ($arItem = $resChain->Fetch()) {
                    $arChain[] = $arItem['NAME'];
                }

                $data['SECTION_CHAIN'] = $arChain;
            }

            if (defined('BX_COMP_MANAGED_CACHE')) {
                global $CACHE_MANAGER;
                $CACHE_MANAGER->StartTagCache('/catalog/ga4');
                $CACHE_MANAGER->RegisterTag('iblock_id_' . static::getCatalogId());
                $CACHE_MANAGER->EndTagCache();
            }
            $cache->EndDataCache($data);
        }

        return $data;
    }
}
