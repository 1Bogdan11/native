<?php

namespace Its\Maxma\Api;

use CloudLoyalty\Api\Generated\Model\ClientInfoQuery;
use CloudLoyalty\Api\Generated\Model\ClientInfoReply;
use Bitrix\Main\Config\Option;
use Its\Maxma\Module;
use CloudLoyalty\Api\Generated\Model\ClientBonusExpirationItem;
use Bitrix\Main\Type\DateTime;
use Bitrix\Sale\BasketBase;
use CloudLoyalty\Api\Generated\Model\CalculationQuery;
use CloudLoyalty\Api\Generated\Model\CalculationResult;
use CloudLoyalty\Api\Generated\Model\ClientQuery;
use CloudLoyalty\Api\Generated\Model\ShopQuery;
use Bitrix\Sale\BasketItemBase;
use CloudLoyalty\Api\Generated\Model\CalculationQueryRow;
use Bitrix\Iblock\ElementTable;
use CloudLoyalty\Api\Generated\Model\CalculationQueryRowProduct;
use CloudLoyalty\Api\Generated\Model\Error;
use CloudLoyalty\Api\Generated\Model\CalculationResultRow;
use CloudLoyalty\Api\Generated\Model\CalculationResultDiscounts;
use CloudLoyalty\Api\Generated\Model\CalculationResultRowOffersItem;
use Bitrix\Main\UserTable;
use Bitrix\Main\PhoneNumber\Parser;
use Bitrix\Main\PhoneNumber\Format;

class Tool
{
    private static string $defaultPhone = '';
    private static string $phoneField = 'PERSONAL_PHONE';
    private static string $orderCouponField = 'MAXMA_COUPON';
    private static string $orderBonusField = 'MAXMA_BONUS_COUNT';
    private static string $orderPhoneField = 'PHONE';
    private static string $certificateField = 'MAXMA_CERTIFICATE';
    private static string $artnumberField = 'CML2_ARTICLE';
    private static string $skuArtnumberField = 'CML2_ARTICLE';
    private static string $orderEndStatus = 'F';
    private static string $shopCode = 'empty_code';
    private static string $shopName = 'empty_name';
    private static int $orderSendTimeout = 0;

    public static function getDefaultPhone(): string
    {
        if (empty(static::$defaultPhone)) {
            static::$defaultPhone = Option::get(Module::getId(), 'its_maxma_fake_user_phone', static::$defaultPhone);
        }
        return static::$defaultPhone;
    }

    public static function getPhoneField(): string
    {
        if (empty(static::$phoneField)) {
            static::$phoneField = Option::get(Module::getId(), 'its_maxma_phone_field_key', static::$phoneField);
        }
        return static::$phoneField;
    }

    public static function getOrderCouponField(): string
    {
        if (empty(static::$orderCouponField)) {
            static::$orderCouponField = Option::get(Module::getId(), 'its_maxma_order_prop_coupon', static::$orderCouponField);
        }
        return static::$orderCouponField;
    }

    public static function getOrderBonusField(): string
    {
        if (empty(static::$orderBonusField)) {
            static::$orderBonusField = Option::get(Module::getId(), 'its_maxma_order_prop_bonus_count', static::$orderBonusField);
        }
        return static::$orderBonusField;
    }

    public static function getOrderPhoneField(): string
    {
        if (empty(static::$orderPhoneField)) {
            static::$orderPhoneField = Option::get(Module::getId(), 'its_maxma_order_prop_phone', static::$orderPhoneField);
        }
        return static::$orderPhoneField;
    }

    public static function getOrderEndStatus(): string
    {
        if (empty(static::$orderEndStatus)) {
            static::$orderEndStatus = Option::get(Module::getId(), 'its_maxma_order_end_status', static::$orderEndStatus);
        }
        return static::$orderEndStatus;
    }

    public static function getOrderSendTimeout(): int
    {
        if (empty(static::$orderSendTimeout)) {
            static::$orderSendTimeout = abs(intval(Option::get(Module::getId(), 'its_maxma_order_send_timeout', static::$orderSendTimeout)));
        }
        return static::$orderSendTimeout;
    }

