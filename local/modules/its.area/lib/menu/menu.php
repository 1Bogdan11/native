<?php

namespace Its\Area\Menu;

use AdminConstructor\Helper\Url;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Its\Area\Right;

Loc::loadMessages(__FILE__);

class Menu
{
    public static function getModuleMenu(&$global, &$module): void
    {
        global $USER;

        $userRightLetter = Right::getUserRight();
        if ($userRightLetter <= 'D' && !$USER->IsAdmin()) {
            return;
        }

        $menu = (new Item())
            ->setSort(10)
            ->setName(Loc::getMessage('ITS_AREA_MENU_MAIN'))
            ->setIcon('its_area_icon_main')
            ->setParentId('global_menu_content');

        $resTemplates = \CSiteTemplate::GetList(
            ['sort' => 'asc', 'name' => 'asc'],
            ['TYPE' => ''],
            ['ID', 'NAME']
        );

        while ($arTemplate = $resTemplates->GetNext()) {
            $settingsPath = Application::getDocumentRoot() . "{$arTemplate['PATH']}/.area.settings.php";
            if (!file_exists($settingsPath)) {
                continue;
            }
            $item = new Item("template_{$arTemplate['ID']}");
            $item->setName("{$arTemplate['NAME']} [{$arTemplate['ID']}]");
            $item->setLink(Url::make('its-area-file-list.php', ['template' => $arTemplate['ID']]));
            $item->setMoreLinks([
                'its-area-file-list.php',
                'its-area-file-edit.php',
            ]);
            $item->setIcon('iblock_menu_icon_sections');
            $menu->addItem($item);

            $arGroups = [];

            ob_start();
            $arSettings = include $settingsPath;
            ob_end_clean();

            if (is_array($arSettings['groups'])) {
                uasort($arSettings['groups'], function ($a, $b) {
                    return $a['sort'] <=> $b['sort'];
                });
                foreach ($arSettings['groups'] as $groupId => $arGroup) {
                    $arGroups[$groupId] = new Item("template_{$arTemplate['ID']}_group_{$groupId}");
                    $arGroups[$groupId]->setName($arGroup['name']);
                    $arGroups[$groupId]->setSort($arGroup['sort']);
                    $arGroups[$groupId]->setIcon('iblock_menu_icon_sections');
                    $item->addItem($arGroups[$groupId]);
                }
            }

            if (is_array($arSettings['files'])) {
                foreach ($arSettings['files'] as $arFile) {
                    $file = new Item();
                    $file->setName($arFile['name']);
                    $file->setSort($arFile['sort']);
                    if (!empty($arGroups[$arFile['group']])) {
                        $arGroups[$arFile['group']]->addItem($file);
                    } else {
                        $item->addItem($file);
                    }
                    $url = Url::make(
                        'its-area-file-edit.php',
                        [
                            'template' => $arTemplate['ID'],
                            'file' => $arFile['path'],
                        ]
                    );
                    $file->setLink($url);
                }
            }
        }

        $module = array_merge($module, [$menu->combine()]);
    }
}
