<?php

namespace Its\Area\Page;

use AdminConstructor\Edit\ActiveInput;
use AdminConstructor\Edit\EditorInput;
use AdminConstructor\Edit\FileInput;
use AdminConstructor\Edit\IntegerInput;
use AdminConstructor\Edit\ReadInput;
use AdminConstructor\Edit\StringInput;
use AdminConstructor\Edit\TextInput;
use AdminConstructor\Helper\Url;
use AdminConstructor\Lang;
use AdminConstructor\Page\Edit;
use AdminConstructor\Structure\UploaderParams;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Its\Area\Right;
use AdminConstructor\Edit\ColorInput;

Loc::loadMessages(__FILE__);

class FileEdit extends Edit
{
    public const PAGE_EDIT = 'its-area-file-edit.php';
    public const PAGE_LIST = 'its-area-file-list.php';

    protected array $arArea = [];

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

    protected function setRequestId(): string
    {
        return 'file';
    }

    protected function setRequestCopy(): string
    {
        return '';
    }

    protected function enableButtons(): bool
    {
        return true;
    }

    protected function customButtons(): void
    {
    }

    protected function checkId(): bool
    {
        return true;
    }

    protected function checkCopyId(): bool
    {
        return false;
    }

    protected function getDefaultValues(string $id): array
    {
        return [];
    }

    protected function getEditValues(string $id): array
    {
        $this->arArea = [];

        $resTemplate = \CSiteTemplate::GetList(
            ['sort' => 'asc', 'name' => 'asc'],
            ['ID' => $this->getRequest()->get('template')],
            ['ID', 'NAME', 'PATH']
        );

        $arTemplate = $resTemplate->GetNext();
        if (!$arTemplate) {
            $this->addError(
                Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_TEMPLATE_NOT_FOUND'),
                true
            );
            return [];
        }

        $settingsPath = Application::getDocumentRoot() . "{$arTemplate['PATH']}/.area.settings.php";

        ob_start();
        $arSettings = include $settingsPath;
        ob_end_clean();

        if (!file_exists($settingsPath) || empty($arSettings['directory'])) {
            $this->addError(
                Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_TEMPLATE_NOT_SUPPORTED'),
                true
            );
            return [];
        }

        $arGroups = [];

        if (is_array($arSettings['groups'])) {
            foreach ($arSettings['groups'] as $groupId => $arGroup) {
                $arGroups[$groupId] = $arGroup;
            }
        }

        if (is_array($arSettings['files'])) {
            foreach ($arSettings['files'] as $arFile) {
                if ($arFile['path'] === $this->getId()) {
                    $this->arArea = [
                        'NAME' => $arFile['name'],
                        'PATH' => $arFile['path'],
                        'SORT' => intval($arFile['sort']),
                        'TYPE' => mb_strtoupper($arFile['type']),
                        'LINES' => $arFile['lines'],
                        'GROUP' => '',
                    ];
                    if (!empty($arGroups[$arFile['group']])) {
                        $this->arArea['GROUP'] = $arGroups[$arFile['group']]['name'];
                    }

                    $this->arArea['FULL_PATH'] = Application::getDocumentRoot()
                        . "{$arTemplate['PATH']}/{$arSettings['directory']}/{$arFile['path']}";

                    $this->arArea['EXIST'] = file_exists($this->arArea['FULL_PATH']) ? 'Y' : 'N';
                    if ($this->arArea['EXIST'] === 'Y') {
                        $this->arArea['RAW'] = file_get_contents($this->arArea['FULL_PATH']);
                        if (is_array($this->arArea['LINES']) && !empty($this->arArea['LINES'])) {
                            $arLines = explode(PHP_EOL, $this->arArea['RAW']);
                            foreach (array_values($this->arArea['LINES']) as $i => $param) {
                                $this->arArea["LINE_{$i}"] = htmlspecialchars_decode($arLines[$i]);
                            }
                        } elseif ($this->arArea['TYPE'] === 'STRING') {
                            $this->arArea['RAW'] = htmlspecialchars_decode(preg_replace('#[\n\r]#', '', $this->arArea['RAW']));
                        } elseif ($this->arArea['TYPE'] === 'TEXT') {
                            $this->arArea['RAW'] = htmlspecialchars_decode($this->arArea['RAW']);
                        } elseif ($this->arArea['TYPE'] === 'IMAGE') {
                            $this->arArea['RAW'] = [['id' => intval($this->arArea['RAW'])]];
                        }
                    }

                    $default = strval($arFile['default']);
                    if (!empty($default)) {
                        $this->addNotice($default, true);
                    }
                }
            }
        }

        if (!$this->arArea) {
            $this->addError(
                Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_FILE_NOT_FOUND'),
                true
            );
            return [];
        }

        return $this->arArea;
    }

    protected function getCopyValues(string $id): array
    {
        return [];
    }

