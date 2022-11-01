<?php

namespace Its\Maxma\Api;

use Bitrix\Main\Config\Option;
use Its\Maxma\Module;
use CloudLoyalty\Api\Generated\Model\ClientQuery;
use CloudLoyalty\Api\Generated\Model\GetBalanceResponse;
use CloudLoyalty\Api\Exception\ProcessingException;
use CloudLoyalty\Api\Generated\Model\NewClientRequest;
use CloudLoyalty\Api\Generated\Model\NewClientResponse;
use Bitrix\Main\Localization\Loc;
use CloudLoyalty\Api\Generated\Model\UpdateClientRequest;
use Bitrix\Sale\BasketBase;
use CloudLoyalty\Api\Generated\Model\V2CalculatePurchaseResponse;
use CloudLoyalty\Api\Generated\Model\V2CalculatePurchaseRequest;
use CloudLoyalty\Api\Generated\Model\V2SetOrderResponse;
use CloudLoyalty\Api\Generated\Model\V2SetOrderRequest;
use Bitrix\Sale\Order;
use CloudLoyalty\Api\Generated\Model\CancelOrderRequest;
use CloudLoyalty\Api\Generated\Model\ConfirmOrderRequest;
use CloudLoyalty\Api\Generated\Model\GenerateGiftCardRequest;
use CloudLoyalty\Api\Generated\Model\GenerateGiftCardResponse;
use Its\Maxma\Entity\CertificateTable;
use Bitrix\Main\Type\DateTime;
use Bitrix\Sale\BasketItemBase;
use Its\Maxma\Entity\OrderTable;
use Its\Maxma\Entity\HistoryTable;
use CloudLoyalty\Api\Generated\Model\ApplyReturnRequest;
use CloudLoyalty\Api\Generated\Model\ApplyReturnRequestTransaction;
use CloudLoyalty\Api\Generated\Model\ApplyReturnRequestTransactionItemsItem;
use Its\Maxma\Api\Cache\Cache;

class Maxma
{
    private static ?Maxma $instance = null;
    private Client $api;

    public static function getInstance(): self
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __construct()
    {
        $this->api = new Client();

        $apiKey = Option::get(Module::getId(), 'its_maxma_api_key');
        $this->api->setProcessingKey($apiKey);

        $this->api->setAcceptLanguage(
            in_array(LANGUAGE_ID, ['ru', 'en']) ? LANGUAGE_ID : 'ru'
        );

        if (Option::get(Module::getId(), 'its_maxma_test_mode') === 'Y') {
            $this->api->setServerAddress(Client::TEST_SERVER_ADDRESS);
        }
    }

    public function getApi(): Client
    {
        return $this->api;
    }

    public function getAnonymousUser(string $userPhone): Result
    {
        /**
         * @var GetBalanceResponse $response
         */

        $result = new Result();
        $cache = Cache::makeInstance([$userPhone], 0);
        if ($cache->isSuccess()) {
            $result->setData($cache->getVars());
            return $result;
        }

        if (empty($userPhone)) {
            $result->addError('User phone is empty!');
            return $result;
        }
        try {
            $response = $this->api->getBalance(
                (new ClientQuery())
                    ->setPhoneNumber(Tool::formatPhoneNumber($userPhone))
            );
            $data = Tool::makeArrayFromClientInfoReply($response->getClient(), $response->getBonuses());
            $data['CARD']['LINK'] = $response->getWalletsLink();
            $cache->setVars($data);
            $result->setData($data);
        } catch (\Throwable $e) {
            $cache->destroy();
            $result->setExceptionError(
                $e,
                __METHOD__,
                [$userPhone],
                [ProcessingException::ERR_CLIENT_NOT_FOUND]
            );
        }

        return $result;
    }

    public function addAnonymousUser(string $userPhone, array $fields): Result
    {
        /**
         * @var NewClientResponse $response
         */

        $cache = Cache::makeInstance([$userPhone], 0);
        $cache->destroy();

        $result = new Result();
        if (empty($userPhone)) {
            $result->addError('User phone is empty!');
            return $result;
        }
        try {
            $response = $this->api->newClient(
                (new NewClientRequest())
                    ->setClient(
                        Tool::makeClientInfoQueryFromArray($fields)
                            ->setPhoneNumber(Tool::formatPhoneNumber($userPhone))
                    )
            );
            $result->setData(
                Tool::makeArrayFromClientInfoReply($response->getClient(), $response->getBonuses())
            );
        } catch (\Throwable $e) {
            $result->setExceptionError(
                $e,
                __METHOD__,
                [$userPhone, $fields]
            );
        }
        return $result;
    }

