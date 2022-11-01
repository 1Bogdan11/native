<?php

namespace Journal;

use Bitrix\Main\Sms\Event;
use Bitrix\Main\Result;
use Bitrix\Sale\Order;
use Bitrix\Main\Error;
use Bitrix\Main\PhoneNumber\Parser;
use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\Type\DateTime;

class SmsEvent
{

    public static function onOrderNewSendEmail($orderId, &$eventName, &$fields): bool
    {
        $sms = static::sendOrderSmsEvent('SMS_EVENT_SALE_NEW_ORDER', intval($orderId));
        if (!$sms->isSuccess()) {
            file_put_contents(
                "{$_SERVER['DOCUMENT_ROOT']}/SMS_EVENT_SALE_NEW_ORDER.log",
                print_r(
                    [
                        'DATE' => (new DateTime())->format('d.m.Y_H.i.s'),
                        'ERROR' => $sms->getErrorMessages(),
                        'orderId' => $orderId,
                        'eventName' => $eventName,
                        'fields' => $fields,
                    ],
                    true
                )
            );
        }
        return true;
    }

    public static function onOrderPaySendEmail($orderId, &$eventName, &$fields): bool
    {
        $sms = static::sendOrderSmsEvent('SMS_EVENT_SALE_ORDER_PAY', intval($orderId));
        if (!$sms->isSuccess()) {
            file_put_contents(
                "{$_SERVER['DOCUMENT_ROOT']}/SMS_EVENT_SALE_ORDER_PAY.log",
                print_r(
                    [
                        'DATE' => (new DateTime())->format('d.m.Y_H.i.s'),
                        'ERROR' => $sms->getErrorMessages(),
                        'orderId' => $orderId,
                        'eventName' => $eventName,
                        'fields' => $fields,
                    ],
                    true
                )
            );
        }
        return true;
    }

    public static function onOrderStatusSendEmail($orderId, &$eventName, &$fields, $statusId): bool
    {
        $sms = static::sendOrderSmsEvent("SMS_EVENT_SALE_STATUS_{$statusId}", intval($orderId));
        if (!$sms->isSuccess()) {
            file_put_contents(
                "{$_SERVER['DOCUMENT_ROOT']}/SMS_EVENT_SALE_STATUS_{$statusId}.log",
                print_r(
                    [
                        'DATE' => (new DateTime())->format('d.m.Y_H.i.s'),
                        'ERROR' => $sms->getErrorMessages(),
                        'orderId' => $orderId,
                        'eventName' => $eventName,
                        'fields' => $fields,
                        'statusId' => $statusId,
                    ],
                    true
                )
            );
        }
        return true;
    }


    protected static function sendOrderSmsEvent(string $eventType, int $orderId): Result
    {
        $fields = [];
        $result = new Result();

        $order = Order::load($orderId);
        if ($order) {
            $properties = $order->getPropertyCollection();
            $phone = $properties->getPhone();
            $payerName = $properties->getPayerName();

            if (!$phone) {
                $result->addError(new Error('Phone is empty!'));
            }
            $number = Parser::getInstance()->parse($phone->getValue());
            if (!$number->isValid()) {
                $result->addError(new Error('Phone number is not valid!'));
            }
            if (!$payerName || empty($payerName->getValue())) {
                $result->addError(new Error('Payer name is empty!'));
            }

            $fields = [
                'USER_PHONE' => $number->format(Format::E164),
                'USER_NAME' => $payerName->getValue(),
                'ORDER_ID' => $order->getId(),
                'ORDER_NUMBER' => $order->getField('ACCOUNT_NUMBER'),
            ];
        } else {
            $result->addError(new Error('Order not found!'));
        }

        if (!$result->isSuccess()) {
            return $result;
        }

        $sms = new Event($eventType, $fields);
        $sms->setSite('s1');
        $sms->setLanguage('ru');
        return $sms->send(true);
    }
}
