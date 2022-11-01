<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Context;

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
$request = Context::getCurrent()->getRequest();

const SECTION_PERSONAL = 'Личные данные';
const SECTION_BEFORE_DELIVERY = 'Местоположение';
const SECTION_AFTER_DELIVERY = 'Доставка';
const SECTION_MAXMA = 'Maxma';

$arSteps = [
    'personal' => [
        'NAME' => Loc::getMessage('MODAL_ORDER_STEP_1_TITLE'),
        'FILE_NAME' => 'step_1_personal.php',
    ],
    'delivery' => [
        'NAME' => Loc::getMessage('MODAL_ORDER_STEP_2_TITLE'),
        'FILE_NAME' => 'step_2_delivery.php',
    ],
    'payment' => [
        'NAME' => Loc::getMessage('MODAL_ORDER_STEP_3_TITLE'),
        'FILE_NAME' => 'step_3_payment.php',
    ],
    'ready' => [
        'NAME' => Loc::getMessage('MODAL_ORDER_STEP_4_TITLE'),
        'FILE_NAME' => 'step_4_ready.php',
    ],
];

$currentStep = $request->get('step');
if (!in_array($currentStep, array_keys($arSteps))) {
    $currentStep = 'personal';
}

$stepCurrentNumber = intval(array_search($currentStep, array_keys($arSteps))) + 1;
$stepsCount = count($arSteps);

$arResult['CURRENT_STEP'] = $currentStep;

require_once __DIR__ . '/function.php';
$request = Context::getCurrent()->getRequest();

if ($arResult['USER_VALS']['CONFIRM_ORDER'] == 'Y') {
    require __DIR__ . '/confirm.php';
    return;
}

?>
<section class="modal modal--ordering-modal modal--aside" data-modal="ordering-modal">
    <button class="modal__overlay" type="button" data-modal-close="ordering-modal">
        <button class="modal__mobile-close"></button>
    </button>
    <div class="modal__container">
        <div class="modal__content">
            <form class="ordering js-form is-active" id="jsModalOrderForm">
                <?php
                if (count($arResult['BASKET']) === 0) {
                    require __DIR__ . '/step_empty_basket.php';
                } else {
                    echo $arResult['CUSTOM_DELIVERY_CONTENT'];
                    echo bitrix_sessid_post();

                    if (!empty($arResult['ERROR']) && $arResult['USER_VALS']['FINAL_STEP'] == 'Y') {
                        ?>
                        <script>
                            showMessage({
                                title: <?=json_encode(Loc::getMessage('ORDER_ERROR'))?>,
                                text: <?=json_encode(implode('<br>', $arResult['ERROR']))?>,
                                time: 10000,
                            });
                        </script>
                        <?php
                    }
                    ?>
                    <div class="ordering__head media-min--tab">
                        <select class="select js-select" data-go-select>
                            <?php
                            foreach ($arSteps as $stepCode => $arStep) {
                                ?>
                                <option value="<?=$stepCode?>" <?=($currentStep === $stepCode ? 'selected' : '')?>>
                                    <?=$arStep['NAME']?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                        <div class="ordering__price">
                            <?=$arResult['ORDER_TOTAL_PRICE_FORMATED']?>
                        </div>
                    </div>
                    <?php
                    $beforeStepCode = '';
                    foreach (array_keys($arSteps) as $stepCode) {
                        if ($currentStep === $stepCode) {
                            if (!empty($beforeStepCode)) {
                                ?>
                                <button class="ordering-back media-max--tab is-active" data-order-go="<?=$beforeStepCode?>">
                                    <svg class="i-arrow-left-thin"><use xlink:href="#i-arrow-left-thin"></use></svg>
                                </button>
                                <?php
                            } else {
                                // TODO Верстка едет, если нет блока
                                ?>
                                <div class="ordering-back media-max--tab"></div>
                                <?php
                            }
                            break;
                        }
                        $beforeStepCode = $stepCode;
                    }
                    ?>
                    <div class="ordering__main">
                        <div class="ordering__main_counter <?=($stepCurrentNumber < $stepsCount ? 'is-active' : '')?>">
                            <?=Loc::getMessage('MODAL_ORDER_STEP_COUNTER', [
                                '#N#' => $stepCurrentNumber,
                                '#CNT#' => $stepsCount - 1,
                            ])?>
                        </div>
                        <div class="ordering__main_part">
                            <?php
                            foreach ($arSteps as $stepCode => $arStep) {
                                require __DIR__ . "/{$arStep['FILE_NAME']}";
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </form>
            <button class="ordering-modal__close" data-modal-close="ordering-modal"></button>
        </div>
    </div>
    <script data-skip-moving="true">
        window.modalOrderScriptLoaded = function () {
            window.ModalOrderInstance = new ModalOrder(
                'jsModalOrderForm',
                {currentPage: '<?=$APPLICATION->GetCurPage(false)?>?siteId=<?=SITE_ID?>'}
            );
        }
    </script>
    <script
        data-skip-moving="true"
        data-call="modalOrderScriptLoaded"
        src="<?="{$templateFolder}/script.js?v=" . filemtime(__DIR__ . '/script.js')?>">
    </script>
</section>
