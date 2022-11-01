<?php

namespace Journal\Collection;

use Bitrix\Main\Localization\Loc;
use Its\Library\Iblock\Iblock;

Loc::loadMessages(__FILE__);

class InCollectionCondition
{
    public static function getConditionDescription(): array
    {
        return [
            'ID' => static::getConditionId(),
            'SORT' => 100,
            'GROUP' => 'N',
            'Parse' => [__CLASS__, 'parse'],
            'Generate' => [__CLASS__, 'getConditionString'],
            'InitParams' => [__CLASS__, 'init'],
            'ApplyValues' => [__CLASS__, 'apply'],
            'GetControlShow' => [__CLASS__, 'getControlParams'],
            'GetConditionShow' => [__CLASS__, 'getConditionData'],
        ];
    }

    public static function getConditionId(): string
    {
        return 'DiscountInCollectionCondition';
    }

    public static function init(array $params): void
    {
    }

    public static function parse(array $params)
    {
        return $params;
    }

    public static function apply($condition, $control): array
    {
        return [];
    }

    public static function getConditionData(array $params)
    {
        if (!isset($params['ID']) || !isset($params['DATA'])) {
            return false;
        }

        $control = static::getControl();
        return \CGlobalCondCtrl::Check($params['DATA'], $params, $control, true);
    }

