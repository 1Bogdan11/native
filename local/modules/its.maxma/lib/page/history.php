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
use Its\Maxma\Entity\HistoryTable;
use Its\Maxma\Right;
use AdminConstructor\Data\FloatFilter;
use AdminConstructor\Helper\Url;
use Its\Maxma\Api\Maxma;
use Bitrix\Sale\Order;
use Bitrix\Main\Loader;
use AdminConstructor\Data\DateTimeFilter;

Loc::loadMessages(__FILE__);
Loader::includeModule('sale');

class History extends Table
{
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
        return 'its_maxma_history_table';
    }

    protected function setColumnRowId(): string
    {
        return 'ID';
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

    protected function prepareParams(): void
    {
        $this->setTitle(Loc::getMessage('ITS_MAXMA_HISTORY_PAGE_TITLE'));
        $this->setDefaultOrder('ID', 'DESC');
        $this->setTotalRow(false);

        if ($this->isWriteRight() && !$this->isReference()) {
            $this->addGroupAction('delete', Lang::get('ACTION_DELETE'));
        }

        foreach (HistoryTable::getEntity()->getFields() as $field) {
            switch ($field->getName()) {
                case 'ID':
                case 'ORDER_ID':
                case 'RETURN_ID':
                    $this->addHeader($field->getName(), $field->getTitle());
                    $this->addFilter(new IntegerFilter($field->getName(), $field));
                    break;

                case 'DATE':
                    $this->addHeader($field->getName(), $field->getTitle());
                    $this->addFilter(new DateTimeFilter($field->getName(), $field));
                    break;

                case 'RESPONSE':
                    $this->addHeader($field->getName(), $field->getTitle());
                    break;
            }
        }
    }

    protected function getResultD7(array $parameters): Result
    {
        return HistoryTable::getList($parameters);
    }

    protected function modifyRow(AdminListRow &$row, array $rowData, AdminList &$list): void
    {
        $row->AddViewField('ORDER_ID', $rowData['ORDER_ID'] ?: '');
        $row->AddViewField('RETURN_ID', $rowData['RETURN_ID'] ?: '');

        $response = var_export($rowData['RESPONSE'], true);
        $open = Loc::getMessage('ITS_MAXMA_HISTORY_PAGE_OPEN');
        $close = Loc::getMessage('ITS_MAXMA_HISTORY_PAGE_CLOSE');
        $row->AddViewField(
            'RESPONSE',
            "<div class='its-maxma-pre-spoiler'>"
            . "<button class='adm-btn open' type='button'>{$open}</button>"
            . "<button class='adm-btn close' type='button'>{$close}</button>"
            . "<pre>{$response}</pre>"
            . "</div>"
        );
    }

    protected function setRowActions(array &$actions, array $rowData, AdminList &$list): void
    {
        if ($this->isWriteRight()) {
            $actions[] = [
                'ICON' => static::ICON_LIST_DELETE,
                'TEXT' => Lang::get('ACTION_DELETE'),
                'ACTION' => $this->jsDeleteAction($rowData['ID']),
            ];
        }
    }

    protected function executeActions(AdminList &$list, array $parameters): void
    {
        if (!$this->isWriteRight()) {
            return;
        }

        $request = $this->getRequest();

        if ($request->get('action_target') === 'selected') {
            $arElements = [];
            $parameters['select'] = ['ID'];
            $resElements = HistoryTable::getList($parameters);
            while ($arElement = $resElements->fetch()) {
                $arElements[] = $arElement['ID'];
            }
        } else {
            $arElements = $list->GroupAction();
        }

        $action = empty($request->get('action_button')) ? $request->get('action') : $request->get('action_button');

        foreach ($arElements as $id) {
            switch ($action) {
                case 'delete':
                    $res = HistoryTable::delete($id);
                    break;
            }

            if (isset($res) && !$res->isSuccess()) {
                foreach ($res->getErrorMessages() as $error) {
                    $list->AddGroupError($error, $id);
                }
            }
        }
    }

    protected function executeTotalRow(AdminListRow &$row, array $headers, array $parameters, AdminList &$list): void
    {
    }
}

