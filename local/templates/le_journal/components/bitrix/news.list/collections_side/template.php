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

$this->setFrameMode(false);

if (empty($arResult['ITEMS'])) {
    return;
}

?>
<ul class="aside-tabs__list">
    <?php
    foreach ($arResult['ITEMS'] as $arItem) {
        ?>
        <li class="aside-tabs__item">
            <a href="<?=$arItem['DETAIL_PAGE_URL']?>">
                <span><?=$arItem['NAME']?></span>
            </a>
        </li>
        <?php
    }
    ?>
</ul>
