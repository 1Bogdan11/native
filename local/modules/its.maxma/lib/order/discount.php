<?php

namespace Its\Maxma\Order;

use Its\Maxma\Api\Maxma;
use Bitrix\Sale\BasketItemBase;
use Bitrix\Sale\Discount\Actions;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;
use Bitrix\Sale\BasketBase;
use Its\Maxma\Api\Tool;
use Bitrix\Sale\EntityPropertyValue;
use Bitrix\Sale\Internals\OrderPropsTable;

class Discount
{
    private static bool $enableCalculation = false;

    private static array $lastResult = [];
    private array $orderData = [];

    public function __construct(array &$order)
    {
        $this->orderData = &$order;
    }

    public function apply(): void
    {
        /** @var BasketItemBase $item */

        global $USER;

        $order = null;
        $orderId = intval($this->orderData['ID']);
        if ($orderId > 0) {
            $order = Order::load($orderId);
        }

        $basket = Basket::create($this->orderData['SITE_ID']);
        $basket->loadFromArray($this->orderData['BASKET_ITEMS']);

        if ($order) {
            $this->applyOrder($basket, $order);
            return;
        }

        if (!self::$enableCalculation) {
            return;
        }

        $bonusPropertyData = OrderPropsTable::getList(['filter' => ['=CODE' => Tool::getOrderBonusField()], 'select' => ['ID']])->fetch();
        $bonus = floatval($this->orderData['ORDER_PROP'][$bonusPropertyData['ID']]);
        $phonePropertyData = OrderPropsTable::getList(['filter' => ['=CODE' => Tool::getOrderPhoneField()], 'select' => ['ID']])->fetch();
        $phone = strval($this->orderData['ORDER_PROP'][$phonePropertyData['ID']]);

        if (!$USER->IsAuthorized() && !empty($phone)) {
            $check = Maxma::getInstance()->getAnonymousUser($phone);
            if (!$check->getData()) {
                Maxma::getInstance()->addAnonymousUser($phone, []);
                $_SESSION['ITS_MAXMA_NEED_UPDATE_USER_AFTER_ORDER'] = true;
            }
        } elseif ($USER->IsAuthorized()) {
            $check = Maxma::getInstance()->getUser(intval($USER->GetID()));
            if (!$check->getData()) {
                Maxma::getInstance()->addUser(intval($USER->GetID()), \CUser::GetByID($USER->GetID())->Fetch());
            }
        }

        $userId = intval($this->orderData['USER_ID']);
        if ($userId === intval(\CSaleUser::GetAnonymousUserID())) {
            $userId = 0;
        }

        $this->applyWithoutOrder(
            $basket,
            $userId,
            $bonus,
            $phone
        );
    }

    protected function applyWithoutOrder(BasketBase $basket, int $userId, float $bonus = 0, string $userPhone = ''): void
    {
        $calculate = Maxma::getInstance()->calculateOrder($basket, $userId, Coupon::getFromSession(), $bonus, $userPhone);
        $calculateData = $calculate->getData();
        self::$lastResult = $calculateData;
        $this->applyToBasket($basket, $calculateData);
    }

    protected function applyOrder(BasketBase $basket, Order $order): void
    {
        /** @var EntityPropertyValue $property */

        $coupon = '';
        $bonus = 0;
        foreach ($order->getPropertyCollection() as $property) {
            if ($property->isUtil() && $property->getField('CODE') === Tool::getOrderCouponField()) {
                $coupon = strval($this->orderData['ORDER_PROP'][$property->getField('ORDER_PROPS_ID')]);
            }
            if ($property->getField('CODE') === Tool::getOrderBonusField()) {
                $bonus = floatval($this->orderData['ORDER_PROP'][$property->getField('ORDER_PROPS_ID')]);
            }
        }

        if ($_SESSION['ITS_MAXMA_NEED_UPDATE_USER_AFTER_ORDER']) {
            $_SESSION['ITS_MAXMA_NEED_UPDATE_USER_AFTER_ORDER'] = false;
            Maxma::getInstance()->updateUser(intval($order->getUserId()), \CUser::GetByID($order->getUserId())->Fetch());
        }

        $calculate = Maxma::getInstance()->setOrder($basket, $order->getId(), $coupon, $bonus);
        $calculateData = $calculate->getData();
        self::$lastResult = $calculateData;
        $this->applyToBasket($basket, $calculateData);
    }

    protected function applyToBasket(BasketBase $basket, array $calculateData): void
    {
        foreach ($basket->getBasketItems() as $item) {
            $row = $calculateData['ROWS'][$item->getBasketCode()];
            if (empty($row)) {
                continue;
            }
            $discount = floatval($row['TOTAL_DISCOUNT']) - floatval($row['DISCOUNT']['AUTO']);
            Actions::applyToBasket(
                $this->orderData,
                [
                    'VALUE' => -$discount,
                    'UNIT' => Actions::VALUE_TYPE_SUMM,
                    'CURRENCY' => $this->orderData['CURRENCY'],
                    'LIMIT_VALUE' => 0,
                ],
                function ($row) use ($item) {
                    return $row['ID'] == $item->getBasketCode();
                }
            );
        }
    }

    public static function enableCalculation(): void
    {
        self::$enableCalculation = true;
    }

    public static function disableCalculation(): void
    {
        self::$enableCalculation = false;
    }

    public static function getLastResult(): array
    {
        return self::$lastResult;
    }
}
