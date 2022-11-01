<?php

namespace Journal;

use Its\Library\Iblock\Iblock;
use Bitrix\Main\Loader;
use Bitrix\Catalog\Product\PropertyCatalogFeature;

class PicturePinHandler
{
    protected const COLOR_PROPERTY_CODE = 'TSVET';
    protected const PINS_PROPERTY_CODE = 'DETAIL_PICTURE_PINS';

    public static function afterElementUpdate(&$params): bool
    {
        Loader::includeModule('sale');
        Loader::includeModule('catalog');

        if ($params['RESULT'] && in_array($params['IBLOCK_ID'], Iblock::getInstance()->getAll('offers'))) {
            static::updateOffers(intval($params['ID']));
        }
        return true;
    }

    protected static function updateOffers(int $id): void
    {
        $productInfo = \CCatalogSku::GetProductInfo($id);
        if (!$productInfo) {
            return;
        }

        $offerPropertyCodes = PropertyCatalogFeature::getOfferTreePropertyCodes(
            $productInfo['OFFER_IBLOCK_ID'],
            ['CODE' => 'Y']
        );
        if (!is_array($offerPropertyCodes) || !in_array(static::COLOR_PROPERTY_CODE, $offerPropertyCodes)) {
            return;
        }

        $offers = \CCatalogSKU::getOffersList(
            [$productInfo['ID']],
            0,
            [],
            ['ID', 'IBLOCK_ID', 'NAME'],
            ['CODE' => [static::COLOR_PROPERTY_CODE, static::PINS_PROPERTY_CODE]]
        )[$productInfo['ID']];

        $color = '';
        $pins = '';
        foreach ($offers as $offerData) {
            if ($offerData['ID'] == $id) {
                $color = $offerData['PROPERTIES'][static::COLOR_PROPERTY_CODE]['VALUE'];
                $pins = $offerData['PROPERTIES'][static::PINS_PROPERTY_CODE]['~VALUE'];
                break;
            }
        }
        foreach ($offers as $offerData) {
            if ($offerData['ID'] == $id) {
                continue;
            }

            if ($offerData['PROPERTIES'][static::COLOR_PROPERTY_CODE]['VALUE'] === $color) {
                \CIBlockElement::SetPropertyValuesEx(
                    $offerData['ID'],
                    $offerData['IBLOCK_ID'],
                    [static::PINS_PROPERTY_CODE => $pins]
                );
            }
        }
    }
}
