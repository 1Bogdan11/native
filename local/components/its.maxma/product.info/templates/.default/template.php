<?php

use Its\Library\Tool\Declension;
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
$declension = new Declension(
    Loc::getMessage('ITS_MAXMA_PRODUCT_INFO_BONUS_ONE'),
    Loc::getMessage('ITS_MAXMA_PRODUCT_INFO_BONUS_TWO'),
    Loc::getMessage('ITS_MAXMA_PRODUCT_INFO_BONUS_MANY')
);

if ($arResult['HAVE_OFFERS'] !== 'Y') {
    if (!$arResult['ROWS'][$arResult['PRODUCT_ID']]['BONUS']['COLLECT']) {
        return;
    }

    ?>
    <div class="product-bonuses">
        <div class="product-bonuses__part">
            <?=Loc::getMessage(
                'ITS_MAXMA_PRODUCT_INFO_LOYALTY_BONUS',
                [
                    '#COUNT#' => floatval($arResult['ROWS'][$arResult['PRODUCT_ID']]['BONUS']['COLLECT']),
                    '#DESC#' => $declension->getMessage(floatval($arResult['ROWS'][$arResult['PRODUCT_ID']]['BONUS']['COLLECT'])),
                ]
            )?>
        </div>
        <a href="<?=SITE_DIR?>support/loyalty-program/" class="product__button link__underline">
            <?=Loc::getMessage('ITS_MAXMA_PRODUCT_INFO_LOYALTY_LINK')?>
        </a>
    </div>
    <?php
} else {
    $jsData = [];
    foreach ($arResult['ROWS'] as $productId => $arRow) {
        if ($arRow['BONUS']['COLLECT'] <= 0) {
            continue;
        }
        $jsData[$productId] = Loc::getMessage(
            'ITS_MAXMA_PRODUCT_INFO_LOYALTY_BONUS',
            [
                '#COUNT#' => floatval($arRow['BONUS']['COLLECT']),
                '#DESC#' => $declension->getMessage(floatval($arRow['BONUS']['COLLECT'])),
            ]
        );
    }
    ?>
    <div class="product-bonuses">
        <div class="product-bonuses__part" id="product_bonuses"></div>
        <a href="<?=SITE_DIR?>support/loyalty-program/" class="product__button link__underline">
            <?=Loc::getMessage('ITS_MAXMA_PRODUCT_INFO_LOYALTY_LINK')?>
        </a>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
           new ItsMaxmaProductInfo(
               'product_bonuses',
               <?=json_encode($jsData)?>,
               'custom_element_change_offer',
               function (event) {
                    return event.detail.id;
               }
           );
        });
    </script>
    <?php
}
