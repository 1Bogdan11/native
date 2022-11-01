<?php

namespace Its\Maxma\Event;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Sale\Order;
use Bitrix\Main\HttpRequest;
use Its\Maxma\Api\Tool;
use Bitrix\Sale\EntityPropertyValue;
use Its\Maxma\Order\Coupon;
use Its\Maxma\Entity\OrderTable;
use Bitrix\Main\Config\Option;
use Its\Maxma\Module;
use Its\Maxma\Entity\HistoryTable;
use Its\Maxma\Order\Discount;

class OrderEvent
{
    public static function beforeOrderFinalAction(Event $event)
    {
        Discount::enableCalculation();
    }

    public static function afterOrderFinalAction(Event $event)
    {
        Discount::disableCalculation();
    }

    public static function componentOrderCreated(
        Order $order,
        &$userResult,
        HttpRequest $request,
        &$params,
        &$result,
        &$deliveries,
        &$paySystems
    ) {
        /** @var EntityPropertyValue $property */

        foreach ($order->getPropertyCollection() as $property) {
            if ($property->isUtil() && $property->getField('CODE') === Tool::getOrderCouponField()) {
                $property->setValue(Coupon::getFromSession());
                break;
            }
        }

        return true;
    }

    public static function orderSaved(Event $event)
    {
        Coupon::clear();
        return new EventResult(EventResult::SUCCESS);
    }

    public static function orderNewSendEmail($orderId, &$eventName, &$fields): bool
    {
        if ($orderId <= 0) {
            return true;
        }

        $order = Order::load($orderId);
        $maxmaOrderData = OrderTable::getList([
            'filter' => ['=ORDER_ID' => $order->getId()]
        ])->fetch();
        if (!$maxmaOrderData) {
            $order->refreshData();
            $order->save();
        }

        return true;
    }

    public static function orderSendEmail($orderId, &$eventName, &$fields, $statusId = ''): bool
    {
        if ($orderId <= 0) {
            return true;
        }

        $order = Order::load($orderId);

        $add = [
            'COUPON' => '',
            'DISCOUNT_BONUS' => 0,
            'DISCOUNT_COUPON' => 0,
            'BONUS_COLLECT' => 0,
            'DISCOUNT_TOTAL' => $order->getBasePrice() - $order->getPrice(),
            'ORDER_ITEMS' => $order->getBasket()->getPrice(),
            'ORDER_DELIVERY' => $order->getDeliveryPrice(),
            'ORDER_TOTAL' => $order->getPrice(),
        ];

        $maxmaOrderData = OrderTable::getList([
            'filter' => ['=ORDER_ID' => $order->getId()]
        ])->fetch();
        if ($maxmaOrderData) {
            $add['COUPON'] = $maxmaOrderData['COUPON'];

            $maxmaOrderRequestData = HistoryTable::getList([
                'order' => ['ID' => 'DESC'],
                'filter' => ['=ORDER_ID' => $order->getId()]
            ])->fetch();
            if ($maxmaOrderRequestData) {
                $add['DISCOUNT_BONUS'] = floatval($maxmaOrderRequestData['RESPONSE']['DISCOUNT']['BONUS']);
                $add['DISCOUNT_COUPON'] = floatval($maxmaOrderRequestData['RESPONSE']['DISCOUNT']['PROMOCODE']);
                $add['BONUS_COLLECT'] = floatval($maxmaOrderRequestData['RESPONSE']['BONUS']['COLLECT']);
            }
        }

        foreach ($add as $field => $value) {
            $fields["MAXMA_{$field}"] = $value;

            if ($field !== 'COUPON') {
                $fields["MAXMA_{$field}_FORMATTED"] = \CCurrencyLang::CurrencyFormat($value, $order->getCurrency());
            }

            if ($value) {
                $fields["MAXMA_{$field}_SAFE"] = str_replace(
                    '#VALUE#',
                    $fields["MAXMA_{$field}_FORMATTED"] ?? $value,
                    Option::get(Module::getId(), 'its_maxma_order_mail_html_' . mb_strtolower($field))
                );
            }
        }

        return true;
    }
}
