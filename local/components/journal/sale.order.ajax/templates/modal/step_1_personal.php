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

$arStep = $arSteps['personal'];
if ($currentStep !== 'personal') {
    foreach ($arResult['ORDER_PROPS'][SECTION_PERSONAL] as $arProperty) {
        ?>
        <input type="hidden" name="<?=$arProperty['FIELD_NAME']?>" value="<?=$arProperty['VALUE']?>">
        <?php
    }
    return;
}

?>

<input type="hidden" name="step" value="personal">

<script>
    ga4(
        'begin_checkout',
        {
            currency: 'RUB',
            value: <?=json_encode(floatval($arResult['ORDER_PRICE']))?>,
            items: <?=json_encode($arResult['GA4_ITEMS'])?>,
        }
    );
</script>
<div class="ordering__content is-active">
    <div class="ordering__content_title"><?=$arStep['NAME']?></div>
    <div class="ordering__form">
        <?php
        foreach ($arResult['ORDER_PROPS'][SECTION_PERSONAL] as $arProperty) {
            PrintOrderProperty(
                $arProperty,
                $arProperty['CODE'] !== 'PHONE' ? 'data-form-step' : '',
                '',
                1
            );
        }
        ?>
        <div class="ordering__form_part ordering__form_part--fixed" data-form-step>
            <a href="javascript:void(0)" class="button-black" data-order-step="1" data-order-go="delivery">
                <?=Loc::getMessage('MODAL_ORDER_STEP_1_BUTTON')?>
            </a>
        </div>
    </div>
</div>
