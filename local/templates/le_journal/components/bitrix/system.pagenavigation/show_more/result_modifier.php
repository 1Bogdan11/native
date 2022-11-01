<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */

$nPageWindow = 3;
if ($arResult['NavPageNomer'] > floor($nPageWindow / 2) + 1 && $arResult['NavPageCount'] > $nPageWindow) {
    $nStartPage = $arResult['NavPageNomer'] - floor($nPageWindow / 2);
} else {
    $nStartPage = 1;
}

if ($arResult['NavPageNomer'] <= $arResult['NavPageCount'] - floor($nPageWindow / 2) && $nStartPage + $nPageWindow - 1 <= $arResult['NavPageCount']) {
    $nEndPage = $nStartPage + $nPageWindow - 1;
} else {
    $nEndPage = $arResult['NavPageCount'];
    if ($nEndPage - $nPageWindow + 1 >= 1) {
        $nStartPage = $nEndPage - $nPageWindow + 1;
    }
}
$arResult['nStartPage'] = $arResult['nStartPage'] = $nStartPage;
$arResult['nEndPage'] = $arResult['nEndPage'] = $nEndPage;

$arResult['NAV_ID'] = intval($arResult['NavNum']);
$arResult['IS_DESC_NUMBERING'] = $arResult["bDescPageNumbering"] === true;
$arResult['NAV_PAGE_SIZE'] = intval($arResult['NavPageSize']);
$arResult['NAV_PAGE_START'] = intval($arResult['nStartPage']);
$arResult['NAV_PAGE_END'] = intval($arResult['nEndPage']);
$arResult['NAV_PAGE_CURRENT'] = intval($arResult['NavPageNomer']);
$arResult['NAV_PAGE_COUNT'] = intval($arResult['NavPageCount']);
$arResult['NAV_PATH'] = $arResult['sUrlPath'] . (!empty($arResult['NavQueryString']) ? '?' . $arResult['NavQueryString'] : null);
$arResult['NAV_QUERY_TO'] = $arResult['sUrlPath'] . '?'
    . (!empty($arResult['NavQueryString']) ? $arResult['NavQueryString'] . '&' : null)
    . 'PAGEN_' . $arResult['NavNum'] . '=';
