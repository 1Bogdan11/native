<?php

use Bitrix\Main\Localization\Loc;

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

$this->SetViewTarget('SUPPORT_MOBILE_NAV');
?>
<div class="support__m-menu-list">
    <?php
    $isSelected = (
        empty($arParams['SELECTED_ELEMENT_ID'])
        && empty($arParams['SELECTED_ELEMENT_CODE'])
    );
    ?>
    <a class="support__m-menu-option <?=($isSelected ? 'is-active' : '')?>"
       href="<?=SITE_DIR . 'support/'?>">
        <?=Loc::getMessage('SUPPORT_MENU_ROOT')?>
    </a>
    <?php
    foreach ($arResult['ITEMS'] as $arItem) {
        $isSelected = (
            $arParams['SELECTED_ELEMENT_ID'] == $arItem['ID']
            || $arParams['SELECTED_ELEMENT_CODE'] == $arItem['CODE']
        );
        ?>
        <a class="support__m-menu-option <?=($isSelected ? 'is-active' : '')?>"
           href="<?=$arItem['DETAIL_PAGE_URL']?>">
            <?=$arItem['NAME']?>
        </a>
        <?php
    }
    ?>
</div>
<?php
$this->EndViewTarget();

?>
<nav class="support__nav">
    <?php
    foreach ($arResult['ITEMS'] as $arItem) {
        $isSelected = (
            $arParams['SELECTED_ELEMENT_ID'] == $arItem['ID']
            || $arParams['SELECTED_ELEMENT_CODE'] == $arItem['CODE']
        );
        ?>
        <a class="support__nav-link"
           href="<?=$arItem['DETAIL_PAGE_URL']?>"
            <?=($isSelected ? 'data-active' : '')?>>
            <?=$arItem['NAME']?>
        </a>
        <?php
    }
    ?>
</nav>
