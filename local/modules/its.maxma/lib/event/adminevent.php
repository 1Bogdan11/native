<?php

namespace Its\Maxma\Event;

use Its\Maxma\Entity\OrderTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class AdminEvent
{
    /** @param \CAdminList $list */
    public static function onBuildList(&$list): void
    {
        /** @var \CAdminListRow $row*/

        if ($list->table_id !== 'tbl_sale_order') {
            return;
        }

        $list->aHeaders['MAXMA'] = [
            'id' => 'MAXMA',
            'content' => Loc::getMessage('ADMIN_LIST_ORDERS_MAXMA_HEADER'),
        ];
        $list->AddVisibleHeaderColumn('MAXMA');

        foreach ($list->aRows as $row) {
            $orderId = intval($row->id);
            $orderData = OrderTable::getList([
                'filter' => ['=ORDER_ID' => $orderId],
                'select' => ['ACCEPT', 'CANCEL'],
            ])->fetch();
            if ($orderData['ACCEPT'] === 'Y') {
                $row->AddViewField(
                    'MAXMA',
                    '<b style="color:green">' . Loc::getMessage('ADMIN_LIST_ORDERS_MAXMA_ACCEPT') . '</b>'
                );
            } elseif ($orderData['CANCEL'] === 'Y') {
                $row->AddViewField(
                    'MAXMA',
                    '<b style="color:red">' . Loc::getMessage('ADMIN_LIST_ORDERS_MAXMA_CANCEL') . '</b>'
                );
            } elseif ($orderData) {
                $row->AddViewField('MAXMA', Loc::getMessage('ADMIN_LIST_ORDERS_MAXMA_Y'));
            } else {
                $row->AddViewField('MAXMA', Loc::getMessage('ADMIN_LIST_ORDERS_MAXMA_N'));
            }
        }
    }
}
