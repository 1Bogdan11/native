<?php

namespace Its\Maxma\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Entity\FloatField;
use Bitrix\Iblock\ElementTable;

Loc::loadMessages(__FILE__);

class ReturnTable extends DataManager
{
    public static function getTableName()
    {
        return 'its_maxma_return';
    }

    /**
     * @return array
     * @throws \Exception
     */
    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_RETURN_TABLE_ID'),
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new DatetimeField(
                'DATE',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_RETURN_TABLE_DATE'),
                    'required' => true,
                ]
            ),
            new IntegerField(
                'ORDER_ID',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_RETURN_TABLE_ORDER_ID'),
                    'required' => true,
                ]
            ),
            new ReferenceField(
                'ORDER',
                OrderTable::class,
                ['=this.ORDER_ID' => 'ref.ORDER_ID'],
                ['join_type' => 'LEFT']
            ),
            new FloatField(
                'SUM',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_RETURN_TABLE_SUM'),
                    'required' => true,
                    'validation' => function () {
                        return [
                            function ($value) {
                                if (floatval($value) > 0) {
                                    return true;
                                }
                                return Loc::getMessage(
                                    'ITS_MAXMA_RETURN_TABLE_ERROR_NOT_NULL',
                                    ['#FIELD#' => Loc::getMessage('ITS_MAXMA_RETURN_TABLE_SUM')]
                                );
                            },
                        ];
                    },
                ]
            ),
            new IntegerField(
                'PRODUCT_CODE',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_RETURN_TABLE_PRODUCT_CODE'),
                    'required' => true,
                ]
            ),
            new IntegerField(
                'QUANTITY',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_RETURN_TABLE_QUANTITY'),
                    'required' => true,
                    'validation' => function () {
                        return [
                            function ($value) {
                                if (intval($value) > 0) {
                                    return true;
                                }
                                return Loc::getMessage(
                                    'ITS_MAXMA_RETURN_TABLE_ERROR_NOT_NULL',
                                    ['#FIELD#' => Loc::getMessage('ITS_MAXMA_RETURN_TABLE_QUANTITY')]
                                );
                            },
                        ];
                    },
                ]
            ),
        ];
    }
}
