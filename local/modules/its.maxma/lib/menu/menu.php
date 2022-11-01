<?php

namespace Its\Maxma\Menu;

use Bitrix\Main\Localization\Loc;
use AdminConstructor\Helper\Url;

Loc::loadMessages(__FILE__);

class Menu
{
    public static function getModuleMenu(&$global, &$module): void
    {
        $menu = (new Item())
            ->setSort(100)
            ->setName(Loc::getMessage('ITS_MAXMA_MENU_MAIN'))
            ->setIcon('its_maxma_icon_main')
            ->setParentId('global_menu_store');

        $menu->addItem(
            (new Item())
                ->setSort(10)
                ->setName(Loc::getMessage('ITS_MAXMA_MENU_ORDERS'))
                ->setLink(Url::make('its-maxma-orders.php'))
        );

        $menu->addItem(
            (new Item())
                ->setSort(20)
                ->setName(Loc::getMessage('ITS_MAXMA_MENU_RETURNS'))
                ->setLink(Url::make('its-maxma-returns.php'))
                ->setMoreLinks(['its-maxma-return-add.php'])
        );

        $menu->addItem(
            (new Item())
                ->setSort(30)
                ->setName(Loc::getMessage('ITS_MAXMA_MENU_CERTIFICATES'))
                ->setLink(Url::make('its-maxma-certificates.php'))
        );

        $menu->addItem(
            (new Item())
                ->setSort(30)
                ->setName(Loc::getMessage('ITS_MAXMA_MENU_HISTORY'))
                ->setLink(Url::make('its-maxma-history.php'))
        );

        $module = array_merge($module, [$menu->combine()]);
    }
}