    public static function getShopCode(): string
    {
        if (empty(static::$shopCode)) {
            static::$shopCode = Option::get(Module::getId(), 'its_maxma_shop_code', static::$shopCode);
        }
        return static::$shopCode;
    }

    public static function getShopName(): string
    {
        if (empty(static::$shopName)) {
            static::$shopName = Option::get(Module::getId(), 'its_maxma_shop_name', static::$shopName);
        }
        return static::$shopName;
    }

    public static function getCertificateField(): string
    {
        if (empty(static::$certificateField)) {
            static::$certificateField = Option::get(Module::getId(), 'its_maxma_element_card_code', static::$certificateField);
        }
        return static::$certificateField;
    }

    public static function getArtnumberField(): string
    {
        if (empty(static::$artnumberField)) {
            static::$artnumberField = Option::get(Module::getId(), 'its_maxma_element_artnumber_field', static::$artnumberField);
        }
        return static::$artnumberField;
    }

    public static function getSkuArtnumberField(): string
    {
        if (empty(static::$skuArtnumberField)) {
            static::$skuArtnumberField = Option::get(Module::getId(), 'its_maxma_element_skuArtnumber_field_sku', static::$skuArtnumberField);
        }
        return static::$skuArtnumberField;
    }

    public static function formatPhoneNumber(string $phone): string
    {
        return Parser::getInstance()->parse($phone)->format(Format::E164);
    }

    public static function getPhoneNumberFromUserId(int $userId = 0): string
    {
        $userData = UserTable::getById($userId)->fetch();
        return strval($userData[static::getPhoneField()]);
    }

    public static function makeClientInfoQueryFromArray(array $fields): ClientInfoQuery
    {
        $client = new ClientInfoQuery();
        $client
            ->setName(strval($fields['NAME']))
            ->setSurname(strval($fields['LAST_NAME']))
            ->setPatronymicName(strval($fields['SECOND_NAME']))
            ->setEmail(strval($fields['EMAIL']))
            ->setGender(['M' => 1, 'F' => 2][$fields['PERSONAL_GENDER']] ?: 0);

        try {
            if ($fields['PERSONAL_BIRTHDAY']) {
                $client->setBirthdate(\DateTime::createFromFormat('d.m.Y', (new DateTime($fields['PERSONAL_BIRTHDAY']))->format('d.m.Y')));
            }
        } catch (\Throwable $e) {
            $client->setBirthdate(\DateTime::createFromFormat('d.m.Y', '01.01.1990'));
        }

        return $client;
    }

    public static function makeArrayFromClientInfoReply(ClientInfoReply $client, array $bonuses = []): array
    {
        /** @var ClientBonusExpirationItem $bonus */

        $result = [
            'ID' => intval($client->getExternalId()),
            'NAME' => $client->getName(),
            'LAST_NAME' => $client->getSurname(),
            'SECOND_NAME' => $client->getPatronymicName(),
            'EMAIL' => $client->getEmail(),
            static::getPhoneField() => $client->getPhoneNumber(),

            'LEVEL' => $client->getLevel(),
            'BALANCE' => floatval($client->getBonuses()),
            'BALANCE_PENDING' => floatval($client->getPendingBonuses()),
            'BALANCE_EXPIRE' => [],
            'CARD' => [
                'ID' => $client->getCard(),
                'NUMBER' => $client->getCardString(),
            ],
        ];

        foreach ($bonuses as $bonus) {
            $expired = $bonus->getExpireAt();
            $item = ['QUANTITY' => $bonus->getAmount()];
            if ($expired instanceof \DateTime) {
                $item['EXPIRED'] = strval(new DateTime($expired->format('d.m.Y H:i:s'), 'd.m.Y H:i:s'));
            }
            $result['BALANCE_EXPIRE'][] = $item;
        }

        return $result;
    }

