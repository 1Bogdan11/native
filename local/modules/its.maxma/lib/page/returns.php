<?php

namespace Its\Maxma\Page;

use AdminConstructor\Data\IntegerFilter;
use AdminConstructor\Page\Table;
use AdminConstructor\Structure\ReferenceItem;
use AdminConstructor\Structure\ReferenceSearch;
use AdminConstructor\System\AdminList;
use AdminConstructor\System\AdminListRow;
use Bitrix\Main\DB\Result;
use Bitrix\Main\Localization\Loc;
use Its\Maxma\Entity\ReturnTable;
use Its\Maxma\Right;
use AdminConstructor\Data\DateTimeFilter;
use AdminConstructor\Data\FloatFilter;
use AdminConstructor\Helper\Url;

Loc::loadMessages(__FILE__);

class Returns extends Table
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
        return 'its_maxma_returns_table';
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
        $this->setTitle(Loc::getMessage('ITS_MAXMA_RETURNS_PAGE_TITLE'));
        $this->setDefaultOrder('ID', 'DESC');
        $this->setTotalRow(false);

        foreach (ReturnTable::getEntity()->getFields() as $field) {
            switch ($field->getName()) {
                case 'ID':
                case 'ORDER_ID':
                case 'PRODUCT_CODE':
                case 'QUANTITY':
                    $this->addHeader($field->getName(), $field->getTitle());
                    $this->addFilter(new IntegerFilter($field->getName(), $field));
                    break;

                case 'SUM':
                    $this->addHeader($field->getName(), $field->getTitle());
                    $this->addFilter(new FloatFilter($field->getName(), $field));
                    break;

                case 'DATE':
                    $this->addHeader($field->getName(), $field->getTitle());
                    $this->addFilter(new DateTimeFilter($field->getName(), $field));
                    break;
            }
        }
    }

    protected function getResultD7(array $parameters): Result
    {
        return ReturnTable::getList($parameters);
    }

    protected function modifyRow(AdminListRow &$row, array $rowData, AdminList &$list): void
    {
        $orderUrl = Url::make('sale_order_view.php', ['ID' => $rowData['ORDER_ID']]);
        $row->AddViewField('ORDER_ID', "<b><a href='{$orderUrl}'>№{$rowData['ORDER_ID']}</a></b>");
    }

    protected function setRowActions(array &$actions, array $rowData, AdminList &$list): void
    {
        $actions[] = [
            'TEXT' => Loc::getMessage('ITS_MAXMA_RETURNS_PAGE_HISTORY'),
            'ACTION' => $this->jsRedirectAction(
                'its-maxma-history.php',
                [
                    'set_filter' => 'Y',
                    'filter_RETURN_ID' => $rowData['ID'],
                ]
            ),
        ];
    }

    protected function executeActions(AdminList &$list, array $parameters): void
    {
    }

    protected function executeTotalRow(AdminListRow &$row, array $headers, array $parameters, AdminList &$list): void
    {
    }
}

