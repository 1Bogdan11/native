<?php

namespace Its\Maxma\Page;

use AdminConstructor\Data\BooleanFilter;
use AdminConstructor\Data\IntegerFilter;
use AdminConstructor\Data\StringFilter;
use AdminConstructor\Lang;
use AdminConstructor\Page\Table;
use AdminConstructor\Structure\ReferenceItem;
use AdminConstructor\Structure\ReferenceSearch;
use AdminConstructor\System\AdminList;
use AdminConstructor\System\AdminListRow;
use Bitrix\Main\DB\Result;
use Bitrix\Main\Localization\Loc;
use Its\Maxma\Entity\OrderTable;
use Its\Maxma\Right;
use AdminConstructor\Data\FloatFilter;
use AdminConstructor\Helper\Url;
use Its\Maxma\Api\Maxma;
use Bitrix\Sale\Order;
use Bitrix\Main\Loader;
use Bitrix\Sale\Internals\StatusLangTable;
use AdminConstructor\Data\EnumFilter;
use AdminConstructor\Data\DateTimeFilter;

Loc::loadMessages(__FILE__);
Loader::includeModule('sale');

class Orders extends Table
{
    protected static $orderStatuses = [];

    protected function setReadRight(): bool
    {
        return Right::getUserRight() > 'D';
    }

    protected function setWriteRight(): bool
    {
        return Right::getUserRight() > 'D';
    }

    protected function setTableId(): string
    {
        return 'its_maxma_orders_table';
    }

    protected function setColumnRowId(): string
    {
        return 'ORDER_ID';
    }

    protected function getReferenceItem(string $id = null): ReferenceItem
    {
        return new ReferenceItem();
    }

    protected function getReferenceSearch(string $search = null): ReferenceSearch
    {
        $return = new ReferenceSearch($search);
        return $return;
    }

    protected static function getOrderStatuses(): array
    {
        if (!static::$orderStatuses) {
            $resource = StatusLangTable::getList([
                'filter' => [
                    '=LID' => LANGUAGE_ID,
                    '=STATUS.TYPE' => 'O',
                ]
            ]);
            while ($data = $resource->fetch()) {
                static::$orderStatuses[$data['STATUS_ID']] = $data['NAME'];
            }
        }

        return static::$orderStatuses;
    }

    protected function prepareParams(): void
    {
        $this->setTitle(Loc::getMessage('ITS_MAXMA_ORDERS_PAGE_TITLE'));
        $this->setDefaultOrder('ORDER_ID', 'DESC');
        $this->setTotalRow(false);

        if ($this->isWriteRight() && !$this->isReference()) {
            $this->addGroupAction('accept', Loc::getMessage('ITS_MAXMA_ORDERS_PAGE_ACCEPT'));
            $this->addGroupAction('cancel', Loc::getMessage('ITS_MAXMA_ORDERS_PAGE_CANCEL'));
        }

        foreach (OrderTable::getEntity()->getFields() as $field) {
            switch ($field->getName()) {
                case 'ORDER_ID':
                    $this->addHeader($field->getName(), $field->getTitle());
                    $this->addFilter(new IntegerFilter($field->getName(), $field));
                    break;

                case 'ORDER_DATE':
                    $this->addHeader($field->getName(), $field->getTitle());
                    $this->addFilter(new DateTimeFilter($field->getName(), $field));
                    break;

                case 'COUPON':
                    $this->addHeader($field->getName(), $field->getTitle());
                    $this->addFilter(new StringFilter($field->getName(), $field));
                    break;

                case 'ACCEPT':
                case 'CANCEL':
                    $this->addHeader($field->getName(), $field->getTitle());
                    $this->addFilter(new BooleanFilter($field->getName(), $field));
                    break;

                case 'RETURN':
                case 'ORDER_PRICE':
                    $this->addHeader($field->getName(), $field->getTitle());
                    $this->addFilter(new FloatFilter($field->getName(), $field));
                    break;

                case 'ORDER_STATUS_ID':
                    $this->addHeader($field->getName(), $field->getTitle());
                    $this->addFilter(new EnumFilter($field->getName(), $field, null, static::getOrderStatuses(), true));
                    break;

                case 'ORDER_LID':
                case 'ORDER_COMMENTS':
                    $this->addHeader($field->getName(), $field->getTitle());
                    break;
            }
        }
    }

    protected function getResultD7(array $parameters): Result
    {
        $parameters['select'] = ['*', 'RETURN', 'ORDER_PRICE', 'ORDER_STATUS_ID', 'ORDER_LID', 'ORDER_COMMENTS', 'ORDER_DATE'];
        return OrderTable::getList($parameters);
    }