    public static function getCalculationQueryFromBasket(BasketBase $basket, string $userId = '0', string $promocode = '', string $userPhone = ''): CalculationQuery
    {
        /** @var BasketItemBase $item */

        $result = new CalculationQuery();

        if (intval($userId) > 0) {
            $userPhone = static::getPhoneNumberFromUserId($userId) ?: $userPhone;
        }

        if ($userPhone) {
            $result->setClient(
                (new ClientQuery())->setPhoneNumber(static::formatPhoneNumber($userPhone))
            );
        }

        $result->setShop(
            (new ShopQuery())->setCode(static::getShopCode())->setName(static::getShopName())
        );
        $result->setDiscountRoundStep(0);

        if (!empty($promocode)) {
            $result->setPromocode($promocode);
        }

        $rows = [];
        foreach ($basket->getOrderableItems()->getBasketItems() as $item) {
            $row = new CalculationQueryRow();

            $row->setId(strval($item->getBasketCode()));
            $row->setQty($item->getQuantity());
            $row->setAutoDiscount(($item->getBasePrice() - $item->getPrice()) * $item->getQuantity());

            $row->setManualDiscount(.0); // Ручная скидка, примененная к строке
            $row->setNoApplyBonuses(false); // Запрет применения бонусов к этой строке
            $row->setNoCollectBonuses(false); // Запрет начисления бонусов за эту строку
            $row->setNoPromocode(false); // Запрет применения скидки по промокоду к этой строке
            $row->setNoOffer(false); // Запрет применения акций к этой строке
            $row->setMaxDiscount($item->getBasePrice()); // Величина максимальной скидки для этой строки

            $element = \CIBlockElement::GetList(
                ['ID' => 'DESC'],
                ['ID' => $item->getProductId()],
                false,
                false,
                ['ID', 'IBLOCK_ID', 'NAME', 'XML_ID', 'IBLOCK_SECTION_ID']
            )->GetNextElement();
            $elementData = $element->GetFields();
            $elementData['PROPERTIES'] = $element->GetProperties();
            $productInfo = \CCatalogSku::GetProductInfo($item->getProductId());

            if (!empty($productInfo)) {
                $parentElement = \CIBlockElement::GetList(
                    ['ID' => 'DESC'],
                    ['ID' => $productInfo['ID'], 'IBLOCK_ID' => $productInfo['IBLOCK_ID']],
                    false,
                    false,
                    ['ID', 'IBLOCK_ID', 'NAME', 'XML_ID', 'IBLOCK_SECTION_ID']
                )->GetNextElement();
                $parentElementData = $parentElement->GetFields();
                $parentElementData['PROPERTIES'] = $parentElement->GetProperties();
                $elementData['NAME'] = $parentElementData['NAME'];
                $elementData['IBLOCK_SECTION_ID'] = $parentElementData['IBLOCK_SECTION_ID'];
                if (Option::get(Module::getId(), 'its_maxma_send_artnumber_or_xml_id_sku') !== 'Y') {
                    $elementData['XML_ID'] = $parentElementData['XML_ID'];
                    $elementData['PROPERTIES'][static::getSkuArtnumberField()]['VALUE'] = $parentElementData['PROPERTIES'][static::getArtnumberField()]['VALUE'];
                }
            }

            $sku = strval($elementData['XML_ID']);
            if (Option::get(Module::getId(), 'its_maxma_send_artnumber') === 'Y') {
                $sku = strval($elementData['PROPERTIES'][!empty($productInfo) ? static::getSkuArtnumberField() : static::getArtnumberField()]['VALUE']);
            }

            $chain = [];
            $resource = \CIBlockSection::GetNavChain($elementData['IBLOCK_ID'], $elementData['IBLOCK_SECTION_ID']);
            while ($sectionData = $resource->Fetch()) {
                $chain[] = $sectionData['NAME'];
            }

            $product = new CalculationQueryRowProduct();
            $product->setExternalId(strval($elementData['ID']))
                ->setSku($sku)
                ->setTitle(strval($elementData['NAME']))
                ->setBlackPrice($item->getBasePrice());
            if (!empty($chain)) {
                $product->setCategory(implode(', ', $chain));
            }
            $row->setProduct($product);

            $rows[] = $row;
        }
        $result->setRows($rows);

        return $result;
    }

