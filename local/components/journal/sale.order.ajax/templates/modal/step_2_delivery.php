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
/** @global array $arSteps */
/** @global string $currentStep */

$arStep = $arSteps['delivery'];
if ($currentStep !== 'delivery') {
    foreach ($arResult['ORDER_PROPS'][SECTION_BEFORE_DELIVERY] as $arProperty) {
        ?>
        <input type="hidden" name="<?=$arProperty['FIELD_NAME']?>" value="<?=$arProperty['VALUE']?>">
        <?php
    }

    foreach ($arResult['ORDER_PROPS'][SECTION_AFTER_DELIVERY] as $arProperty) {
        ?>
        <input type="hidden" name="<?=$arProperty['FIELD_NAME']?>" value="<?=$arProperty['VALUE']?>">
        <?php
    }

    foreach ($arResult['DELIVERY'] as $arDelivery) {
        if ($arDelivery['CHECKED'] !== 'Y') {
            continue;
        }
        ?>
        <input type="hidden" name="<?=$arDelivery['FIELD_NAME']?>" value="<?=$arDelivery['ID']?>">
        <?php
    }

    ?>
    <input type="hidden" name="BUYER_STORE" value="<?=$arResult['BUYER_STORE']?>">
    <input type="hidden" name="ORDER_DESCRIPTION" value="<?=$arResult['USER_VALS']['ORDER_DESCRIPTION']?>">
    <?php

    return;
}

?>
<div class="ordering__content">
    <div class="ordering__content_title"><?=$arStep['NAME']?></div>
    <div class="ordering__form">
        <?php
        foreach ($arResult['ORDER_PROPS'][SECTION_BEFORE_DELIVERY] as $arProperty) {
            PrintOrderProperty(
                $arProperty,
                $arProperty['CODE'] === 'LOCATION' ? "data-order-reload='{$currentStep}'" : '',
                '',
                2
            );
        }
        ?>
        <div class="ordering__form_part ordering__form_part--full">
            <div class="delivery">
                <?php
                $deliveryId = 0;
                foreach ($arResult['DELIVERY'] as $arDelivery) {
                    if ($arDelivery['CHECKED'] === 'Y') {
                        $deliveryId = intval($arDelivery['ID']);
                        ?>
                        <script>
                            ga4(
                                'add_shipping_info',
                                {
                                    currency: 'RUB',
                                    shipping_tier: <?=json_encode($arDelivery['OWN_NAME'])?>,
                                    value: <?=json_encode(floatval($arDelivery['PRICE']))?>,
                                    items: <?=json_encode($arResult['GA4_ITEMS'])?>,
                                }
                            );
                        </script>
                        <?php
                    }
                    ?>
                    <div class="delivery-item">
                        <input type="radio"
                            value="<?=$arDelivery['ID']?>"
                            id="DELIVERY_<?=$arDelivery['ID']?>"
                            name="<?=$arDelivery['FIELD_NAME']?>"
                            <?=($arDelivery['CHECKED'] === 'Y' ? 'checked' : '')?>
                            data-order-reload="<?=$currentStep?>"
                        />
                        <label for="DELIVERY_<?=$arDelivery['ID']?>">
                            <div class="delivery-item__head">
                                <div class="delivery-item__title">
                                    <?=$arDelivery['OWN_NAME']?>
                                </div>
                                <div class="delivery-item__description">
                                    <?=$arDelivery['DESCRIPTION']?>
                                </div>
                            </div>
                            <div class="delivery-item__price">
                                <?php
                                $price = isset($arDelivery['DELIVERY_DISCOUNT_PRICE']) && $arDelivery['DELIVERY_DISCOUNT_PRICE'] < $arDelivery['PRICE']
                                    ? $arDelivery['DELIVERY_DISCOUNT_PRICE']
                                    : $arDelivery['PRICE'];
                                $priceFormatted = isset($arDelivery['DELIVERY_DISCOUNT_PRICE']) && $arDelivery['DELIVERY_DISCOUNT_PRICE'] < $arDelivery['PRICE']
                                    ? $arDelivery['DELIVERY_DISCOUNT_PRICE_FORMATED']
                                    : $arDelivery['PRICE_FORMATED'];
                                if ($price <= 0) {
                                    echo Loc::getMessage('MODAL_ORDER_FREE');
                                } else {
                                    echo $priceFormatted;
                                }
                                ?>
                            </div>
                        </label>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

        <?php
        foreach ($arResult['DELIVERY'] as $arDelivery) {
            if ($arDelivery['CHECKED'] !== 'Y' || !is_array($arDelivery['STORE']) || empty($arDelivery['STORE'])) {
                continue;
            }
            ?>
            <div class="ordering__form_part ordering__form_part--full is-reloadable">
                <select class="select js-select" name="BUYER_STORE" id="BUYER_STORE">
                    <?php
                    foreach ($arDelivery['STORE'] as $storeId) {
                        $storeId = intval($storeId);
                        $arStore = $arResult['STORE_LIST'][$storeId];
                        ?>
                        <option value="<?=$storeId?>" <?=($storeId === intval($arResult['BUYER_STORE']) ? 'selected' : '')?>>
                            <?="{$arStore['TITLE']}. {$arStore['ADDRESS']}"?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <?php
        }
        ?>

        <?php
        foreach ($arResult['ORDER_PROPS'][SECTION_AFTER_DELIVERY] as $arProperty) {
            PrintOrderProperty($arProperty, '', '', 2, $deliveryId, $arResult);
        }
        ?>
        <div class="ordering__form_part ordering__form_part--full is-reloadable">
            <div class="input">
                <input type="text"
                    name="ORDER_DESCRIPTION"
                    id="ORDER_DESCRIPTION"
                    value="<?=$arResult['USER_VALS']['ORDER_DESCRIPTION']?>"
                />
                <label class="input__label" for="ORDER_DESCRIPTION">
                    <?=Loc::getMessage('MODAL_ORDER_DESCRIPTION')?>
                </label>
                <div class="input__bar"></div>
            </div>
        </div>

        <div class="ordering__form_part ordering__form_part--fixed is-reloadable">
            <button class="button-black" data-order-go="payment" data-order-step="2">
                <?=Loc::getMessage('MODAL_ORDER_STEP_2_BUTTON')?>
            </button>
        </div>
    </div>
</div>