    public function updateAnonymousUser(string $userPhone, array $fields): Result
    {
        /**
         * @var NewClientResponse $response
         */

        $cache = Cache::makeInstance([$userPhone], 0);
        $cache->destroy();

        $result = new Result();
        if (empty($userPhone)) {
            $result->addError('User phone is empty!');
            return $result;
        }
        try {
            $response = $this->api->updateClient(
                (new UpdateClientRequest())
                    ->setPhoneNumber(Tool::formatPhoneNumber($userPhone))
                    ->setClient(Tool::makeClientInfoQueryFromArray($fields))
            );
            $result->setData(
                Tool::makeArrayFromClientInfoReply($response->getClient(), $response->getBonuses())
            );
        } catch (\Throwable $e) {
            $result->setExceptionError(
                $e,
                __METHOD__,
                [$userPhone, $fields]
            );
        }
        return $result;
    }

    public function getUser(string $userId): Result
    {
        return $this->getAnonymousUser(Tool::getPhoneNumberFromUserId($userId));
    }

    public function addUser(string $userId, array $fields): Result
    {
        return $this->addAnonymousUser(Tool::getPhoneNumberFromUserId($userId), $fields);
    }

    public function updateUser(string $userId, array $fields): Result
    {
        return $this->updateAnonymousUser(Tool::getPhoneNumberFromUserId($userId), $fields);
    }

    public function calculateOrder(BasketBase $basket, string $userId = '0', string $promocode = '', float $bonus = 0, string $userPhone = ''): Result
    {
        /**
         * @var V2CalculatePurchaseResponse $response
         * @var BasketItemBase $item
         */

        $basketCacheId = [];
        foreach ($basket->getOrderableItems()->getBasketItems() as $item) {
            $basketCacheId[] = "{$item->getBasketCode()}_{$item->getQuantity()}_{$item->getPrice()}";
        }
        $result = new Result();
        $cache = Cache::makeInstance(
            [
                $basketCacheId,
                $userId,
                $promocode,
                $bonus,
                $userPhone,
            ],
            30
        );
        if ($cache->isSuccess()) {
            $result->setData($cache->getVars());
            return $result;
        }

        try {
            $request = Tool::getCalculationQueryFromBasket(
                $basket,
                $userId,
                $promocode,
                $userPhone
            )->setApplyBonuses($bonus);
            $response = $this->api->calculatePurchase(
                (new V2CalculatePurchaseRequest())->setCalculationQuery($request)
            );
            $data = array_merge(
                Tool::getArrayFromCalculationResult($response->getCalculationResult()),
                [
                    'REQUEST' => Tool::getArrayFromCalculationRequest($request),
                    'REQUEST_BONUS' => $bonus,
                ]
            );
            $cache->setVars($data);
            $result->setData($data);
        } catch (\Throwable $e) {
            $cache->destroy();
            $result->setExceptionError(
                $e,
                __METHOD__,
                [$userPhone, $basket, $userId]
            );
        }
        return $result;
    }

    public function setOrder(BasketBase $basket, string $orderId, string $promocode = '', float $bonus = 0): Result
    {
        /**
         * @var V2SetOrderResponse $response
         */

        $result = new Result();
        try {
            $order = Order::load($orderId);
            $maxmaOrderData = OrderTable::getList([
                'filter' => ['=ORDER_ID' => $order->getId()]
            ])->fetch();

            if ($maxmaOrderData) {
                if ($maxmaOrderData['ACCEPT'] === 'Y') {
                    $result->addError(Loc::getMessage('ITS_MAXMA_API_ERROR_ORDER_ACCEPTED'));
                }
                if ($maxmaOrderData['CANCEL'] === 'Y') {
                    $result->addError(Loc::getMessage('ITS_MAXMA_API_ERROR_ORDER_CANCELED'));
                }
            }

            if ($result->isSuccess()) {
                $request = Tool::getCalculationQueryFromBasket(
                    $basket,
                    $order->getUserId(),
                    $promocode
                )->setApplyBonuses($bonus);
                $response = $this->api->setOrder(
                    (new V2SetOrderRequest())
                        ->setOrderId($orderId)
                        ->setCalculationQuery($request)
                );
                $result->setData(array_merge(
                    Tool::getArrayFromCalculationResult($response->getCalculationResult()),
                    [
                        'REQUEST' => Tool::getArrayFromCalculationRequest($request),
                        'REQUEST_BONUS' => $bonus,
                    ]
                ));

                if ($result->getData()) {
                    $add = HistoryTable::add([
                        'ORDER_ID' => $orderId,
                        'RETURN_ID' => 0,
                        'RESPONSE' => $result->getData(),
                    ]);

                    if (!$maxmaOrderData) {
                        OrderTable::add([
                            'ORDER_ID' => $orderId,
                            'COUPON' => $promocode,
                            'ACCEPT' => 'N',
                            'CANCEL' => 'N',
                        ]);
                    } else {
                        OrderTable::update(
                            ['ORDER_ID' => $orderId],
                            [
                                'COUPON' => $promocode,
                            ]
                        );
                    }
                }

            }
        } catch (\Throwable $e) {
            $result->setExceptionError(
                $e,
                __METHOD__,
                [$basket, $orderId]
            );
        }
        return $result;
    }

