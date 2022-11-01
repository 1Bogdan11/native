<?php

use Journal\DiscountPrice;
use Its\Library\Iblock\Iblock;

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__, 3);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$discountPrice = new DiscountPrice(
    Iblock::getInstance()->get('offers', 's1'),
    'FILTER_DISCOUNT_PRICE'
);
$discountPrice->calculate();
