<?php

namespace Journal\Favorite;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Entity\TextField;
use Bitrix\Main\UserTable;

class FavoriteTable extends DataManager
{
    public static function getTableName()
    {
        return 'its_favorite';
    }

    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'title' => 'ID',
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new TextField(
                'FAVORITES',
                [
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
            new IntegerField(
                'USER_ID',
                []
            ),
            new ReferenceField(
                'USER',
                UserTable::class,
                ['this.USER_ID' => 'ref.ID'],
                ['join_type' => 'LEFT']
            ),
        ];
    }
}
