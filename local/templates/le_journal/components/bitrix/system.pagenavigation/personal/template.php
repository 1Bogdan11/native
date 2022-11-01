<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

/*
 * NAV_ID - NavNum
 * IS_DESC_NUMBERING - bDescPageNumbering
 * NAV_PAGE_SIZE - NavPageSize
 * NAV_PAGE_START - nStartPage
 * NAV_PAGE_END - nEndPage
 * NAV_PAGE_CURRENT - NavPageNomer
 * NAV_PAGE_COUNT - NavPageCount
 * NAV_PATH - sUrlPath / NavQueryString
 * NAV_QUERY_TO - sUrlPath / NavQueryString / PAGEN_ / NavNum
 */

$this->setFrameMode(true);

if ($arResult['NAV_PAGE_COUNT'] < 2) {
    return;
}

?>
<ul class="pagination">
    <?php
    if ($arResult['NAV_PAGE_START'] >= 2) {
        echo "<li class='pagination__item'><a href='{$arResult['NAV_QUERY_TO']}1'>1</a></li>";

        if ($arResult['NAV_PAGE_START'] > 2) {
            echo "<li class='pagination__ite pagination__item--separator'><span>...</span></li>";
        }
    }

    for ($i = $arResult['NAV_PAGE_START']; $i <= $arResult['NAV_PAGE_END']; $i++) {
        if ($i === $arResult['NAV_PAGE_CURRENT']) {
            echo "<li class='pagination__item is-active'><span>$i</span></li>";
            continue;
        }

        echo "<li class='pagination__item'><a href='{$arResult['NAV_QUERY_TO']}$i'>$i</a></li>";
    }

    if ($arResult['NAV_PAGE_END'] <= ($arResult['NAV_PAGE_COUNT'] - 1)) {
        if ($arResult['NAV_PAGE_END'] < ($arResult['NAV_PAGE_COUNT'] - 1)) {
            echo "<li class='pagination__ite pagination__item--separator'><span>...</span></li>";
        }

        echo "<li class='pagination__item'><a href='{$arResult['NAV_QUERY_TO']}{$arResult['NAV_PAGE_COUNT']}'>{$arResult['NAV_PAGE_COUNT']}</a></li>";
    }
    ?>
</ul>
