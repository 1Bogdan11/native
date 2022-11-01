<?php

namespace Journal;

use Bitrix\Main\Loader;
use Bitrix\Sale\OrderTable;
use Bitrix\Sale\Delivery\Services\Manager;
use Bitrix\Sale\Internals\StatusTable;
use Bitrix\Main\Config\Option;
use Bitrix\Sale\Order;
use Bitrix\Main\Type\DateTime;

class BoxberryStatusChecker
{
    protected const FINAL_STATUS = 'Выдано';

    protected string $checkStatusId = '';
    protected string $finalStatusId = '';
    protected array $ordersIds = [];

    public static function registerAgent(string $checkStatusId, string $finalStatusId): void
    {
        \CAgent::AddAgent(
            static::getFunctionName($checkStatusId, $finalStatusId),
            '',
            'N',
            60 * 30,
            '',
            'Y',
            (new DateTime())->add('60 seconds'),
            90
        );
    }

    public static function getFunctionName(string $checkStatusId, string $finalStatusId): string
    {
        return __CLASS__ . "::check('{$checkStatusId}', '{$finalStatusId}');";
    }

    public static function check(string $checkStatusId, string $finalStatusId): string
    {
        $sender = new static($checkStatusId, $finalStatusId);
        return static::getFunctionName($checkStatusId, $finalStatusId);
    }

    public function __construct(string $checkStatusId, string $finalStatusId)
    {
        $this->checkStatusId = $checkStatusId;
        $this->finalStatusId = $finalStatusId;

        if (!Loader::includeModule('up.boxberrydelivery')) {
            throw new \Exception('Boxberry module not installed!');
        }

        $checkStatuses = StatusTable::getList([
            'filter' => [
                '=TYPE' => 'O',
                '=ID' => [$this->checkStatusId, $this->finalStatusId]
            ]
        ]);

        if ($checkStatuses->getSelectedRowsCount() !== 2) {
            throw new \Exception('Invalid statuses');
        }

        $this->checkStatuses();
    }

    protected function getDeliveriesIds(): array
    {
        $deliveriesIds = [];
        $deliveries = Manager::getActiveList();
        foreach ($deliveries as $delivery) {
            if (stristr($delivery['CODE'], 'boxberry') !== false && $delivery['CLASS_NAME'] === '\Bitrix\Sale\Delivery\Services\AutomaticProfile') {
                $deliveriesIds[] = intval($delivery['ID']);
            }
        }
        return $deliveriesIds;
    }

    protected function getOrders(): array
    {
        $ordersIds = [];
        $deliveriesIds = $this->getDeliveriesIds();
        if (!$deliveriesIds) {
            return [];
        }
        $resource = OrderTable::getList([
            'select' => ['ACCOUNT_NUMBER', 'ID'],
            'filter' => [
                '=STATUS_ID' => $this->checkStatusId,
                '=DELIVERY_ID' => $deliveriesIds,
            ]
        ]);
        while ($orderData = $resource->fetch()) {
            $ordersIds[] = $orderData;
        }
        return $ordersIds;
    }

    protected function checkStatuses(): void
    {
        $boxberry = new \CBoxberry();
        $boxberry->initApi();
        $orderField = Option::get('up.boxberrydelivery', 'BB_ACCOUNT_NUMBER') === 'Y' ? 'ACCOUNT_NUMBER' : 'ID';
        foreach ($this->getOrders() as $orderData) {
            $statusesResponse = $boxberry->methodExec(
                'ListStatuses',
                0,
                ['ImId=' . $orderData[$orderField]]
            );
            $statuses = [];
            foreach ($statusesResponse as $status) {
                if (!is_array($status)) {
                    continue;
                }
                $statuses[strtotime($status['Date'])] = $status['Name'];
            }
            krsort($statuses);

            if (strcasecmp(current($statuses), static::FINAL_STATUS) === 0) {
                $order = Order::load($orderData['ID']);
                $order->setField('STATUS_ID', $this->finalStatusId);
                $order->save();
            }
        }
    }
}
