<?php

namespace Journal;

use Bitrix\Iblock\ElementTable;
use Its\Library\Iblock\Iblock;

class IblockEventHandler
{
    protected static bool $handlerDisallow = false;

    public static function onAfterIBlockElementAddHandler(&$params): bool
    {
        if (self::$handlerDisallow) {
            return true;
        }

        if (intval($params['ID']) > 0) {
            self::afterElement($params);
        }
        return true;
    }

    public static function onAfterIBlockElementUpdateHandler(&$params): bool
    {
        if (self::$handlerDisallow) {
            return true;
        }

        if ($params['RESULT']) {
            self::afterElement($params);
        }
        return true;
    }

    protected static function afterElement(&$params)
    {
        $iblocks = [
//            Iblock::getInstance()->get('offers', 's1'),
            Iblock::getInstance()->get('catalog', 's1'),
        ];

        if (!in_array(intval($params['IBLOCK_ID']), $iblocks)) {
            return;
        }

        $elementData = ElementTable::getList([
            'filter' => [
                '=ID' => intval($params['ID']),
                '=IBLOCK_ID' => intval($params['IBLOCK_ID']),
            ],
            'select' => ['ID', 'IBLOCK_ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE']
        ])->fetch();

        if (!$elementData) {
            return;
        }

        if (!intval($elementData['PREVIEW_PICTURE']) && !intval($elementData['DETAIL_PICTURE'])) {
            self::$handlerDisallow = true;
            $element = new \CIBlockElement();
            $element->update(intval($elementData['ID']), ['ACTIVE' => 'N']);
            self::$handlerDisallow = false;
        }
    }
}
