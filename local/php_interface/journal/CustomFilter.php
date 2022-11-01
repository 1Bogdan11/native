<?php

namespace Journal;

use Its\Library\Iblock\Iblock;
use Bitrix\Iblock\InheritedPropertyTable;
use Bitrix\Iblock\PropertyTable;

class CustomFilter
{
    protected const IBLOCK_CATALOG_CODE = 'catalog';
    protected const IBLOCK_SET_CODE = 'set';
    protected const IBLOCK_SIZES_CODE = 'sizes';

    protected const PROPERTY_IN_SET_CODE = 'FILTER_IN_SET';
    protected const PROPERTY_SIZE_IN_MODEL_CODE = 'FILTER_SIZE_IN_MODEL';
    protected const PROPERTY_LONG_NAME_CODE = 'FILTER_LONG_NAME';

    protected static array $lastProperties = [];

    protected static array $propertyValueCache = [];

    protected static function getIblockCatalogId(): int
    {
        return Iblock::getInstance()->get(static::IBLOCK_CATALOG_CODE, 's1');
    }

    protected static function getIblockSetId(): int
    {
        return Iblock::getInstance()->get(static::IBLOCK_SET_CODE, 's1');
    }

    protected static function getIblockSizesId(): int
    {
        return Iblock::getInstance()->get(static::IBLOCK_SIZES_CODE, 's1');
    }

    public static function afterElementAdd(&$params): bool
    {
        if (intval($params['ID']) > 0) {
            return static::afterElementModify($params);
        }
        return true;
    }

    public static function beforeElementUpdate(&$params): bool
    {
        if (
            intval($params['IBLOCK_ID']) === static::getIblockSetId()
            || intval($params['IBLOCK_ID']) === static::getIblockSizesId()
        ) {
            static::$lastProperties = \CIBlockElement::GetByID($params['ID'])->GetNextElement()->GetProperties();
        }
        return true;
    }

    public static function afterElementUpdate(&$params): bool
    {
        if ($params['RESULT']) {
            return static::afterElementModify($params);
        }

        static::$lastProperties = [];
        return true;
    }

    public static function beforeElementDelete(&$params): bool
    {
        $element = \CIBlockElement::GetByID(intval($params))->GetNextElement();
        $elementData = $element->GetFields();

        if (
            intval($elementData['IBLOCK_ID']) === static::getIblockSetId()
            || intval($elementData['IBLOCK_ID']) === static::getIblockSizesId()
        ) {
            static::$lastProperties = $element->GetProperties();
        }
        return true;
    }

    public static function afterElementDelete(&$params): bool
    {
        if (intval($params['IBLOCK_ID']) === static::getIblockSetId()) {
            foreach (static::$lastProperties['ITEMS']['VALUE'] as $elementId) {
                static::updateFilterProperties(intval($elementId));
            }
        }
        if (intval($params['IBLOCK_ID']) === static::getIblockSizesId()) {
            static::updateFilterProperties(intval(static::$lastProperties['ITEM']['VALUE']));
        }

        static::$lastProperties = [];
        return true;
    }

    protected static function afterElementModify(&$params): bool
    {
        if (intval($params['IBLOCK_ID']) === static::getIblockCatalogId()) {
            static::updateFilterProperties(intval($params['ID']));
            return true;
        }

        if (intval($params['IBLOCK_ID']) === static::getIblockSetId()) {
            $properties = \CIBlockElement::GetByID($params['ID'])->GetNextElement()->GetProperties();
            $values = $properties['ITEMS']['VALUE'];
            if (is_array(static::$lastProperties['ITEMS']['VALUE'])) {
                $values = array_merge($values, static::$lastProperties['ITEMS']['VALUE']);
            }
            foreach (array_unique($values) as $value) {
                static::updateFilterProperties(intval($value));
            }
            return true;
        }

        if (intval($params['IBLOCK_ID']) === static::getIblockSizesId()) {
            $properties = \CIBlockElement::GetByID($params['ID'])->GetNextElement()->GetProperties();
            $values = [
                $properties['ITEM']['VALUE'],
                static::$lastProperties['ITEM']['VALUE'],
            ];
            foreach (array_unique($values) as $value) {
                static::updateFilterProperties(intval($value));
            }
            return true;
        }

        return true;
    }

    protected static function updateFilterProperties(int $elementId): void
    {
        if ($elementId <= 0) {
            return;
        }

        $checkLongName = boolval(InheritedPropertyTable::getList([
            'filter' => [
                '=IBLOCK_ID' => static::getIblockCatalogId(),
                '=ENTITY_ID' => $elementId,
                '!TEMPLATE' => false,
            ]
        ])->getSelectedRowsCount());

        $checkSet = boolval(\CIBlockElement::GetList(
            ['ID' => 'DESC'],
            [
                'IBLOCK_ID' => static::getIblockSetId(),
                'PROPERTY_ITEMS' => $elementId,
            ],
            false,
            false,
            ['ID', 'IBLOCK_ID']
        )->SelectedRowsCount());

        $checkSize = boolval(\CIBlockElement::GetList(
            ['ID' => 'DESC'],
            [
                'IBLOCK_ID' => static::getIblockSizesId(),
                'PROPERTY_ITEM' => $elementId,
            ],
            false,
            false,
            ['ID', 'IBLOCK_ID']
        )->SelectedRowsCount());

        $values = [
            static::PROPERTY_IN_SET_CODE => $checkSet ? 'Y' : 'N',
            static::PROPERTY_SIZE_IN_MODEL_CODE => $checkSize ? 'Y' : 'N',
            static::PROPERTY_LONG_NAME_CODE => $checkLongName ? 'Y'  : 'N',
        ];

        foreach ($values as $propertyCode => &$value) {
            if (!static::$propertyValueCache[$propertyCode . $value]) {
                $propValueData = \CIBlockProperty::GetPropertyEnum(
                    $propertyCode,
                    [],
                    [
                        'IBLOCK_ID' => static::getIblockCatalogId(),
                        'EXTERNAL_ID' => $value
                    ]
                )->Fetch();
                static::$propertyValueCache[$propertyCode . $value] = $propValueData['ID'];
            }
            $value = intval(static::$propertyValueCache[$propertyCode . $value]);
        }

        \CIBlockElement::SetPropertyValuesEx($elementId, static::getIblockCatalogId(), $values);
    }
}
