<?php

use Bitrix\Main\Loader;
use Its\Library\Iblock\Iblock;
use Bitrix\Sale\Basket;
use Bitrix\Catalog\Product\PropertyCatalogFeature;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Currency\CurrencyManager;
use Bitrix\Sale\Discount\Context\Fuser as FuserDiscount;
use Bitrix\Sale\Discount;
use Bitrix\Sale\DiscountBase;
use Bitrix\Sale\BasketItem;
use Bitrix\Sale\Fuser;

class ElementSetComponent extends \CBitrixComponent
{
    public function executeComponent()
    {
        if (
            !Loader::includeModule('iblock')
            || !Loader::includeModule('catalog')
            || !Loader::includeModule('sale')
        ) {
            return;
        }

        $elementId = intval($this->arParams['ELEMENT_ID']);
        if ($elementId <= 0) {
            return;
        }

        $collectionParams = static::getCollectionParams();

        $resCollections = \CIBlockElement::GetList(
            [
                'SORT' => 'ASC',
                'ID' => 'ASC',
            ],
            [
                'IBLOCK_ID' => $collectionParams['IBLOCK_ID'],
                'ACTIVE' => 'Y',
                'ACTIVE_DATE' => 'Y',
                "PROPERTY_{$collectionParams['PROPERTY_ITEMS_CODE']}" => $elementId,
            ],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'NAME']
        );

        $this->arResult['COLLECTIONS'] = [];

