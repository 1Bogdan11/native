<?php

namespace Journal;

use Bitrix\Main\Context;
use Bitrix\Main\HttpRequest;
use Its\Library\Iblock\Iblock;
use Bitrix\Iblock\PropertyTable;

class Import1C
{
    protected const IBLOCK_CATALOG_CODE = 'catalog';

    protected static ?HttpRequest $request = null;
    protected static int $catalogId = 0;
    protected static int $catalogOfferId = 0;
    protected static array $propertyIds = [];

    public static function getRequest(): HttpRequest
    {
        if (!static::$request) {
            static::$request = Context::getCurrent()->getRequest();
        }
        return static::$request;
    }

    protected static function getCatalogId(): int
    {
        if (static::$catalogId <= 0) {
            $iblocks = Iblock::getInstance()->getAll(static::IBLOCK_CATALOG_CODE);
            static::$catalogId = intval(array_shift($iblocks));
        }
        return static::$catalogId;
    }

    protected static function getCatalogOfferId(): int
    {
        if (static::$catalogOfferId <= 0) {
            $info = \CCatalogSKU::GetInfoByProductIBlock(static::getCatalogId());
            static::$catalogOfferId = intval($info['IBLOCK_ID']);
        }
        return static::$catalogOfferId;
    }

    protected static function getPropertyId(string $code, bool $offer = false): int
    {
        $iblockId = $offer ? static::getCatalogOfferId() : static::getCatalogId();
        $key = "{$iblockId}_{$code}";
        if (!isset(static::$propertyIds[$key])) {
            $arProperty = PropertyTable::getList([
                'filter' => [
                    '=IBLOCK_ID' => $iblockId,
                    '=CODE' => $code,
                ],
                'select' => ['ID'],
            ])->fetch();
            if ($arProperty) {
                static::$propertyIds[$key] = intval($arProperty['ID']);
            }
        }
        return intval(static::$propertyIds[$key]);
    }

    public static function beforePropertyUpdate(&$arFields): bool
    {
        $mode = static::getRequest()->get('mode');
        if ($mode !== 'import') {
            return true;
        }

        $id = intval($arFields['ID']);
        $iblockId = intval($arFields['IBLOCK_ID']);

        if ($iblockId === 0 && $id > 0) {
            $propertyData = \CIBlockProperty::GetByID($id)->Fetch();
            $iblockId = intval($propertyData['IBLOCK_ID']);
        }

        if (!in_array($iblockId, [static::getCatalogId(), static::getCatalogOfferId()])) {
            return true;
        }

        unset(
            $arFields['NAME'],
            $arFields['CODE'],
        );

        return true;
    }

    public static function beforeElementUpdateAction(&$arFields): bool
    {
        $mode = static::getRequest()->get('mode');
        if ($mode !== 'import') {
            return true;
        }

        if (in_array(intval($arFields['IBLOCK_ID']), [static::getCatalogId(), static::getCatalogOfferId()])) {
            unset(
                $arFields['NAME'],
                $arFields['PREVIEW_TEXT'],
                $arFields['DETAIL_TEXT'],
                $arFields['PREVIEW_PICTURE'],
                $arFields['DETAIL_PICTURE'],
                $arFields['PROPERTY_VALUES'][static::getPropertyId('MORE_PHOTO')],
                $arFields['PROPERTY_VALUES'][static::getPropertyId('MORE_PHOTO', true)]
            );
        }

        return true;
    }
}