    protected function prepareEditParams(): void
    {
        $this->setTitle(
            Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_TITLE', ['#NAME#' => $this->arArea['NAME']])
        );

        $this->addContextMenuButton(
            Lang::get('ACTION_BACK'),
            Url::make(self::PAGE_LIST, [
                'template' => $this->getRequest()->get('template'),
            ]),
            static::ICON_LIST
        );

        $this->addTab('main', Lang::get('EDIT_MAIN_TAB'), [&$this, 'printMainTab']);

        if ($this->getRequest()->get('message') === 'OK') {
            $this->addMessage(Lang::get('EDIT_SAVED'));
        }

        $this->addInput('NAME', new ReadInput(
            null,
            'NAME',
            Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_FIELD_NAME')
        ));
        $this->addInput('PATH', new ReadInput(
            null,
            'PATH',
            Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_FIELD_PATH')
        ));
        $this->addInput('SORT', new ReadInput(
            null,
            'SORT',
            Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_FIELD_SORT')
        ));
        $this->addInput('GROUP', new ReadInput(
            null,
            'GROUP',
            Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_FIELD_GROUP'),
            function ($value) {
                return "<span class='adm-submenu-item-link-icon fileman_icon_folder'></span>&nbsp;{$value}";
            }
        ));

        if (is_array($this->arArea['LINES']) && !empty($this->arArea['LINES'])) {
            foreach (array_values($this->arArea['LINES']) as $i => $paramName) {
                $this->addInput("LINE_{$i}", new StringInput(null, "LINE_{$i}", strval($paramName)));
            }
        } else {
            switch ($this->arArea['TYPE']) {
                case 'STRING':
                    $this->addInput('RAW', new StringInput(
                        null,
                        'RAW',
                        Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_FIELD_CUSTOM_CONTENT')
                    ));
                    break;

                case 'TEXT':
                    $this->addInput('RAW', new TextInput(
                        null,
                        'RAW',
                        Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_FIELD_CUSTOM_CONTENT')
                    ));
                    break;

                case 'HTML':
                    $this->addInput('RAW', new EditorInput(
                        null,
                        'RAW',
                        Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_FIELD_CUSTOM_CONTENT')
                    ));
                    break;

                case 'IMAGE':
                    $params = (new UploaderParams())
                        ->setCount(1)
                        ->setDirectory('its_area_files')
                        ->setImage(true)
                        ->setOptimize(true, 3000, 3000);
                    $this->addInput('RAW', new FileInput(
                        $params,
                        null,
                        'RAW',
                        Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_FIELD_CUSTOM_CONTENT')
                    ));
                    break;

                case 'COLOR':
                    $this->addInput('RAW', new ColorInput(
                        null,
                        'RAW',
                        Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_FIELD_CUSTOM_CONTENT')
                    ));
                    break;

                default:
                    $this->addInput('RAW', new ReadInput(
                        null,
                        'RAW',
                        Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_FIELD_CUSTOM_CONTENT')
                    ));
                    break;
            }
        }

        $this->setButtons(
            Url::make(static::PAGE_LIST, [
                'template' => $this->getRequest()->get('template'),
            ]),
            true,
            true,
            true,
            false
        );
    }

    protected function printMainTab(): void
    {
        $this->printHiddenParam('file', $this->getRequest()->get('file'));
        $this->printHiddenParam('template', $this->getRequest()->get('template'));

        $this->printRow('NAME');
        $this->printRow('GROUP');
        $this->printRow('SORT');
        $this->printRow('PATH');

        $this->printHead(Loc::getMessage('ITS_AREA_PAGE_FILE_EDIT_FIELD_CUSTOM'));

        if (is_array($this->arArea['LINES']) && !empty($this->arArea['LINES'])) {
            foreach (array_values($this->arArea['LINES']) as $i => $paramName) {
                $this->printRow("LINE_{$i}");
            }
        } elseif ($this->arArea['TYPE'] === 'HTML') {
            $this->printRow('RAW', true, false);
        } else {
            $this->printRow('RAW');
        }
    }

    protected function executeContextActions(): void
    {
    }

    protected function executeActions(array $values): void
    {
        if (is_array($this->arArea['LINES']) && !empty($this->arArea['LINES'])) {
            $arLines = [];
            foreach (array_values($this->arArea['LINES']) as $i => $param) {
                $arLines[] = htmlspecialchars($values["LINE_{$i}"]);
            }
            $content = implode(PHP_EOL, $arLines);
        } elseif ($this->arArea['TYPE'] === 'HTML') {
            $content = $values['RAW'];
        } elseif ($this->arArea['TYPE'] === 'IMAGE') {
            if (!is_array($values['RAW'])) {
                $content = 0;
            } else {
                $content = intval(reset($values['RAW'])['id']);
            }
        } else {
            $content = htmlspecialchars($values['RAW']);
        }

        $directory = dirname($this->arArea['FULL_PATH']);
        if (!file_exists($directory)) {
            mkdir($directory, 0644);
        }
        file_put_contents($this->arArea['FULL_PATH'], strval($content));

        $this->completeActions(true);

        if ($this->getRequest()->get('save')) {
            LocalRedirect(Url::make(static::PAGE_LIST, [
                'template' => $this->getRequest()->get('template'),
            ]));
            return;
        }

        if ($this->getRequest()->get('apply')) {
            LocalRedirect(Url::make(
                static::PAGE_EDIT,
                [
                    'file' => $this->getRequest()->get('file'),
                    'template' => $this->getRequest()->get('template'),
                    'message' => 'OK',
                    'tabControl_active_tab' => $this->getRequest()->get('tabControl_active_tab'),
                ]
            ));
            return;
        }
    }
}
