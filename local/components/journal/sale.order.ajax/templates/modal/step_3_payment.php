<?php

use Bitrix\Main\Localization\Loc;
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

$arStep = $arSteps['payment'];
if ($currentStep !== 'payment') {
    if (Loader::includeModule('its.maxma')) {
        foreach ($arResult['ORDER_PROPS'][SECTION_MAXMA] as $arProperty) {
            if ($arProperty['CODE'] === MaxmaTool::getOrderBonusField()) {
                $bonus = intval($arResult['MAXMA_USER']['BALANCE']);
                $arProperty['VALUE'] = max(0, min(floatval($arProperty['VALUE']), $bonus, $arResult['MAXMA']['BONUS']['MAX_APPLY']));
            }
            ?>
            <input type="hidden" name="<?=$arProperty['FIELD_NAME']?>" value="<?=$arProperty['VALUE']?>">
            <?php
        }
    }
    foreach ($arResult['PAY_SYSTEM'] as $arPaySystem) {
        if ($arPaySystem['CHECKED'] !== 'Y') {
            continue;
        }
        ?>
        <input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem['ID']?>">
        <?php
    }
    return;
}

?>
<div class="ordering__content">
    <div class="ordering__content_title"><?=$arStep['NAME']?></div>
    <div class="ordering__form">
        <div class="ordering__form_part ordering__form_part--full">
            <div class="way-of-payment">
                <?php
                foreach ($arResult['PAY_SYSTEM'] as $arPaySystem) {
                    if ($arPaySystem['CHECKED'] === 'Y') {
                        ?>
                        <script>
                            ga4(
                                'add_payment_info',
                                {
                                    currency: 'RUB',
                                    shipping_tier: <?=json_encode($arPaySystem['NAME'])?>,
                                    value: <?=json_encode(floatval($arResult['ORDER_TOTAL_PRICE']))?>,
                                    items: <?=json_encode($arResult['GA4_ITEMS'])?>,
                                }
                            );
                        </script>
                        <?php
                    }
                    ?>
                    <div class="way-of-payment__item">
                        <input type="radio"
                            value="<?=$arPaySystem['ID']?>"
                            id="PAY_SYSTEM_<?=$arPaySystem['ID']?>"
                            name="PAY_SYSTEM_ID"
                            <?=($arPaySystem['CHECKED'] === 'Y' ? 'checked' : '')?>
                            data-order-reload="<?=$currentStep?>"
                        />
                        <label for="PAY_SYSTEM_<?=$arPaySystem['ID']?>">
                            <div class="way-of-payment__head">
                                <div class="way-of-payment__title">
                                    <?=$arPaySystem['NAME']?>
                                </div>
                                <div class="way-of-payment__description">
                                    <?=$arPaySystem['DESCRIPTION']?>
                                </div>
                            </div>
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
                                    <div class="payment-list__item">
                                        <svg class="i-mir-pay"><use xlink:href="#i-mir-pay"></use></svg>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </label>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
        foreach ($arResult['ORDER_PROPS'][SECTION_MAXMA] as $arProperty) {
            $arProperty['RESULT'] = &$arResult;
            PrintOrderProperty(
                $arProperty,
                "data-order-reload='{$currentStep}'",
                '',
                3
            );
        }
        ?>
        <div class="ordering__form_part ordering__form_part--fixed">
            <button class="button-black" data-order-go="ready" data-order-step="3">
                <?=Loc::getMessage('MODAL_ORDER_STEP_3_BUTTON')?>
            </button>
        </div>
    </div>
</div>
