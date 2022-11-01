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

if (!empty($arResult)) {
    ?>
    <ul class="aside-tabs__list">
        <?php
        foreach ($arResult as $arItem) {
            if ($arParams['MAX_LEVEL'] == 1 && $arItem['DEPTH_LEVEL'] > 1) {
                continue;
            }
            $select = ($arItem['SELECTED'] ? 'selected' : '');
            $product = ($arItem['PARAMS']['UF_PRODINMENU'] ? 'data-modal-level-open ="product-' . $arItem['PARAMS']['UF_PRODINMENU'] . '"' : '');
            ?>
            <li class="aside-tabs__item <?=$select?>">
                <?php
                if ($arItem['PARAMS']['COUNT']) {
                    ?>
                    <div class="aside-tabs__item_count">
                        <?=$arItem['PARAMS']['COUNT']?>
                    </div>
                    <?php
                }
                ?>
                <a href="<?=$arItem['LINK']?>" <?=$product?>>
                    <span><?=$arItem['TEXT']?></span>
                </a>
            </li>
            <?php
        }
        ?>
    </ul>
    <?php
}
