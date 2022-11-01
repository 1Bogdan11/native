<?php

namespace Journal;

use Bitrix\Main\Entity\Event;
use Its\Library\Iblock\Iblock;

class DiscountEvent
{
    public static function afterAdd(Event $event)
    {
        static::clearCatalogCache();
    }

    public static function afterUpdate(Event $event)
    {
        static::clearCatalogCache();
    }

    protected static function clearCatalogCache(): void
    {
        global $CACHE_MANAGER;
        foreach (Iblock::getInstance()->getAll('catalog') as $iblockId) {
            $CACHE_MANAGER->ClearByTag("iblock_id_{$iblockId}");
        }
    }
}