    public static function getControl(): array
    {
        $collections = [];
        $params = static::getCollectionParams();
        $resource = \CIBlockElement::GetList(
            [
                'SORT' => 'ASC',
                'ID' => 'ASC',
            ],
            [
                'IBLOCK_ID' => $params['IBLOCK_ID'],
                'ACTIVE' => 'Y',
                'ACTIVE_DATE' => 'Y'
            ],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'NAME']
        );
        while ($collectionData = $resource->Fetch()) {
            $collections[$collectionData['ID']] = "[{$collectionData['ID']}] {$collectionData['NAME']}";
        }
        return [
            'ID' => static::getConditionId(),
            'FIELD' => 'SET',
            'FIELD_TYPE' => 'int',
            'LOGIC' => \CGlobalCondCtrl::getLogic([BT_COND_LOGIC_EQ]),
            'JS_VALUE' => [
                'type' => 'select',
                'values' => $collections,
                'defaultText' => current($collections),
                'defaultValue' => key($collections)
            ],
            'PHP_VALUE' => '',
        ];
    }

    public static function getControlParams(array $params): array
    {
        $control = static::getControl();
        $return = [
            'controlId' => static::getConditionId(),
            'group' => false,
            'label' => Loc::getMessage('DISCOUNT_IN_SET_NAME'),
            'showIn' => [DiscountAction::getActionId()],
            'control' => [
                [
                    'id' => 'prefix',
                    'type' => 'prefix',
                    'text' => Loc::getMessage('DISCOUNT_IN_SET_CONDITIONAL_IN'),
                ],
                Loc::getMessage('DISCOUNT_IN_SET_CONDITIONAL_AND'),
                [
                    'id' => 'prefix',
                    'type' => 'prefix',
                    'text' => Loc::getMessage('DISCOUNT_IN_SET_CONDITIONAL_UNITY'),
                ],
                Loc::getMessage('DISCOUNT_IN_SET_CONDITIONAL_AND'),
                [
                    'id' => 'prefix',
                    'type' => 'prefix',
                    'text' => Loc::getMessage('DISCOUNT_IN_SET_CONDITIONAL_SET'),
                ],
                \CGlobalCondCtrl::getLogicAtom($control['LOGIC']),
                \CGlobalCondCtrl::getValueAtom($control['JS_VALUE']),
            ],
        ];

        return $return;
    }

    public static function getConditionString($condition, $params, $control, $child = []): string
    {
        return __CLASS__
            . "::isApplyProductDiscount({$params['BASKET']}, {$params['ORDER']}, {$params['BASKET_ROW']}, '{$condition['value']}')";
    }

    public static function isApplyProductDiscount($basket, $order, $row, $collectionId): bool
    {
        if (!is_array($order['BASKET_ITEMS']) || count($order['BASKET_ITEMS']) < 2) {
            return false;
        }

        $basketMap = [];
        $currentProductId = 0;
        $collectionId = intval($collectionId);

        foreach ($order['BASKET_ITEMS'] as $basketItem) {
            $productInfo = \CCatalogSku::GetProductInfo(intval($basketItem['PRODUCT_ID']));
            $productId = !empty($productInfo) ? intval($productInfo['ID']) : intval($basketItem['PRODUCT_ID']);

            for ($i = 1; $i <= intval($basketItem['QUANTITY']); $i++) {
                $basketMap[$productId][] = [
                    'COLLECTION_ID' => 0,
                    'SPLIT_ID' => intval($basketItem['QUANTITY']) > 1 ? intval($basketItem['PRODUCT_ID']) : 0,
                    'ORIGINAL_PRODUCT_ID' => intval($basketItem['PRODUCT_ID']),
                ];
            }
            if (intval($basketItem['PRODUCT_ID']) === intval($row['PRODUCT_ID'])) {
                $currentProductId = $productId;
            }
        }

        $params = static::getCollectionParams();
        $resource = \CIBlockElement::GetList(
            [
                'SORT' => 'ASC',
                'ID' => 'ASC',
            ],
            [
                'IBLOCK_ID' => $params['IBLOCK_ID'],
                'ACTIVE' => 'Y',
                'ACTIVE_DATE' => 'Y'
            ],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'NAME']
        );

        $currentCollectionItems = [];

        while ($collection = $resource->getNextElement()) {
            if (count($basketMap) < 2) {
                break;
            }
            $data = $collection->getFields();
            $data['PROPERTIES'] = $collection->getProperties();
            $items = $data['PROPERTIES'][$params['PROPERTY_ITEMS_CODE']]['VALUE'];
            if (!is_array($items)) {
                continue;
            }

            foreach ($items as $key => $item) {
                $items[$key] = intval($item);
            }

            $items = array_unique($items);

            if (count($items) < 2) {
                continue;
            }

            if (count(array_intersect(array_keys($basketMap), $items)) !== count($items)) {
                // Не все товары коллекции есть в корзине
                continue;
            }

            while (true) {
                $candidates = [];
                foreach ($items as $productId) {
                    $candidateIndex = false;
                    foreach ($basketMap[$productId] as $key => $basketItemData) {
                        if ($basketItemData['COLLECTION_ID'] === 0) {
                            $candidateIndex = $key;
                            break;
                        }
                    }
                    if ($candidateIndex === false) {
                        continue 3;
                    }
                    $candidates[$productId] = $candidateIndex;
                }

                foreach ($candidates as $productId => $basketItemIndex) {
                    $basketMap[$productId][$basketItemIndex]['COLLECTION_ID'] = intval($data['ID']);
                }

                if (intval($data['ID']) === $collectionId) {
                    $currentCollectionItems = $items;
                }
            }
        }

        if (!in_array($currentProductId, $currentCollectionItems)) {
            return false;
        }

        foreach ($currentCollectionItems as $productId) {
            foreach ($basketMap[$productId] as $basketItemData) {
                if ($basketItemData['COLLECTION_ID'] === $collectionId) {
                    if ($basketItemData['SPLIT_ID'] !== 0) {
                        foreach ($basketMap[$productId] as $checkSplitBasketItemData) {
                            if (
                                $checkSplitBasketItemData['SPLIT_ID'] === $basketItemData['SPLIT_ID']
                                && $checkSplitBasketItemData['COLLECTION_ID'] === 0
                            ) {
                                return false;
                            }
                        }
                    }
                }
            }
        }

        foreach ($currentCollectionItems as $productId) {
            foreach ($basketMap[$productId] as $basketItemData) {
                if (
                    $basketItemData['COLLECTION_ID'] === $collectionId
                    && $basketItemData['ORIGINAL_PRODUCT_ID'] === intval($row['PRODUCT_ID'])
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function getCollectionParams(): array
    {
        $iblocks = Iblock::getInstance()->getAll('set');
        return [
            'IBLOCK_ID' => reset($iblocks),
            'PROPERTY_ITEMS_CODE' => 'ITEMS',
            'PROPERTY_DISCOUNT_CODE' => 'DISCOUNT',
        ];
    }
}
