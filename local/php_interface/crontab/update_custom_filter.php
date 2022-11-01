<?php

use Its\Library\Iblock\Iblock;
use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__, 3);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

Loader::includeModule('iblock');

$resource = ElementTable::getList([
    'filter' => ['=IBLOCK_ID' => Iblock::getInstance()->get('catalog', 's1')],
    'select' => ['ID', 'ACTIVE'],
]);
$element = new \CIBlockElement();
while ($elementData = $resource->fetch()) {
    $update = $element->Update($elementData['ID'], ['ACTIVE' => $elementData['ACTIVE']]);
    if (!$update) {
        echo $element->LAST_ERROR . PHP_EOL;
    }
}