    protected function modifyRow(AdminListRow &$row, array $rowData, AdminList &$list): void
    {
        $orderUrl = Url::make('sale_order_view.php', ['ID' => $rowData['ORDER_ID']]);
        $row->AddViewField('ORDER_ID', "<b><a href='{$orderUrl}'>№{$rowData['ORDER_ID']}</a></b>");
        $row->AddViewField('ACCEPT', Lang::getBooleanType(strval($rowData['ACCEPT'])));
        $row->AddViewField('CANCEL', Lang::getBooleanType(strval($rowData['CANCEL'])));
        $row->AddViewField('RETURN', round(floatval($rowData['RETURN']), 2));
        $row->AddViewField('ORDER_PRICE', round(floatval($rowData['ORDER_PRICE']), 2));
        $row->AddViewField('ORDER_STATUS_ID', "[{$rowData['ORDER_STATUS_ID']}] {$this::getOrderStatuses()[$rowData['ORDER_STATUS_ID']]}");
    }

    protected function setRowActions(array &$actions, array $rowData, AdminList &$list): void
    {
        if ($this->isWriteRight()) {
            if ($rowData['ACCEPT'] !== 'Y' && $rowData['CANCEL'] !== 'Y') {
                $confirm = json_encode(Loc::getMessage(
                    'ITS_MAXMA_ORDERS_PAGE_CONFIRM_ACTION',
                    [
                        '#ACTION#' => mb_strtolower(Loc::getMessage('ITS_MAXMA_ORDERS_PAGE_ACCEPT')),
                        '#ORDER_ID#' => $rowData['ORDER_ID'],
                    ]
                ));
                $actions[] = [
                    'TEXT' => Loc::getMessage('ITS_MAXMA_ORDERS_PAGE_ACCEPT'),
                    'ACTION' => "if (confirm({$confirm})) {{$this->jsAction($rowData['ORDER_ID'], 'accept')};};",
                ];
                $confirm = json_encode(Loc::getMessage(
                    'ITS_MAXMA_ORDERS_PAGE_CONFIRM_ACTION',
                    [
                        '#ACTION#' => mb_strtolower(Loc::getMessage('ITS_MAXMA_ORDERS_PAGE_CANCEL')),
                        '#ORDER_ID#' => $rowData['ORDER_ID'],
                    ]
                ));
                $actions[] = [
                    'TEXT' => Loc::getMessage('ITS_MAXMA_ORDERS_PAGE_CANCEL'),
                    'ACTION' => "if (confirm({$confirm})) {{$this->jsAction($rowData['ORDER_ID'], 'cancel')};};",
                ];
            }

            if ($rowData['ACCEPT'] === 'Y') {
                $actions[] = [
                    'TEXT' => Loc::getMessage('ITS_MAXMA_ORDERS_PAGE_RETURNS'),
                    'ACTION' => $this->jsRedirectAction(
                        'its-maxma-returns.php',
                        [
                            'set_filter' => 'Y',
                            'filter_ORDER_ID' => $rowData['ORDER_ID'],
                        ]
                    ),
                ];

                $windowUrl = Url::make('its-maxma-return-add.php', ['ORDER_ID' => $rowData['ORDER_ID']]);
                $windowName = "'return_add_{$rowData['ORDER_ID']}'";
                $windowParams = '`scrollbars=yes,resizable=yes,width=800,height=500,top=${Math.floor(((screen.height - 500) / 2) - 14)},left=${Math.floor(((screen.width - 800) / 2) - 5)}`';
                $actions[] = [
                    'TEXT' => Loc::getMessage('ITS_MAXMA_ORDERS_PAGE_RETURN'),
                    'ACTION' => "window.open('{$windowUrl}', {$windowName}, {$windowParams});",
                ];
            }
        }

        $actions[] = [
            'TEXT' => Loc::getMessage('ITS_MAXMA_ORDERS_PAGE_HISTORY'),
            'ACTION' => $this->jsRedirectAction(
                'its-maxma-history.php',
                [
                    'set_filter' => 'Y',
                    'filter_ORDER_ID' => $rowData['ORDER_ID'],
                ]
            ),
        ];
    }

    protected function executeActions(AdminList &$list, array $parameters): void
    {
        if (!$this->isWriteRight()) {
            return;
        }

        $request = $this->getRequest();

        if ($request->get('action_target') === 'selected') {
            $arElements = [];
            $parameters['select'] = ['ORDER_ID'];
            $resElements = OrderTable::getList($parameters);
            while ($arElement = $resElements->fetch()) {
                $arElements[] = $arElement['ORDER_ID'];
            }
        } else {
            $arElements = $list->GroupAction();
        }

        $action = !empty($request->get('action_button')) ? $request->get('action_button') : $request->get('action');
        $maxma = Maxma::getInstance();

        foreach ($arElements as $id) {
            switch ($action) {
                case 'accept':
                    $order = Order::load($id);
                    $res = $maxma->acceptOrder($order);
                    break;
                case 'cancel':
                    $res = $maxma->cancelOrder($id);
                    break;
            }

            if (isset($res) && !$res->isSuccess()) {
                foreach ($res->getErrors() as $error) {
                    $list->AddGroupError("#{$id}: {$error}", $id);
                }
            }
        }
    }

    protected function executeTotalRow(AdminListRow &$row, array $headers, array $parameters, AdminList &$list): void
    {
    }
}

