<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

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

$nextConditional = $arResult['NAV_PAGE_CURRENT'] < $arResult['NAV_PAGE_COUNT'];
$nextUrl = $arResult['NAV_QUERY_TO'] . ($arResult['NAV_PAGE_CURRENT'] + 1);

?>
<div class="jsPageNavigationWrap">
    <div class="diary-section__footer">
        <?php
        if ($nextConditional) {
            ?>
            <a class="btn btn--lines jsPageNavigationShowMoreButton" href="<?=$nextUrl?>">
                <span class="btn__bg"></span>
                <span class="btn__text"><?=Loc::getMessage('PAGE_NAVIGATION_TEMPLATE_SHOW_MORE')?></span>
            </a>
            <?php
        }
        ?>
        <ul class="pagination pagination--catalog">
            <?php
            if ($arResult['NAV_PAGE_START'] >= 2) {
                echo "<li class='pagination__item'><a class='btn btn--lines' href='{$arResult['NAV_QUERY_TO']}1'><span class='btn__bg'></span><span class='btn__text'>1</span></a></li>";

                if ($arResult['NAV_PAGE_START'] > 2) {
                    echo "<li class='pagination__item pagination__item--separator'><a class='btn btn--lines' href='javascript:void(0)'><span class='btn__bg'></span><span class='btn__text'>...</span></a></li>";
                }
            }

            for ($i = $arResult['NAV_PAGE_START']; $i <= $arResult['NAV_PAGE_END']; $i++) {
                if ($i === $arResult['NAV_PAGE_CURRENT']) {
                    echo "<li class='pagination__item is-active'><a class='btn btn--lines' href='javascript:void(0)'><span class='btn__bg'></span><span class='btn__text'>$i</span></a></li>";
                    continue;
                }

                echo "<li class='pagination__item'><a class='btn btn--lines' href='{$arResult['NAV_QUERY_TO']}$i'><span class='btn__bg'></span><span class='btn__text'>$i</span></a></li>";
            }

            if ($arResult['NAV_PAGE_END'] <= ($arResult['NAV_PAGE_COUNT'] - 1)) {
                if ($arResult['NAV_PAGE_END'] < ($arResult['NAV_PAGE_COUNT'] - 1)) {
                    echo "<li class='pagination__item pagination__item--separator'><a class='btn btn--lines' href='javascript:void(0)'><span class='btn__bg'></span><span class='btn__text'>...</span></a></li>";
                }

                echo "<li class='pagination__item'><a class='btn btn--lines' href='{$arResult['NAV_QUERY_TO']}{$arResult['NAV_PAGE_COUNT']}'><span class='btn__bg'></span><span class='btn__text'>{$arResult['NAV_PAGE_COUNT']}</span></a></li>";
            }
            ?>
        </ul>
    </div>
</div>
