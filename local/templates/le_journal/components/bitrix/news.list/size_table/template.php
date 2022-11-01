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

$this->setFrameMode(true);

if (empty($arResult['ITEMS'])) {
    return;
}

$this->SetViewTarget('ELEMENT_SIZE_TABLE_BUTTON');
?>
<button class="aside-tabs__link" data-modal-tab="sizes" data-modal-state-remove="is-fullscreen">
    <?=Loc::getMessage('ELEMENT_SIZE_TABLE_HEAD')?>
</button>
<?php
$this->EndViewTarget();

?>
<div class="aside-tabs__content js-booster-mobile-x" data-modal-content="sizes">
    <div class="product-modal__main js-tabs js-booster__inner">
        <div class="product-modal__body">
            <div class="product-modal__tab_content is-active js-tabs__content">
                <div class="product-modal__title">
                    <?=Loc::getMessage('ELEMENT_SIZE_MODAL_BRALETTES')?>
                </div>
                <div class="product-modal__content">
                    <?php
                    foreach ($arResult['ITEMS'] as $arItem) {
                        echo $arItem['~DETAIL_TEXT'];
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
