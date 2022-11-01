<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Sale\Internals\OrderTable;
use Bitrix\Main\Loader;

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

$APPLICATION->SetTitle('Список заказов');

?>
<div class="row">
    <div class="col-lg-3">
        <?$component->includeComponentTemplate('menu')?>
    </div>
    <div class="col-lg-9">
        <?
        if ($arResult['ELEMENT_ID'] > 0 || strlen($arResult['ELEMENT_CODE']) > 0) {
            $APPLICATION->AddChainItem('Список заказов', $arParams['SEF_FOLDER'] . 'orders/');

            if ($arResult['ELEMENT_ID'] <= 0) {
                Loader::includeModule('sale');

                $arOrder = OrderTable::getList([
                    'filter' => ['=ACCOUNT_NUMBER' => $arResult['ELEMENT_CODE']],
                    'select' => ['ID']
                ])->fetch();

                $arResult['ELEMENT_ID'] = intval($arOrder['ID']);
            }

            if ($_REQUEST['CANCEL'] === 'Y') {
                $APPLICATION->IncludeComponent(
                    "bitrix:sale.personal.order.cancel",
                    "",
                    array(
                        'PATH_TO_LIST' => $arParams['SEF_FOLDER'] . 'orders/',
                        'PATH_TO_DETAIL' => $arParams['SEF_FOLDER'] . 'orders/#ID#/',
                        'SET_TITLE' => 'N',
                        'ID' => $arResult['ELEMENT_ID'],
                    ),
                    $component
                );
            } else {
                $APPLICATION->IncludeComponent(
                    "bitrix:sale.personal.order.detail",
                    "",
                    [
                        'PATH_TO_LIST' => $arParams['SEF_FOLDER'] . 'orders/',
                        'PATH_TO_CANCEL' => $arParams['SEF_FOLDER'] . 'orders/#ID#/',
                        'PATH_TO_COPY' => '',
                        'PATH_TO_PAYMENT' => $arParams['SEF_FOLDER'] . 'payment/',
                        'SET_TITLE' => 'N',
                        'ID' => $arResult['ELEMENT_ID'],
                        'ACTIVE_DATE_FORMAT' => $arParams["ACTIVE_DATE_FORMAT"],
                        'ALLOW_INNER' => 'N',
                        'ONLY_INNER_FULL' => 'Y',
                        'CACHE_TYPE' => $arParams["CACHE_TYPE"],
                        'CACHE_TIME' => $arParams["CACHE_TIME"],
                        'CACHE_GROUPS' => $arParams["CACHE_GROUPS"],
                        'RESTRICT_CHANGE_PAYSYSTEM' => ['F'],
                        'DISALLOW_CANCEL' => 'N',
                        'REFRESH_PRICES' => 'Y',
                    ],
                    $component,
                    ['HIDE_ICONS' => 'Y']
                );
            }

            $link = $arParams['SEF_FOLDER'] . 'orders/';
        } else {
            $APPLICATION->AddChainItem('Список заказов');
            $APPLICATION->IncludeComponent(
                "bitrix:sale.personal.order.list",
                "",
                array(
                    'PATH_TO_DETAIL' => $arParams['SEF_FOLDER'] . 'orders/#ID#/',
                    'PATH_TO_CANCEL' => $arParams['SEF_FOLDER'] . 'orders/#ID#/',
                    'PATH_TO_CATALOG' => $arParams['PATH_TO_CATALOG'],
                    'PATH_TO_COPY' => '',
                    'PATH_TO_BASKET' => $arParams['PATH_TO_BASKET'],
                    'PATH_TO_PAYMENT' => $arParams['SEF_FOLDER'] . 'payment/',
                    'SAVE_IN_SESSION' => 'N',
                    'ORDERS_PER_PAGE' => $arParams['ORDERS_ELEMENT_COUNT'],
                    'SET_TITLE' => 'N',
                     'ID' => $_REQUEST['ID'],
                    'NAV_TEMPLATE' => '',
                    'ACTIVE_DATE_FORMAT' => $arParams['ACTIVE_DATE_FORMAT'],
                    'HISTORIC_STATUSES' => ['F'],
                    'RESTRICT_CHANGE_PAYSYSTEM' => ['F'],
                    'ALLOW_INNER' => 'N',
                    'ONLY_INNER_FULL' => 'Y',
                    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                    'CACHE_TIME' => $arParams['CACHE_TIME'],
                    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                    'DEFAULT_SORT' => 'DATE_INSERT',
                    'DISALLOW_CANCEL' => 'N',
                ),
                $component,
                ['HIDE_ICONS' => 'Y']
            );
        }
        ?>
    </div>
</div>
