<?php

use Bitrix\Main\UserTable;
use Bitrix\Sale\Delivery\Services\Manager;
use Bitrix\Sale\Location\LocationTable;
use Bitrix\Main\Loader;
use Its\Maxma\Api\Tool as MaxmaTool;
use Bitrix\Main\Localization\Loc;

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

if (!function_exists('BonusFormatRu')) {
    function BonusFormatRu(string $number, int $decimals = 2): string
    {
        $number = floatval(preg_replace('/[^0-9.,]/', '', $number));
        $decimals = round($number, $decimals) === round($number, 0) ? 0 : $decimals;
        return number_format($number, $decimals, ',', ' ');
    }
}
if (!function_exists('PrintOrderProperty')) {
    function PrintOrderProperty(array $arProperty, string $attribute = '', string $class = '', int $step = 1, int $deliveryId = 0, array $arResult = [])
    {
        global $USER, $APPLICATION;

        if (!empty($arProperty)) {
            $isRequired = $arProperty['REQUIED_FORMATED'] === 'Y';

            switch ($arProperty['TYPE']) {
                case 'TEXT':
                    $type = 'text';
                    if ($arProperty['IS_EMAIL'] === 'Y') {
                        $type = 'email';
                    }

                    if (intval($arProperty['MAXLENGTH']) > 50) {
                        $class .= ' ordering__form_part--full';
                    }

                    if (strlen($attribute) === 0) {
                        $class .= ' is-reloadable';
                    }

                    if (Loader::includeModule('its.maxma')) {
                        if ($arProperty['CODE'] === MaxmaTool::getOrderBonusField()) {
                            $bonus = floatval($arProperty['RESULT']['MAXMA_USER']['BALANCE']);
                            $maxApply = floatval($arProperty['RESULT']['MAXMA']['BONUS']['MAX_APPLY']);
                            if ($bonus <= 0) {
                                return;
                            }
                            $arProperty['VALUE'] = max(0, min(floatval($arProperty['VALUE']), $bonus, $arProperty['RESULT']['MAXMA']['BONUS']['MAX_APPLY']));
                            $class .= ' ordering__form_part--full';
                            $declension = new \Its\Library\Tool\Declension(
                                Loc::getMessage('MODAL_ORDER_FIELDS_BONUS_ONE'),
                                Loc::getMessage('MODAL_ORDER_FIELDS_BONUS_TWO'),
                                Loc::getMessage('MODAL_ORDER_FIELDS_BONUS_MANY')
                            );
                            ?>
                            <div class="ordering__form_part <?=$class?>" <?=$attribute?>>
                                <div class="ordering__loyalty">
                                    <div class="ordering__loyalty-title"><?=Loc::getMessage('MODAL_ORDER_FIELDS_BONUS_INPUT_TITLE')?></div>
                                    <table class="ordering__loyalty-content">
                                        <tbody>
                                        <tr>
                                            <td><?=Loc::getMessage('MODAL_ORDER_FIELDS_BONUS_INPUT_BALANCE')?></td>
                                            <td>
                                                <?php
                                                echo BonusFormatRu($bonus);
                                                echo '&nbsp;';
                                                echo $declension->getMessage($bonus);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?=Loc::getMessage('MODAL_ORDER_FIELDS_BONUS_INPUT_AVAILABLE')?></td>
                                            <td>
                                                <?php
                                                echo BonusFormatRu($maxApply);
                                                echo '&nbsp;';
                                                echo $declension->getMessage($maxApply);
                                                ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div class="input">
                                        <input class="<?=($isRequired ? 'validate' : '')?>"
                                            type="<?=$type?>"
                                            name="<?=$arProperty['FIELD_NAME']?>"
                                            id="<?=$arProperty['FIELD_NAME']?>"
                                            value="<?=$arProperty['VALUE']?>"
                                            data-property-code="<?=$arProperty['CODE']?>"
                                        />
                                        <label class="input__label" for="<?=$arProperty['FIELD_NAME']?>">
                                            <?=Loc::getMessage('MODAL_ORDER_FIELDS_BONUS_INPUT_NAME') . ($isRequired ? '*' : '')?>
                                        </label>
                                        <div class="input__bar"></div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            return;
                        }
                    }

                    if ($arProperty['CODE'] === 'PHONE') {
                        $arUser = UserTable::getById(intval($USER->GetID()))->fetch();
                        $userPhoneNumber = $arUser ? [$arUser['PERSONAL_PHONE']] : [];
                        if (empty($arProperty['VALUE']) && $arUser) {
                            $arProperty['VALUE'] = $arUser['PERSONAL_PHONE'];
                        }
                        $APPLICATION->IncludeComponent(
                            'its.agency:phone.check',
                            'order',
                            [
                                'DEV_MODE' => 'N',
                                'PLACEHOLDER' => $arProperty['NAME'] . ($isRequired ? '*' : ''),
                                'USER_PHONES' => $userPhoneNumber,
                                'FIELD_NAME' => $arProperty['FIELD_NAME'],
                                'FIELD_VALUE' => $arProperty['VALUE'],
                                'FIELD_VALUE_COUNTRY' => '',
                                'CONFIRM_CODE_LENGTH' => 4,
                                'RESEND_LIMIT' => 30,
                                'SMS_EVENT_CODE' => 'SMS_USER_CONFIRM_NUMBER',
                            ],
                            false,
                            ['HIDE_ICONS' => 'Y']
                        );
                        break;
                    }
                    if ($arProperty['CODE'] === 'ADDRESS') {
                        $fieldType = 'ADDRESS';
                        foreach (Manager::getActiveList() as $arDelivery) {
                            if (intval($arDelivery['ID']) === $deliveryId) {
                                if (strpos($arDelivery['CODE'], 'boxberry:PVZ') !== false) {
                                    $fieldType = 'BOXBERRY_PVZ';
                                    break;
                                }
                            }
                        }
                        switch ($fieldType) {
                            case 'BOXBERRY_PVZ':
                                if (strpos($arProperty['VALUE'], 'Boxberry: ') === false) {
                                    /*
                                     * Если в адресе НЕ указан пункт самовывоза, то чистим,
                                     * чтобы пользователь не оформил заказ без пункта
                                     */
                                    $arProperty['VALUE'] = '';
                                }
                                ?>
                                <div class="ordering__form_part <?=$class?>" <?=$attribute?>>
                                    <div class="input" data-order-step-field="<?=$step?>">
                                        <input class="<?=($isRequired ? 'validate' : '')?>"
                                            type="<?=$type?>"
                                            name="<?=$arProperty['FIELD_NAME']?>"
                                            id="<?=$arProperty['FIELD_NAME']?>"
                                            value="<?=$arProperty['VALUE']?>"
                                            data-property-code="<?=$arProperty['CODE']?>"
                                            data-order-reload="<?=$arResult['CURRENT_STEP']?>"
                                        />
                                        <label class="input__label" for="<?=$arProperty['FIELD_NAME']?>">
                                            <?=$arProperty['NAME'] . ($isRequired ? '*' : '')?>
                                        </label>
                                        <div class="input__bar"></div>
                                    </div>
                                    <div class="ordering__form_boxberry" id="boxberrySelectPvzWrap"></div>
                                </div>
                                <?php
                                break;

                            default:
                                if (strpos($arProperty['VALUE'], 'Boxberry: ') !== false) {
                                    /*
                                     * Если в адресе указан пункт самовывоза, то чистим
                                     */
                                    $arProperty['VALUE'] = '';
                                }

                                $locationId = 0;
                                foreach ($arResult['ORDER_PROPS'] as $groupName => $arProps) {
                                    foreach ($arProps as $arProp) {
                                        if ($arProp['IS_LOCATION'] === 'Y') {
                                            $locationId = intval($arProp['VALUE']);
                                            break 2;
                                        }
                                    }
                                }
                                $resLocation = LocationTable::getList([
                                    'filter' => [
                                        '=ID' => $locationId,
                                        'NAME.LANGUAGE_ID' => LANGUAGE_ID,
                                    ],
                                    'select' => [
                                        'ID', 'LOCATION_NAME' => 'NAME.NAME'
                                    ]
                                ]);
                                $arLocation = $resLocation->fetch();
                                ?>
                                <div class="ordering__form_part <?=$class?>" <?=$attribute?>>
                                    <div class="input"
                                        data-order-step-field="<?=$step?>"
                                        data-suggestions="<?=DADATA_PUBLIC_KEY?>"
                                        data-city="<?=$arLocation['LOCATION_NAME']?>"
                                        data-value="<?=$arProperty['VALUE']?>">
                                        <input class="<?=($isRequired ? 'validate' : '')?>"
                                            type="<?=$type?>"
                                            name="<?=$arProperty['FIELD_NAME']?>"
                                            id="<?=$arProperty['FIELD_NAME']?>"
                                            value="<?=$arProperty['VALUE']?>"
                                            data-property-code="<?=$arProperty['CODE']?>"
                                        />
                                        <label class="input__label" for="<?=$arProperty['FIELD_NAME']?>">
                                            <?=$arProperty['NAME'] . ($isRequired ? '*' : '')?>
                                        </label>
                                        <div class="input__bar"></div>
                                        <div class="input__suggestions">
                                            <ul class="input__suggestions-inner"></ul>
                                        </div>
                                        <span class="input__suggestions-error"></span>
                                    </div>
                                </div>
                                <?php
                                break;
                        }
                        break;
                    }
                    ?>
                    <div class="ordering__form_part <?=$class?>" <?=$attribute?>>
                        <div class="input" data-order-step-field="<?=$step?>">
                            <input class="<?=($isRequired ? 'validate' : '')?>"
                                type="<?=$type?>"
                                name="<?=$arProperty['FIELD_NAME']?>"
                                id="<?=$arProperty['FIELD_NAME']?>"
                                value="<?=$arProperty['VALUE']?>"
                                data-property-code="<?=$arProperty['CODE']?>"
                            />
                            <label class="input__label" for="<?=$arProperty['FIELD_NAME']?>">
                                <?=$arProperty['NAME'] . ($isRequired ? '*' : '')?>
                            </label>
                            <div class="input__bar"></div>
                        </div>
                    </div>
                    <?php
                    break;

                case 'LOCATION':
                    $APPLICATION->IncludeComponent(
                        'journal:sale.location.selector.search',
                        'order',
                        [
                            'PROPERTY_NAME' => $arProperty['NAME'],
                            'INPUT_VALUE' => $arProperty['VALUE'],
                            'INPUT_NAME' => $arProperty['FIELD_NAME'],
                            'IS_REQUIRED' => $isRequired ? 'Y' : 'N',
                            'ATTRIBUTE' => $attribute,
                            'STEP' => $step,
                        ],
                        true,
                        ['HIDE_ICONS' => 'Y']
                    );
                    break;

                case 'CHECKBOX':
                case 'MULTISELECT':
                case 'TEXTAREA':
                case 'RADIO':
                case 'FILE':
                case 'SELECT':
                default:
                    break;
            }
        }
    }
}