        while ($collection = $resCollections->GetNextElement()) {
            $collectionData = $collection->GetFields();
            $collectionData['PROPERTIES'] = $collection->GetProperties();

            $collectionData['ITEMS'] = [];

            $items = [];
            foreach ($collectionData['PROPERTIES'][$collectionParams['PROPERTY_ITEMS_CODE']]['VALUE'] as $itemId) {
                $items[] = intval($itemId);
            }
            usort($items, function ($a, $b) use ($elementId) {
                if ($a === $elementId) {
                    return -1;
                }
                return $b === $elementId ? 1 : 0;
            });

            if (count($items) < 2) {
                continue;
            }

            $resItems = \CIBlockElement::GetList(
                ['ID' => $items],
                [
                    'ID' => $items,
                    'ACTIVE' => 'Y',
                    'ACTIVE_DATE' => 'Y',
                    'CATALOG_AVAILABLE' => 'Y',
                ]
            );

            $colorSelected = '';
            $sizeSelected = '';
            $itemsData = [];
            while ($item = $resItems->GetNextElement()) {
                $itemData = $item->GetFields();
                $itemData['PROPERTIES'] = $item->GetProperties();

                $offerIblockProps = \CCatalogSku::GetInfoByProductIBlock($itemData['IBLOCK_ID']);
                if ($offerIblockProps) {
                    $offerProps = PropertyCatalogFeature::getOfferTreePropertyCodes(
                        $offerIblockProps['IBLOCK_ID'],
                        ['CODE' => 'Y']
                    );
                    $itemData['SKU_PROPS'] = \CIBlockPriceTools::getTreeProperties(
                        $offerIblockProps,
                        $offerProps,
                        [
                            'PICT' => false,
                            'NAME' => '-'
                        ]
                    );
                    $itemData['OFFERS'] = array_values(\CCatalogSKU::getOffersList(
                        [$itemData['ID']],
                        $offerIblockProps['PRODUCT_IBLOCK_ID'],
                        [
                            'ACTIVE' => 'Y',
                            'ACTIVE_DATE' => 'Y',
                            'CATALOG_AVAILABLE' => 'Y',
                        ],
                        ['ID', 'IBLOCK_ID', 'NAME', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'],
                        ['CODE' => !empty($offerProps) ? $offerProps : []]
                    )[$itemData['ID']]);
                }

                $itemData['OFFERS_SPECIAL_SORTED'] = [];
                foreach ($itemData['OFFERS'] as $offerKey => $offerData) {
                    $offerData['ORIGINAL_KEY'] = $offerKey;
                    $itemData['OFFERS_SPECIAL_SORTED'][mb_strtoupper("{$offerData['PROPERTIES']['RAZMER']['VALUE']}_{$offerData['ID']}")] = $offerData;
                }

                uksort($itemData['OFFERS_SPECIAL_SORTED'], function ($a, $b) {
                    foreach ([&$a, &$b] as &$x) {
                        $map = [
                            'XXL' => 6000,
                            'XL' => 5000,
                            'L' => 4000,
                            'M' => 3000,
                            'S' => 2000,
                            'XS' => 1000,
                            'XXS' => 500,
                        ];
                        foreach ($map as $s => $w) {
                            if (substr($x, 0, strlen($s)) === $s) {
                                $x = $w;
                                break;
                            }
                        }
                    }
                    if ($a == $b) {
                        return 0;
                    }
                    return $a < $b ? 1 : -1;
                });

                if ($this->request->get('parent_element') == $itemData['ID'] && !empty($itemData['OFFERS'])) {
                    foreach ($itemData['OFFERS'] as $offerData) {
                        if ($this->request->get('parent_offer_selected') == $offerData['ID']) {
                            $colorSelected = $offerData['PROPERTIES']['TSVET']['VALUE'];
                            $sizeSelected = $offerData['PROPERTIES']['RAZMER']['VALUE'];
                        }
                    }
                }

                $itemsData[] = $itemData;
            }

            $sizesMap = [
                '70а' => ['XS', 'S'],
                '70b' => ['XS', 'S'],
                '70c' => ['XS', 'S'],
                '75a' => ['S', 'M'],
                '75b' => ['S', 'M'],
                '75c' => ['S', 'M'],
                '80b' => ['L'],
                '80c' => ['L'],
                '80d' => ['L'],
                '85b' => ['XL'],
                '85c' => ['XL'],
                '85d' => ['XL'],
                '90b' => ['XXL'],
                '90c' => ['XXL'],
                '90d' => ['XXL'],

                'XS' => [
                    '70а', '70b', '70c',
                    '70А', '70B', '70C'
                ],
                'S' => [
                    '75a', '75b', '75c', '70а', '70b', '70c',
                    '75A', '75B', '75C', '70А', '70B', '70C'
                ],
                'M' => [
                    '75a', '75b', '75c',
                    '75A', '75B', '75C'
                ],
                'L' => [
                    '80b', '80c', '80d',
                    '80B', '80C', '80D'
                ],
                'XL' => [
                    '85b', '85c', '85d',
                    '85B', '85C', '85D'
                ],
                'XXL' => [
                    '90b', '90c', '90d',
                    '90B', '90C', '90D'
                ],

                '70А' => ['XS', 'S'],
                '70B' => ['XS', 'S'],
                '70C' => ['XS', 'S'],
                '75A' => ['S', 'M'],
                '75B' => ['S', 'M'],
                '75C' => ['S', 'M'],
                '80B' => ['L'],
                '80C' => ['L'],
                '80D' => ['L'],
                '85B' => ['XL'],
                '85C' => ['XL'],
                '85D' => ['XL'],
                '90B' => ['XXL'],
                '90C' => ['XXL'],
                '90D' => ['XXL'],
            ];

            $sizeSelectedVariants = is_array($sizesMap[$sizeSelected]) ? $sizesMap[$sizeSelected] : [];

            foreach ($itemsData as $itemKey => $itemData) {
                if (!empty($itemData['OFFERS'])) {
                    $itemsData[$itemKey]['OFFER_SELECTED'] = false;
                    foreach ($itemData['OFFERS'] as $offerKey => $offerData) {
                        if (!empty($colorSelected) && !empty($sizeSelected)) {
                            if (
                                $offerData['PROPERTIES']['TSVET']['VALUE'] === $colorSelected
                                && (
                                    $offerData['PROPERTIES']['RAZMER']['VALUE'] === $sizeSelected
                                    || in_array($offerData['PROPERTIES']['RAZMER']['VALUE'], $sizeSelectedVariants)
                                )
                            ) {
                                $itemsData[$itemKey]['OFFER_SELECTED'] = $offerKey;
                                break;
                            }
                        }
                    }
                }
            }

            foreach ($itemsData as $itemKey => $itemData) {
                if (!empty($itemData['OFFERS'])) {
                    if ($itemData['OFFER_SELECTED'] === false) {
                        foreach ($itemData['OFFERS_SPECIAL_SORTED'] as $offerData) {
                            if (!empty($colorSelected)) {
                                if ($offerData['PROPERTIES']['TSVET']['VALUE'] === $colorSelected) {
                                    $itemsData[$itemKey]['OFFER_SELECTED'] = $offerData['ORIGINAL_KEY'];
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            foreach ($itemsData as $itemKey => $itemData) {
                if (!empty($itemData['OFFERS'])) {
                    $offersMap = [];
                    if ($itemData['OFFER_SELECTED'] === false) {
                        foreach ($itemData['OFFERS_SPECIAL_SORTED'] as $offerData) {
                            $offersMap[$offerData['ID']] = $offerData['ORIGINAL_KEY'];
                            if (!empty($sizeSelected)) {
                                if (
                                    $offerData['PROPERTIES']['RAZMER']['VALUE'] === $sizeSelected
                                    || in_array($offerData['PROPERTIES']['RAZMER']['VALUE'], $sizeSelectedVariants)
                                ) {
                                    $itemsData[$itemKey]['OFFER_SELECTED'] = $offerData['ORIGINAL_KEY'];
                                    break;
                                }
                            } elseif (intval($this->request["collection_{$collectionData['ID']}_product_{$itemData['ID']}"]) === intval($offerData['ID'])) {
                                $itemsData[$itemKey]['OFFER_SELECTED'] = $offerData['ORIGINAL_KEY'];
                                break;
                            }
                        }
                    }
                    if ($itemsData[$itemKey]['OFFER_SELECTED'] === false) {
                        $itemsData[$itemKey]['OFFER_SELECTED'] = reset($offersMap);
                    }
                }
            }

            foreach ($itemsData as $itemKey => $itemData) {
                if (!empty($itemData['OFFERS'])) {
                    $needValues = [];
                    foreach ($itemData['OFFERS'] as $offerKey => $offerData) {
                        foreach (array_keys($itemData['SKU_PROPS']) as $propCode) {
                            if (isset($offerData['PROPERTIES'][$propCode])) {
                                if (!isset($needValues[$itemData['SKU_PROPS'][$propCode]['ID']])) {
                                    $needValues[$itemData['SKU_PROPS'][$propCode]['ID']] = [];
                                }
                                $valueId = $itemData['SKU_PROPS'][$propCode]['PROPERTY_TYPE'] == PropertyTable::TYPE_LIST
                                    ? $offerData['PROPERTIES'][$propCode]['VALUE_ENUM_ID']
                                    : $offerData['PROPERTIES'][$propCode]['VALUE'];

                                $needValues[$itemData['SKU_PROPS'][$propCode]['ID']][$valueId] = $valueId;

                                $itemData['OFFERS'][$offerKey]['TREE']["PROP_{$offerData['PROPERTIES'][$propCode]['ID']}"] = $valueId;
                            }
                        }
                    }
                    if (!empty($needValues)) {
                        \CIBlockPriceTools::getTreePropertyValues($itemData['SKU_PROPS'], $needValues);
                    }

                    $allowProps = [];
                    foreach ($itemData['SKU_PROPS'] as $propertyData) {
                        $allowProps["PROP_{$propertyData['ID']}"] = $propertyData['ID'];
                    }

                    foreach ($itemData['OFFERS'] as $offerKey => $offerData) {
                        $itemData['OFFERS'][$offerKey]['TREE'] = array_intersect_key(
                            $itemData['OFFERS'][$offerKey]['TREE'],
                            $allowProps
                        );
                    }
                }

                $collectionData['ITEMS'][$itemData['ID']] = $itemData;
            }

            if (count($collectionData['ITEMS']) < 2) {
                continue;
            }

            $currentBasket = Basket::loadItemsForFUser(
                Fuser::getId(),
                !empty($this->request['siteId']) ? $this->request['siteId'] : SITE_ID
            );

            $basket = Basket::create(!empty($this->request['siteId']) ? $this->request['siteId'] : SITE_ID);
            $parentsMap = [];

            $collectionData['IN_BASKET'] = 'Y';
            foreach ($collectionData['ITEMS'] as $itemKey => $itemData) {
                if (!empty($itemData['OFFERS'])) {
                    $itemId = $itemData['OFFERS'][$itemData['OFFER_SELECTED']]['ID'];
                    $parentsMap[$itemId] = $itemData['ID'];
                } else {
                    $itemId = $itemData['ID'];
                    $parentsMap[$itemId] = $itemData['ID'];
                }

                $isInBasketItem = false;
                foreach ($currentBasket->getBasketItems() as $currentBasketItem) {
                    /** @var $currentBasketItem BasketItem */
                    if (intval($currentBasketItem->getProductId()) === intval($itemId)) {
                        $isInBasketItem = true;
                        break;
                    }
                }

                if (intval($this->request->get('add_to_basket')) === intval($collectionData['ID'])) {
                    if ($isInBasketItem && isset($currentBasketItem)) {
                        $currentBasketItem->setField('QUANTITY', $currentBasketItem->getQuantity() + 1);
                    } else {
                        $item = $currentBasket->createItem('catalog', intval($itemId));
                        $item->setFields([
                            'QUANTITY' => 1,
                            'CURRENCY' => CurrencyManager::getBaseCurrency(),
                            'LID' => !empty($this->request['siteId']) ? $this->request['siteId'] : SITE_ID,
                            'PRODUCT_PROVIDER_CLASS' => '\CCatalogProductProvider',
                        ]);
                    }
                    $isInBasketItem = true;
                }

                if (!$isInBasketItem) {
                    $collectionData['IN_BASKET'] = 'N';
                }

                $item = $basket->createItem('catalog', intval($itemId));
                $item->setFields([
                    'QUANTITY' => 1,
                    'CURRENCY' => CurrencyManager::getBaseCurrency(),
                    'LID' => !empty($this->request['siteId']) ? $this->request['siteId'] : SITE_ID,
                    'PRODUCT_PROVIDER_CLASS' => '\CCatalogProductProvider',
                ]);
            }
            $basket->refresh();
            $currentBasket->save();

            $context = new FuserDiscount($basket->getFUserId());
            $discounts = Discount::buildFromBasket($basket, $context);
            if ($discounts instanceof DiscountBase) {
                $result = $discounts->calculate();
                if ($result->isSuccess()) {
                    $discountData = $result->getData();
                    $basket->applyDiscount($discountData['BASKET_ITEMS']);
                }
            }

            $collectionData['BASKET'] = [
                'SUM' => $basket->getPrice(),
                'SUM_FORMATTED' => \CCurrencyLang::CurrencyFormat($basket->getPrice(), CurrencyManager::getBaseCurrency()),
                'BASE_SUM' => $basket->getBasePrice(),
                'BASE_SUM_FORMATTED' => \CCurrencyLang::CurrencyFormat($basket->getBasePrice(), CurrencyManager::getBaseCurrency()),
                'ITEMS' => []
            ];

            foreach ($basket as $basketItem) {
                /** @var $basketItem BasketItem */
                $collectionData['BASKET']['ITEMS'][] = [
                    'PRODUCT_ID' => $parentsMap[$basketItem->getProductId()],
                    'PRICE' => $basketItem->getPrice(),
                    'PRICE_FORMATTED' => \CCurrencyLang::CurrencyFormat($basketItem->getPrice(), CurrencyManager::getBaseCurrency()),
                    'BASE_PRICE' => $basketItem->getBasePrice(),
                    'BASE_PRICE_FORMATTED' => \CCurrencyLang::CurrencyFormat($basketItem->getBasePrice(), CurrencyManager::getBaseCurrency()),
                ];
            }

            $this->arResult['COLLECTIONS'][] = $collectionData;
        }

        $this->includeComponentTemplate();
    }

    public static function getCollectionParams(): array
    {
        return [
            'IBLOCK_ID' => Iblock::getInstance()->get('set'),
            'PROPERTY_ITEMS_CODE' => 'ITEMS',
            'PROPERTY_DISCOUNT_CODE' => 'DISCOUNT',
        ];
    }
}