    public function createReturn(
        string $id,
        DateTime $date,
        string $orderId,
        string $productNumber,
        float $productQuantity,
        float $sum
    ): Result {
        global $USER;

        $result = new Result();
        try {
            $return = $this->api->applyReturn(
                (new ApplyReturnRequest())
                    ->setTransaction(
                        (new ApplyReturnRequestTransaction())
                            ->setId($id)
                            ->setExecutedAt(\DateTime::createFromFormat('d.m.Y H:i:s', $date->format('d.m.Y H:i:s')))
                            ->setPurchaseId($orderId)
                            ->setRefundAmount($sum)
                            ->setShopCode(Tool::getShopCode())
                            ->setShopName(Tool::getShopName())
                            ->setCashierId(strval($USER->GetID()))
                            ->setItems([
                                (new ApplyReturnRequestTransactionItemsItem())
                                    ->setSku($productNumber)
                                    ->setItemCount($productQuantity)
                            ])
                    )
            )->getConfirmation();

            HistoryTable::add([
                'ORDER_ID' => 0,
                'RETURN_ID' => $id,
                'RESPONSE' => [
                    'PHONE_NUMBER' => $return->getPhoneNumber(),
                    'REFUND_ID' => $return->getRefundId(),
                    'REFUND_AMOUNT' => $return->getRefundAmount(),
                    'RECOVERED_BONUSES' => $return->getRecoveredBonuses(),
                    'CANCELLED_BONUSES' => $return->getCancelledBonuses(),
                ],
            ]);
        } catch (\Throwable $e) {
            $result->setExceptionError(
                $e,
                __METHOD__,
                [$id, $date, $orderId, $productNumber, $productQuantity, $sum]
            );
        }
        return $result;
    }

    public function cancelOrder(string $orderId): Result
    {
        $result = new Result();
        try {
            $this->api->cancelOrder(
                (new CancelOrderRequest())->setOrderId($orderId)
            );
            OrderTable::update(
                ['ORDER_ID' => $orderId],
                [
                    'CANCEL' => 'Y',
                ]
            );
        } catch (\Throwable $e) {
            $result->setExceptionError(
                $e,
                __METHOD__,
                [$orderId]
            );
        }
        return $result;
    }

    public function acceptOrder(Order $order): Result
    {
        $result = new Result();
        $orderId = strval($order->getId());
        try {
            $this->api->confirmOrder(
                (new ConfirmOrderRequest())->setOrderId($orderId)
            );
            OrderTable::update(
                ['ORDER_ID' => $orderId],
                [
                    'ACCEPT' => 'Y',
                ]
            );
            foreach ($order->getBasket()->getBasketItems() as $basketItem) {
                /** @var BasketItemBase $basketItem */
                $element = \CIBlockElement::GetByID($basketItem->getProductId())->GetNextElement();
                $elementData = $element->GetFields();
                $elementData['PROPERTIES'] = $element->GetProperties();
                $code = strval($elementData['PROPERTIES'][Tool::getCertificateField()]['VALUE']);
                if (empty(trim($code))) {
                    continue;
                }
                for ($i = intval($basketItem->getQuantity()); $i > 0; $i--) {
                    $this->generateCertificate(
                        $order->getId(),
                        $basketItem->getProductId(),
                        $code
                    );
                }
            }
        } catch (\Throwable $e) {
            $result->setExceptionError(
                $e,
                __METHOD__,
                [$orderId]
            );
        }
        return $result;
    }

    public function generateCertificate(int $orderId, int $elementId, string $code): Result
    {
        /** @var GenerateGiftCardResponse $response */

        $result = new Result();
        try {
            $response = $this->api->itsGenerateGiftCard(
                (new GenerateGiftCardRequest())->setCode($code)
            );
            $card = $response->getGiftCard();
            $expire = $card->getValidUntil() instanceof \DateTime ? $card->getValidUntil() : new \DateTime();
            $add = CertificateTable::add([
                'ORDER_ID' => $orderId,
                'ELEMENT_ID' => $elementId,
                'CODE' => $card->getCode(),
                'SUM' => $card->getInitAmount(),
                'EXPIRE' => DateTime::createFromPhp($expire),
                'EXPIRE_INF' => $expire instanceof \DateTime ? 'N' : 'Y',
                'NUMBER' => $card->getNumber(),
            ]);
            if (!$add->isSuccess()) {
                throw new \Exception(implode(', ', $add->getErrorMessages()));
            }
        } catch (\Throwable $e) {
            $result->setExceptionError(
                $e,
                __METHOD__,
                [$orderId, $elementId, $code]
            );
        }
        return $result;
    }
}
