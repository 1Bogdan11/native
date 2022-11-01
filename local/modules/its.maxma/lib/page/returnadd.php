<?php

namespace Its\Maxma\Page;

use AdminConstructor\Edit\IntegerInput;
use AdminConstructor\Edit\ReadInput;
use AdminConstructor\Helper\Url;
use AdminConstructor\Lang;
use AdminConstructor\Page\Edit;
use Bitrix\Main\Localization\Loc;
use Its\Maxma\Entity\ReturnTable;
use Its\Maxma\Right;
use AdminConstructor\Edit\DateTimeInput;
use AdminConstructor\Edit\FloatInput;
use Its\Maxma\Entity\OrderTable;
use AdminConstructor\Edit\StringInput;
use Its\Maxma\Api\Maxma;

Loc::loadMessages(__FILE__);

class ReturnAdd extends Edit
{
    public const PAGE_EDIT = 'its-maxma-return-add.php';

    protected bool $close = false;
    protected int $orderId = 0;

    protected function setReadRight(): bool
    {
        return Right::getUserRight() > 'D';
    }

    protected function setWriteRight(): bool
    {
        return Right::getUserRight() >= 'W';
    }

    protected function setRequestId(): string
    {
        return '';
    }

    protected function setRequestCopy(): string
    {
        return '';
    }

    protected function enableButtons(): bool
    {
        return true;
    }

    protected function customButtons(): void
    {
    }

    protected function checkId(): bool
    {
        return false;
    }

    protected function checkCopyId(): bool
    {
        return false;
    }

    protected function getDefaultValues(string $id): array
    {
        return [];
    }

    protected function getEditValues(string $id): array
    {
        return [];
    }

    protected function getCopyValues(string $id): array
    {
        return [];
    }

    protected function prepareEditParams(): void
    {
        $this->setModalMode(true);

        $this->setTitle(
            Loc::getMessage('ITS_MAXMA_RETURNS_PAGE_TITLE', ['#ORDER_ID#' => $this->orderId])
        );

        $this->addTab('main', Lang::get('EDIT_MAIN_TAB'), [&$this, 'printMainTab']);

        if ($this->getRequest()->get('action') === 'close') {
            $this->close = true;
            return;
        }

        $this->orderId = intval($this->getRequest()->get('ORDER_ID'));
        $orderData = OrderTable::getList([
            'filter' => ['=ORDER_ID' => $this->orderId]
        ])->fetch();

        if (!$orderData) {
            $this->addError(
                Loc::getMessage('ITS_MAXMA_RETURNS_PAGE_ERROR_ORDER_NOT_FOUND', ['#ORDER_ID#' => $this->orderId]),
                true
            );
            return;
        }

        if ($orderData['ACCEPT'] !== 'Y') {
            $this->addError(
                Loc::getMessage('ITS_MAXMA_RETURNS_PAGE_INVALID_ORDER', ['#ORDER_ID#' => $this->orderId]),
                true
            );
            return;
        }


        foreach (ReturnTable::getEntity()->getFields() as $field) {
            switch ($field->getName()) {
                case 'ID':
                    $this->addInput($field->getName(), new ReadInput($field));
                    break;

                case 'DATE':
                    $this->addInput($field->getName(), new DateTimeInput($field));
                    break;

                case 'PRODUCT_CODE':
                    $this->addInput($field->getName(), new StringInput($field));
                    break;

                case 'QUANTITY':
                    $this->addInput($field->getName(), new IntegerInput($field));
                    break;

                case 'SUM':
                    $this->addInput($field->getName(), new FloatInput($field));
                    break;
            }
        }

        $this->setButtons(
            '',
            false,
            false,
            true,
            false,
        );
    }

    protected function printMainTab(): void
    {
        if ($this->close) {
            $this->beginCustomRow(true);
            ?>
            <script>
                window.opener.location.reload();
                window.close();
            </script>
            <?php
            $this->endCustomRow();
            return;
        }

        $this->printHiddenParam('ORDER_ID', $this->orderId);
        $this->printRow('ID');
        $this->printRow('DATE');
        $this->printRow('PRODUCT_CODE');
        $this->printRow('QUANTITY');
        $this->printRow('SUM');
    }

    protected function executeContextActions(): void
    {
    }

    protected function executeActions(array $values): void
    {
        if (!$this->isWriteRight()) {
            return;
        }

        $values['ORDER_ID'] = $this->orderId;

        $res = ReturnTable::add($values);
        if (!$res->isSuccess()) {
            $this->addErrors($res->getErrorMessages());
            return;
        }

        $return = Maxma::getInstance()->createReturn(
            $res->getId(),
            $values['DATE'],
            $values['ORDER_ID'],
            $values['PRODUCT_CODE'],
            $values['QUANTITY'],
            $values['SUM']
        );
        if (!$return->isSuccess()) {
            ReturnTable::delete($res->getId());
            $this->addErrors($return->getErrors());
            return;
        }

        if ($this->getRequest()->get('save')) {
            LocalRedirect(Url::make(static::PAGE_EDIT, ['action' => 'close']));
            return;
        }
    }
}
