<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Location\LocationTable;
use Bitrix\Main\Loader;
use Its\Maxma\Api\Tool as MaxmaTool;

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
/** @global array $arSteps */
/** @global string $currentStep */

if ($currentStep !== 'ready') {
    return;
}

?>
<div class="ordering__content">
    <div class="ordering-final">
        <?php
        $stepsCount = count($arSteps);
        $counter = 0;
        foreach ($arSteps as $stepCode => $arStep) {
            $counter++;
            if ($stepCode === $currentStep) {
                continue;
            }
            ?>
            <div class="ordering-final__part">
                <div class="ordering-final__head">
                    <div class="ordering-final__counter">
                        <?=Loc::getMessage('MODAL_ORDER_STEP_COUNTER', [
                            '#N#' => $counter,
                            '#CNT#' => $stepsCount - 1,
                        ])?>
                    </div>
                    <div class="ordering-final__head_part">
                        <div class="ordering__content_title"><?=$arStep['NAME']?></div>
                        <button class="ordering-final__back" data-order-go="<?=$stepCode?>">
                            <?=Loc::getMessage('MODAL_ORDER_EDIT')?>
                        </button>
                    </div>
                </div>
                <div class="ordering-final__content">
                    <?php
                    switch ($stepCode) {
                        case 'personal':
                            foreach ($arResult['ORDER_PROPS'][SECTION_PERSONAL] as $arProperty) {
                                if (empty($arProperty['VALUE']) && $arProperty['REQUIED_FORMATED'] !== 'Y') {
                                    continue;
                                }
                                ?>
                                <p><b><?=$arProperty['NAME']?>:</b> <?=$arProperty['VALUE']?></p>
                                <?php
                            }
                            break;

                        case 'delivery':
                            foreach ($arResult['DELIVERY'] as $arDelivery) {
                                if ($arDelivery['CHECKED'] !== 'Y') {
                                    continue;
                                }
                                ?>
                                <div class="ordering-final__content_part">
                                    <b>
                                        <?=$arDelivery['OWN_NAME']?>
                                        /
                                        <?php
                                        if ($arDelivery['PRICE'] <= 0) {
                                            echo Loc::getMessage('MODAL_ORDER_FREE');
                                        } else {
                                            echo $arDelivery['PRICE_FORMATED'];
                                        }
                                        ?>
                                    </b>
                                    <?php
                                    if (is_array($arDelivery['STORE']) && !empty($arDelivery['STORE'])) {
                                        foreach ($arDelivery['STORE'] as $storeId) {
                                            $storeId = intval($storeId);
                                            if ($storeId !== intval($arResult['BUYER_STORE'])) {
                                                continue;
                                            }
                                            $arStore = $arResult['STORE_LIST'][$storeId];
                                            ?>
                                            <p><?="{$arStore['TITLE']}. {$arStore['ADDRESS']}"?></p>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="ordering-final__content_part">
                                <?php
                                foreach ([$arResult['ORDER_PROPS'][SECTION_BEFORE_DELIVERY], $arResult['ORDER_PROPS'][SECTION_AFTER_DELIVERY]] as $arPropertySet) {
                                    foreach ($arPropertySet as $arProperty) {
                                        if (empty($arProperty['VALUE']) && $arProperty['REQUIED_FORMATED'] !== 'Y') {
                                            continue;
                                        }
                                        if ($arProperty['IS_LOCATION'] === 'Y') {
                                            $resLocation = LocationTable::getList([
                                                'filter' => [
                                                    '=ID' => intval($arProperty['VALUE']),
                                                    'NAME.LANGUAGE_ID' => LANGUAGE_ID,
                                                ],
                                                'select' => [
                                                    'ID', 'LOCATION_NAME' => 'NAME.NAME'
                                                ]
                                            ]);
                                            $arLocation = $resLocation->fetch();
                                            $value = $arLocation ? $arLocation['LOCATION_NAME'] : '';
                                        } else {
                                            $value = $arProperty['VALUE'];
                                        }
                                        ?>
                                        <p><b><?=$arProperty['NAME']?>:</b> <?=$value?></p>
                                        <?php
                                    }
                                }
                                if (!empty($arResult['USER_VALS']['ORDER_DESCRIPTION'])) {
                                    ?>
                                    <p>
                                        <b><?=Loc::getMessage('MODAL_ORDER_DESCRIPTION')?>:</b>
                                        <?=$arResult['USER_VALS']['ORDER_DESCRIPTION']?>
                                    </p>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            break;

                        case 'payment':
                            foreach ($arResult['PAY_SYSTEM'] as $arPaySystem) {
                                if ($arPaySystem['CHECKED'] !== 'Y') {
                                    continue;
                                }
                                ?>
                                <div class="ordering-final__payment">
                                    <?php
                                    if ($arPaySystem['PSA_NAME'] === 'CASH') {
                                        ?>
                                        <div class="way-of-payment__icon">
                                            <svg class="i-cash"><use xlink:href="#i-cash"></use></svg>
                                        </div>
                                        <?php
                                    } elseif ($arPaySystem['PSA_NAME'] === 'CARD') {
                                        ?>
                                        <div class="payment-list">
                                            <div class="payment-list__item">
                                                <svg class="i-visa"><use xlink:href="#i-visa"></use></svg>
                                            </div>
                                            <div class="payment-list__item payment-list__item--nofill">
                                                <svg class="i-master-card"><use xlink:href="#i-master-card"></use></svg>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <span>
                                        <?=$arPaySystem['NAME']?>
                                    </span>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="ordering-final__content_part">
                                <?php
                                if (Loader::includeModule('its.maxma')) {
                                    foreach ($arResult['ORDER_PROPS'][SECTION_MAXMA] as $arProperty) {
                                        if ($arProperty['CODE'] === MaxmaTool::getOrderBonusField()) {
                                            $bonusAll = floatval($arResult['MAXMA_USER']['BALANCE']);
                                            $bonusSelected = floatval($arProperty['VALUE']);
                                            if ($bonusSelected > 0 && $bonusSelected <= $bonusAll) {
                                                ?>
                                                <p>
                                                    <b>Списать:</b>
                                                    <?=$bonusSelected?> из <?=$bonusAll?> бонусов
                                                </p>
                                                <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <?php
                            break;
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="ordering-final__part">
            <div class="ordering-final__head">
                <div class="ordering-final__head_part">
                    <div class="ordering__content_title">
                        <?=Loc::getMessage('ORDER_TOTAL_TITLE')?>
                    </div>
                </div>
            </div>
            <div class="ordering-final__content">
                <div class="ordering-final__content_part">
                    <p>
                        <b>
                            <?=Loc::getMessage('ORDER_TOTAL_BASKET')?>
                        </b>
                        <?=$arResult['ORDER_PRICE_FORMATED']?>
                    </p>
                    <p>
                        <b>
                            <?=Loc::getMessage('ORDER_TOTAL_DELIVERY')?>
                        </b>
                        <?=$arResult['DELIVERY_PRICE_FORMATED']?>
                    </p>
                    <?php
                    if (floatval($arResult['MAXMA']['DISCOUNT']['BONUS']) > 0) {
                        ?>
                        <p>
                            <b>
                                <?=Loc::getMessage('ORDER_TOTAL_BONUS')?>
                            </b>
                            <?=\CCurrencyLang::CurrencyFormat(floatval($arResult['MAXMA']['DISCOUNT']['BONUS']), 'RUB')?>
                        </p>
                        <?php
                    }
                    if (floatval($arResult['MAXMA']['DISCOUNT']['PROMOCODE']) > 0) {
                        ?>
                        <p>
                            <b>
                                <?=Loc::getMessage('ORDER_TOTAL_PROMOCODE')?>
                            </b>
                            <?=\CCurrencyLang::CurrencyFormat(floatval($arResult['MAXMA']['DISCOUNT']['PROMOCODE']), 'RUB')?>
                        </p>
                        <?php
                    }
                    if ($arResult['DISCOUNT_PRICE'] > 0) {
                        ?>
                        <p>
                            <b>
                                <?=Loc::getMessage('ORDER_TOTAL_DISCOUNTS')?>
                            </b>
                            <?=$arResult['DISCOUNT_PRICE_FORMATED']?>
                        </p>
                        <?php
                    }
                    ?>
                    <p>
                        <b>
                            <?=Loc::getMessage('ORDER_TOTAL_SUM')?>
                        </b>
                        <?=$arResult['ORDER_TOTAL_PRICE_FORMATED']?>
                    </p>
                </div>
            </div>
        </div>
        <div class="ordering__form_part ordering__form_part--fixed">
            <button class="button-black" data-order-submit="<?=$currentStep?>" data-order-step="4">
                <?=Loc::getMessage('MODAL_ORDER_SUBMIT')?>
            </button>
        </div>
    </div>
</div>
