<?php

namespace Its\Maxma\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Sale\OrderTable as InternalOrderTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Entity\BooleanField;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\Entity\StringField;

Loc::loadMessages(__FILE__);

class OrderTable extends DataManager
{
    public static function getTableName()
    {
        return 'its_maxma_order';
    }

    /**
     * @return array
     * @throws \Exception
     */
    public static function getMap()
    {
        return [
            new IntegerField(
                'ORDER_ID',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_ORDER_TABLE_ORDER_ID'),
                    'primary' => true,
                    'required' => true,
                ]
            ),
            new ReferenceField(
                'ORDER',
                InternalOrderTable::class,
                ['=this.ORDER_ID' => 'ref.ID'],
                ['join_type' => 'LEFT']
            ),
            new StringField(
                'COUPON',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_ORDER_TABLE_COUPON'),
                ]
            ),
            new BooleanField(
                'ACCEPT',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_ORDER_TABLE_ACCEPT'),
                    'values' => ['N', 'Y'],
                ]
            ),
            new BooleanField(
                'CANCEL',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_ORDER_TABLE_CANCEL'),
                    'values' => ['N', 'Y'],
                ]
            ),
            new ExpressionField(
                'RETURN',
                '(select sum(SUM) from ' . ReturnTable::getTableName() . ' where ORDER_ID=%s)',
                ['ORDER_ID'],
                [
                    'title' => Loc::getMessage('ITS_MAXMA_ORDER_TABLE_RETURN'),
                ]
            ),
            new ExpressionField(
                'ORDER_LID',
                '%s',
                ['ORDER.LID'],
                [
                    'title' => Loc::getMessage('ITS_MAXMA_ORDER_TABLE_ORDER_LID'),
                ]
            ),
            new ExpressionField(
                'ORDER_STATUS_ID',
                '%s',
                ['ORDER.STATUS_ID'],
                [
                    'title' => Loc::getMessage('ITS_MAXMA_ORDER_TABLE_ORDER_STATUS_ID'),
                ]
            ),
            new ExpressionField(
                'ORDER_COMMENTS',
                '%s',
                ['ORDER.COMMENTS'],
                [
                    'title' => Loc::getMessage('ITS_MAXMA_ORDER_TABLE_ORDER_COMMENTS'),
                ]
            ),
            new ExpressionField(
                'ORDER_PRICE',
                '%s',
                ['ORDER.PRICE'],
                [
                    'title' => Loc::getMessage('ITS_MAXMA_ORDER_TABLE_ORDER_PRICE'),
                ]
            ),
            new ExpressionField(
                'ORDER_DATE',
                '%s',
                ['ORDER.DATE_INSERT'],
                [
                    'title' => Loc::getMessage('ITS_MAXMA_ORDER_TABLE_ORDER_DATE'),
                ]
            ),
        ];
    }
}
