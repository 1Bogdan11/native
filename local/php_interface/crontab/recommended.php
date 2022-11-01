<?php

use Journal\Recommended;
use Its\Library\Iblock\Iblock;

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__DIR__, 3));
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$calculator = new Recommended(
    Iblock::getInstance()->get('catalog', 's1'),
    'RECOMMENDED',
    4
);
$calculator->setOrder([
    'SORT' => 'ASC',
    'ID' => 'DESC',
]);
$calculator->recalculate();
