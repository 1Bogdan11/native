<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */

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