    public static function getArrayFromCalculationRequest(CalculationQuery $request): array
    {
        $result = [
            'CLIENT_PHONE' => $request->getClient() ? $request->getClient()->getPhoneNumber() : '',
            'SHOP_CODE' => $request->getShop()->getCode(),
            'SHOP_NAME' => $request->getShop()->getName(),
            'COUPON' => $request->getPromocode(),
            'ROWS' => [],
        ];

        foreach ($request->getRows() as $row) {
            /** @var CalculationQueryRow $row */
            $item = [
                'ID' => $row->getId(),
                'QUANTITY' => $row->getQty(),
                'AUTO_DISCOUNT' => $row->getAutoDiscount(),
                'MANUAL_DISCOUNT' => $row->getManualDiscount(),
                'NO_APPLY_BONUSES' => $row->getNoApplyBonuses(),
                'NO_COLLECT_BONUSES' => $row->getNoCollectBonuses(),
                'NO_PROMOCODE' => $row->getNoPromocode(),
                'NO_OFFER' => $row->getNoOffer(),
                'MAX_DISCOUNT' => $row->getMaxDiscount(),
                'PRODUCT_EXTERNAL_ID' => $row->getProduct()->getExternalId(),
                'PRODUCT_SKU' => $row->getProduct()->getSku(),
                'PRODUCT_TITLE' => $row->getProduct()->getTitle(),
                'PRODUCT_BLACK_PRICE' => $row->getProduct()->getBlackPrice(),
                'PRODUCT_CATEGORY' => $row->getProduct()->getCategory(),
            ];

            $result['ROWS'][] = $item;
        }

        return $result;
    }

    public static function getArrayFromCalculationResult(CalculationResult $response): array
    {
        /** @var CalculationResultRow $row */
        /** @var CalculationResultRowOffersItem $promo */

        $result = [
            'ROWS' => [],
            'PROMOCODE' => [],
            'DISCOUNT' => static::getArrayFromCalculationResultDiscounts($response->getSummary()->getDiscounts()),
            'BONUS' => [],
            'TOTAL_DISCOUNT' => $response->getSummary()->getTotalDiscount(),
        ];

        $promocode = $response->getPromocode();
        if ($promocode) {
            $result['PROMOCODE'] = [
                'APPLY' => $promocode->getApplied() ? 'Y' : 'N',
                'ERROR' => static::getArrayFromError($promocode->getError()),
            ];
        }

        $bonus = $response->getBonuses();
        if ($bonus) {
            $result['BONUS'] = [
                'APPLY' => $bonus->getApplied(),
                'COLLECT' => $bonus->getCollected(),
                'MAX_APPLY' => $bonus->getMaxToApply(),
                'ERROR' => static::getArrayFromError($bonus->getError()),
            ];
        }

        $rows = $response->getRows();
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $item = [
                    'ID' => $row->getId(),
                    'DISCOUNT' => static::getArrayFromCalculationResultDiscounts($row->getDiscounts()),
                    'TOTAL_DISCOUNT' => $row->getTotalDiscount(),
                    'BONUS' => [],
                    'PROMO' => [],
                ];

                $bonus = $row->getBonuses();
                if ($bonus) {
                    $item['BONUS'] = [
                        'APPLY' => $bonus->getApplied(),
                        'COLLECT' => $bonus->getCollected(),
                    ];
                }

                $offers = $row->getOffers();
                if (is_array($offers)) {
                    foreach ($offers as $promo) {
                        $item['PROMO'][] = [
                            'ID' => $promo->getId(),
                            'CODE' => $promo->getCode(),
                            'NAME' => $promo->getName(),
                            'BONUS' => $promo->getBonuses(),
                            'DISCOUNT' => $promo->getAmount(),
                        ];
                    }
                }

                $result['ROWS'][$row->getId()] = $item;
            }
        }

        return $result;
    }

    public static function getArrayFromError($error): array
    {
        $result = [];
        if ($error instanceof Error) {
            $result = [
                'CODE' => $error->getCode(),
                'DESCRIPTION' => $error->getDescription(),
                'DETAIL' => $error->getHint(),
            ];
        }
        return $result;
    }

    public static function getArrayFromCalculationResultDiscounts($discount): array
    {
        $result = [];
        if ($discount instanceof CalculationResultDiscounts) {
            $result = [
                'AUTO' => $discount->getAuto(),
                'MANUAL' => $discount->getManual(),
                'BONUS' => $discount->getBonuses(),
                'PROMOCODE' => $discount->getPromocode(),
                'PROMO' => $discount->getOffer(),
                'ROUND' => $discount->getRounding(),
            ];
        }
        return $result;
    }
}
