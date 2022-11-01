<?php

namespace Its\Maxma\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Entity\TextField;
use Bitrix\Main\Entity\BooleanField;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\ORM\Fields\StringField;

Loc::loadMessages(__FILE__);

class HistoryTable extends DataManager
{
    public static function getTableName()
    {
        return 'its_maxma_history';
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
                    'title' => Loc::getMessage('ITS_MAXMA_HISTORY_TABLE_ID'),
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new DatetimeField(
                'DATE',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_HISTORY_TABLE_DATE'),
                ]
            ),
            new IntegerField(
                'ORDER_ID',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_HISTORY_TABLE_ORDER_ID'),
                ]
            ),
            new ReferenceField(
                'ORDER',
                OrderTable::class,
                ['=this.ORDER_ID' => 'ref.ORDER_ID'],
                ['join_type' => 'LEFT']
            ),
            new IntegerField(
                'RETURN_ID',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_HISTORY_TABLE_RETURN_ID'),
                ]
            ),
            new ReferenceField(
                'RETURN',
                OrderTable::class,
                ['=this.RETURN_ID' => 'ref.ID'],
                ['join_type' => 'LEFT']
            ),
            new TextField(
                'RESPONSE',
                [
                    'title' => Loc::getMessage('ITS_MAXMA_HISTORY_TABLE_RESPONSE'),
                    'save_data_modification' => function () {
                        return [
                            function ($value) {
                                return json_encode($value);
                            }
                        ];
                    },
                    'fetch_data_modification' => function () {
                        return [
                            function ($value) {
                                return json_decode($value, true);
                            }
                        ];
                    }
                ]
            ),
        ];
    }

    public static function add(array $data)
    {
        $data['DATE'] = new DateTime();
        return parent::add($data);
    }

    /**
     * @inernal
     * @param $primary
     * @param array $data
     * @return \Bitrix\Main\ORM\Data\UpdateResult
     * @throws \Exception
     */
    public static function update($primary, array $data)
    {
        return parent::update($primary, $data);
    }
}
