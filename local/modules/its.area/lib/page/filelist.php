<?php

namespace Its\Area\Page;

use AdminConstructor\Lang;
use AdminConstructor\Page\Table;
use AdminConstructor\Structure\ReferenceItem;
use AdminConstructor\Structure\ReferenceSearch;
use AdminConstructor\System\AdminList;
use AdminConstructor\System\AdminListRow;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Its\Area\Right;

Loc::loadMessages(__FILE__);

class FileList extends Table
{
    public const PAGE_EDIT = 'its-area-file-edit.php';
    public const PAGE_LIST = 'its-area-file-list.php';

    protected function setReadRight(): bool
    {
        global $USER;
        return Right::getUserRight() > 'D' || $USER->IsAdmin();
    }

    protected function setWriteRight(): bool
    {
        global $USER;
        return Right::getUserRight() > 'D' || $USER->IsAdmin();
    }

    protected function setTableId(): string
    {
        return 'its_area_file_table';
    }

    protected function setColumnRowId(): string
    {
        return 'PATH';
    }

    protected function getReferenceItem(string $id = null): ReferenceItem
    {
        return new ReferenceItem();
    }

    protected function getReferenceSearch(string $search = null): ReferenceSearch
    {
        $return = new ReferenceSearch($search);
        return $return;
    }

    protected function prepareParams(): void
    {
        $this->setTitle(Loc::getMessage('ITS_AREA_PAGE_FILE_LIST_TITLE'));
        $this->setTotalRow(false);
        $this->setDataType(static::PARAM_OLD);

        $this->addHeader('NAME', Loc::getMessage('ITS_AREA_PAGE_FILE_LIST_FIELD_NAME'), false, true);
        $this->addHeader('GROUP', Loc::getMessage('ITS_AREA_PAGE_FILE_LIST_FIELD_GROUP'), false, true);
        $this->addHeader('SORT', Loc::getMessage('ITS_AREA_PAGE_FILE_LIST_FIELD_SORT'), false, true);
        $this->addHeader('PATH', Loc::getMessage('ITS_AREA_PAGE_FILE_LIST_FIELD_PATH'), false, true);
    }

    protected function getResult(array $parameters): \CAllDBResult
    {
        $resTemplate = \CSiteTemplate::GetList(
            ['sort' => 'asc', 'name' => 'asc'],
            ['ID' => $this->getRequest()->get('template')],
            ['ID', 'NAME', 'PATH']
        );

        $arTemplate = $resTemplate->GetNext();
        if (!$arTemplate) {
            $this->addError(
                Loc::getMessage('ITS_AREA_PAGE_FILE_LIST_TEMPLATE_NOT_FOUND'),
                true
            );
            return new \CAdminResult([], $this->setTableId());
        }

        $settingsPath = Application::getDocumentRoot() . "{$arTemplate['PATH']}/.area.settings.php";
        if (!file_exists($settingsPath)) {
            $this->addError(
                Loc::getMessage('ITS_AREA_PAGE_FILE_LIST_TEMPLATE_NOT_SUPPORTED'),
                true
            );
            return new \CAdminResult([], $this->setTableId());
        }

        $arGroups = [];
        $arResult = [];

        ob_start();
        $arSettings = include $settingsPath;
        ob_end_clean();

        if (is_array($arSettings['groups'])) {
            foreach ($arSettings['groups'] as $groupId => $arGroup) {
                $arGroups[$groupId] = $arGroup;
            }
        }

        if (is_array($arSettings['files'])) {
            foreach ($arSettings['files'] as $arFile) {
                $arRow = [
                    'NAME' => $arFile['name'],
                    'PATH' => $arFile['path'],
                    'SORT' => intval($arFile['sort']),
                    'GROUP' => '',
                    'REAL_SORT' => intval($arFile['sort']),
                ];
                if (!empty($arGroups[$arFile['group']])) {
                    $arRow['GROUP'] = $arGroups[$arFile['group']]['name'];
                    $arRow['REAL_SORT'] = $arRow['REAL_SORT'] + (intval($arGroups[$arFile['group']]['sort']) * 1000);
                }
                $arResult[] = $arRow;
            }
        }
        uasort($arResult, function ($a, $b) {
            return $a['REAL_SORT'] <=> $b['REAL_SORT'];
        });

        return new \CAdminResult($arResult, $this->setTableId());
    }

    protected function modifyRow(AdminListRow &$row, array $rowData, AdminList &$list): void
    {
        if (!empty($rowData['GROUP'])) {
            $row->AddViewField('GROUP', "
                <table cellpadding='0' cellspacing='0' border='0'>
                    <tr>
                        <td align='left'><span class='adm-submenu-item-link-icon fileman_icon_folder'></span></td>
                        <td align='left'>&nbsp;{$rowData['GROUP']}</td>
                    </tr>
                
                </table>
            ");
        }
    }

    protected function setRowActions(array &$actions, array $rowData, AdminList &$list): void
    {
        $actions[] = [
            'DEFAULT' => true,
            'ICON' => static::ICON_LIST_EDIT,
            'TEXT' => Lang::get('ACTION_EDIT'),
            'ACTION' => $this->jsRedirectAction(static::PAGE_EDIT, [
                'template' => $this->getRequest()->get('template'),
                'file' => $rowData['PATH'],
            ]),
        ];
    }

    protected function executeActions(AdminList &$list, array $parameters): void
    {
    }

    protected function executeTotalRow(AdminListRow &$row, array $headers, array $parameters, AdminList &$list): void
    {
    }
}
