<?php

namespace Its\Maxma\Agent;

use Its\Maxma\Module;
use Bitrix\Main\Type\DateTime;
use Its\Maxma\Entity\OrderTable;
use Its\Maxma\Api\Tool;
use Bitrix\Main\Config\Option;
use Its\Maxma\Api\Maxma;
use Bitrix\Sale\Order;
use Bitrix\Main\Loader;

class OrderSender
{
    protected const ORDER_SEND_LIMIT = 2;

    protected array $canceled = [];
    protected array $accepted = [];

    public static function register(): void
    {
        \CAgent::AddAgent(
            static::getFunctionName(),
            Module::getId(),
            'N',
            60 * 10,
            '',
            'Y',
            (new DateTime())->add('60 seconds'),
            100
        );
    }

    public static function unregister(): void
    {
        \CAgent::RemoveAgent(static::getFunctionName(), Module::getId());
    }

    public static function getFunctionName(): string
    {
        return __CLASS__ . '::check();';
    }

    public static function check(): string
    {
        $sender = new static();
        $sender->send();
        return static::getFunctionName();
    }

    public function __construct()
    {
        Loader::includeModule('sale');
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');

        $timeout = Tool::getOrderSendTimeout();

        if (Option::get(Module::getId(), 'its_maxma_enable_cancel_order') === 'Y') {
            $resourceCancel = OrderTable::getList([
                'order' => [
                    'ID' => 'ASC',
                ],
                'filter' => [
                    '=ACCEPT' => 'N',
                    '=CANCEL' => 'N',
                    '=ORDER.CANCELED' => 'Y',
                    '<=ORDER.DATE_CANCELED' => (new DateTime())->add("- {$timeout} minutes"),
                ],
                'limit' => static::ORDER_SEND_LIMIT,
                'select' => ['ID' => 'ORDER_ID'],
            ]);

            while ($orderData = $resourceCancel->fetch()) {
                $this->canceled[] = intval($orderData['ID']);
            }
        }

        if (Option::get(Module::getId(), 'its_maxma_enable_accept_order') === 'Y') {
            $resourceAccept = OrderTable::getList([
                'order' => [
                    'ID' => 'ASC',
                ],
                'filter' => [
                    '=ACCEPT' => 'N',
                    '=CANCEL' => 'N',
                    '=ORDER.STATUS_ID' => Tool::getOrderEndStatus(),
                    '<=ORDER.DATE_STATUS' => (new DateTime())->add("- {$timeout} minutes"),
                ],
                'limit' => static::ORDER_SEND_LIMIT,
                'select' => ['ID' => 'ORDER_ID'],
            ]);

            while ($orderData = $resourceAccept->fetch()) {
                $this->accepted[] = intval($orderData['ID']);
            }
        }
    }

    public function send(): void
    {
        $maxma = Maxma::getInstance();
        foreach ($this->canceled as $orderId) {
            $maxma->cancelOrder($orderId);
        }
        foreach ($this->accepted as $orderId) {
            $order = Order::load($orderId);
            $maxma->acceptOrder($order);
        }
    }
}
