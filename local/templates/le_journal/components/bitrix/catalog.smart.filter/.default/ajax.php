<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */
/** @global CMain $APPLICATION */

while (ob_get_level()) {
    ob_end_clean();
}
unset($arResult["COMBO"]);
echo json_encode($arResult);
die();
