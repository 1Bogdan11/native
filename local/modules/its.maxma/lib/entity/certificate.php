<?php

namespace Its\Maxma\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Sale\OrderTable;
use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Entity\FloatField;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\BooleanField;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class CertificateTable extends DataManager
{
    public static function getTableName()
    {
        return 'its_maxma_certificate';
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
                    'title' => Loc::getMessage('ITS_MAXMA_CERTIFICATE_TABLE_ID'),
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new IntegerField(
                'ORDER_ID',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_CERTIFICATE_TABLE_ORDER_ID'),
                    'required' => true,
                ]
            ),
            new ReferenceField(
                'ORDER',
                OrderTable::class,
                ['=this.ORDER_ID' => 'ref.ID'],
                ['join_type' => 'LEFT']
            ),
            new IntegerField(
                'ELEMENT_ID',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_CERTIFICATE_TABLE_ELEMENT_ID'),
                    'required' => true,
                ]
            ),
            new ReferenceField(
                'ELEMENT',
                ElementTable::class,
                ['=this.ELEMENT_ID' => 'ref.ID'],
                ['join_type' => 'LEFT']
            ),
            new StringField(
                'CODE',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_CERTIFICATE_TABLE_CODE'),
                ]
            ),
            new FloatField(
                'SUM',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_CERTIFICATE_TABLE_SUM'),
                ]
            ),
            new DatetimeField(
                'EXPIRE',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_CERTIFICATE_TABLE_EXPIRE'),
                ]
            ),
            new BooleanField(
                'EXPIRE_INF',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_CERTIFICATE_TABLE_EXPIRE_INF'),
                    'values' => ['N', 'Y'],
                ]
            ),
            new StringField(
                'NUMBER',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_CERTIFICATE_TABLE_NUMBER'),
                ]
            ),
        ];
    }
}
