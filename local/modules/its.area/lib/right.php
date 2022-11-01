<?php

namespace Its\Area;

use Bitrix\Main\Localization\Loc;

class Right
{
    protected array $arRights = [];
    protected array $arOperations = [];

    public function __construct()
    {
        $this->addRight(
            'D',
            'ITS_AREA_RIGHT_DENY',
            Loc::getMessage('ITS_AREA_RIGHT_DENY')
        );
        $this->addRight(
            'W',
            'ITS_AREA_RIGHT_ADMIN',
            Loc::getMessage('ITS_AREA_RIGHT_ADMIN')
        );
    }

    public static function canOperation(string $code): bool
    {
        global $USER;
        return $USER->CanDoOperation($code);
    }

    public static function getUserRight(): string
    {
        global $APPLICATION;
        $arModule = include __DIR__ . '/../module.php';
        return strval($APPLICATION->GetGroupRight(strval($arModule['MODULE_ID'])));
    }

    public function getModuleRights(): array
    {
        $referenceId = [];
        $reference = [];
        foreach ($this->arRights as $letter => $arRight) {
            $referenceId[] = $letter;
            $reference[] = "[$letter] {$arRight['NAME']}";
        }
        return [
            'reference_id' => $referenceId,
            'reference' => $reference,
        ];
    }

    public function getModuleTasks(): array
    {
        $tasks = [];
        foreach ($this->arRights as $letter => $arRight) {
            $operations = [];
            foreach ($this->arOperations as $code => $arOperation) {
                if (in_array($letter, $arOperation['RIGHT'])) {
                    $operations[] = $code;
                }
            }
            $tasks[$arRight['CODE']] = [
                'LETTER' => $letter,
                'OPERATIONS' => $operations
            ];
        }
        return $tasks;
    }

    public function getModuleOperationsList(): array
    {
        $operations = [];
        foreach ($this->arOperations as $code => $arOperation) {
            $operations[mb_strtoupper($code)] = [
                'title' => $arOperation['NAME'],
                'description' => $arOperation['DESCRIPTION'],
            ];
        }
        return $operations;
    }

    public function getModuleTasksList(): array
    {
        $tasks = [];
        foreach ($this->arRights as $letter => $arRight) {
            $tasks[mb_strtoupper($arRight['CODE'])] = [
                'title' => $arRight['NAME'],
                'description' => $arRight['DESCRIPTION'],
            ];
        }
        return $tasks;
    }

    protected function addRight(string $letter, string $code, string $name, string $description = ''): void
    {
        $this->arRights[$letter] = [
            'CODE' => $code,
            'NAME' => $name,
            'DESCRIPTION' => $description,
        ];
    }

    protected function addOperation(string $code, string $name, array $rights = [], string $description = ''): void
    {
        $this->arOperations[$code] = [
            'NAME' => $name,
            'DESCRIPTION' => $description,
            'RIGHT' => $rights,
        ];
    }
}
