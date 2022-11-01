<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Sale\Internals\OrderTable;
use Bitrix\Main\Context;
use Bitrix\Iblock\Component\Tools;
use Bitrix\Main\Entity\ExpressionField;

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

$APPLICATION->SetTitle(Loc::getMessage('PERSONAL_ORDERS_HEAD'));

?>
<div class="container profile js-toggle">
    <div class="profile__header">
        <div class="profile__header-left">
            <?php $APPLICATION->IncludeComponent(
                'journal:personal.user',
                '',
                [],
                true
            )?>
        </div>
        <div class="profile__header-right">
            <div class="profile__header-title-wrapper">
                <h5 class="profile__header-title">
                    <?php $APPLICATION->ShowTitle(false)?>
                    <?php $APPLICATION->ShowViewContent('PERSONAL_TITLE_COUNTER')?>
                </h5>
            </div>
            <?php $APPLICATION->ShowViewContent('PERSONAL_MENU_MOBILE')?>
        </div>
    </div>
    <div class="profile__main">
        <?php $component->includeComponentTemplate('menu')?>
        <div class="profile__content">
            <?php
            Loader::includeModule('sale');
            $arOrder = OrderTable::getList([
                'filter' => [[
                    'LOGIC' => 'OR',
                    '=ACCOUNT_NUMBER' => $arResult['ELEMENT_CODE'],
                    '=ID' => $arResult['ELEMENT_ID'],
                ]],
                'select' => ['ID', 'ACCOUNT_NUMBER'],
            ])->fetch();
            if ($arOrder) {
                $arResult['ELEMENT_ID'] = intval($arOrder['ID']);
            }

            if ($arResult['ELEMENT_ID'] > 0) {
                $APPLICATION->AddChainItem(Loc::getMessage('PERSONAL_ORDERS_HEAD'), $arParams['SEF_FOLDER'] . 'orders/');

                if ($_REQUEST['CANCEL'] === 'Y') {
                    $title = Loc::getMessage(
                        'PERSONAL_ORDER_CANCEL_HEAD',
                        ['#NUMBER#' => $arOrder['ACCOUNT_NUMBER']]
                    );
                    $APPLICATION->SetTitle($title);
                    $APPLICATION->AddChainItem($title);
                    $APPLICATION->IncludeComponent(
                        "bitrix:sale.personal.order.cancel",
                        "",
                        [
                            'PATH_TO_LIST' => $arParams['SEF_FOLDER'] . 'orders/',
                            'PATH_TO_DETAIL' => $arParams['SEF_FOLDER'] . 'orders/?order=#ID#',
                            'SET_TITLE' => 'N',
                            'ID' => $arResult['ELEMENT_ID'],
                        ],
                        $component
                    );
                } else {
                    $title = Loc::getMessage(
                        'PERSONAL_ORDER_HEAD',
                        ['#NUMBER#' => $arOrder['ACCOUNT_NUMBER']]
                    );
                    $APPLICATION->SetTitle($title);
                    $APPLICATION->AddChainItem($title);

                    while (ob_get_level()) {
                        ob_end_clean();
                    }
                    $APPLICATION->IncludeComponent(
                        'bitrix:sale.personal.order.detail',
                        '',
                        [
                            'PATH_TO_LIST' => $arParams['SEF_FOLDER'] . 'orders/',
                            'PATH_TO_CANCEL' => $arParams['SEF_FOLDER'] . 'orders/#ID#/',
                            'PATH_TO_COPY' => '',
                            'PATH_TO_PAYMENT' => $arParams['SEF_FOLDER'] . 'payment/',
                            'SET_TITLE' => 'N',
                            'ID' => $arResult['ELEMENT_ID'],
                            'ACTIVE_DATE_FORMAT' => $arParams['ACTIVE_DATE_FORMAT'],
                            'ALLOW_INNER' => 'N',
                            'ONLY_INNER_FULL' => 'Y',
                            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                            'CACHE_TIME' => $arParams['CACHE_TIME'],
                            'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                            'RESTRICT_CHANGE_PAYSYSTEM' => ['F'],
                            'DISALLOW_CANCEL' => 'N',
                            'REFRESH_PRICES' => 'Y',
                        ],
                        $component,
                        ['HIDE_ICONS' => 'Y']
                    );
                    die();
                }
            } else {
                $orderId = intval($_REQUEST['order']);
                if ($orderId > 0) {
                    ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                           document.dispatchEvent(new CustomEvent(
                               'modal:load',
                               {detail: {url: <?=json_encode("{$arParams['SEF_FOLDER']}orders/$orderId/")?>}}
                           ))
                        });
                    </script>
                    <?php
                }
                $APPLICATION->AddChainItem(Loc::getMessage('PERSONAL_ORDERS_HEAD'));
                ?>
                <div class="profile__content-title">
                    <p>
                        <?=Loc::getMessage('PERSONAL_ORDERS_ACTIVE_TITLE')?>
                    </p>
                </div>
                <?php
                $APPLICATION->IncludeComponent(
                    'journal:sale.personal.order.list',
                    '',
                    [
                        'SHOW_TYPE' => 'ACTIVE',
                        'SHOW_YEAR' => '',
                        'PATH_TO_DETAIL' => "{$arParams['SEF_FOLDER']}orders/#ID#/",
                        'PATH_TO_CANCEL' => "{$arParams['SEF_FOLDER']}orders/#ID#/",
                        'PATH_TO_CATALOG' => $arParams['PATH_TO_CATALOG'],
                        'PATH_TO_COPY' => '',
                        'PATH_TO_BASKET' => $arParams['PATH_TO_BASKET'],
                        'PATH_TO_PAYMENT' => "{$arParams['SEF_FOLDER']}payment/",
                        'SAVE_IN_SESSION' => 'N',
                        'ORDERS_PER_PAGE' => $arParams['ORDERS_ELEMENT_COUNT'],
                        'SET_TITLE' => 'N',
                        'ID' => $_REQUEST['ID'],
                        'NAV_TEMPLATE' => 'personal',
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
                    ],
                    $component,
                    ['HIDE_ICONS' => 'Y']
                );


                $resOrders = OrderTable::getList([
                    'filter' => [[
                        'LOGIC' => 'OR',
                        '=STATUS_ID' => 'F',
                        '=CANCELED' => 'Y',
                    ]],
                    'select' => [
                        new ExpressionField(
                            'YEAR',
                            'YEAR(%s)',
                            ['DATE_INSERT']
                        )
                    ],
                    'group' => ['YEAR']
                ]);
                $usedYears = [];
                while ($arOrder = $resOrders->fetch()) {
                    $usedYears[] = $arOrder['YEAR'];
                }

                if (count($usedYears) > 0) {
                    ?>
                    <hr class="profile__content-separator">
                    <div class="profile__content-title">
                        <p>
                            <?=Loc::getMessage('PERSONAL_ORDERS_HISTORY_TITLE')?>
                        </p>
                        <hr class="profile__content-title-separator">
                        <div class="profile__content-title-filter">
                            <?php
                            foreach ($usedYears as $year) {
                                ?>
                                <a href="<?=$APPLICATION->GetCurPageParam("year={$year}", ['year'])?>"
                                   class="profile__content-title-year <?=($_REQUEST['year'] == $year ? 'is-active' : '')?>">
                                    <?=$year?>
                                </a>
                                <?php
                            }
                            ?>
                        </div>
                        <select class="select js-select js-incomes-select"
                                data-scroll
                                data-observe="fade-y"
                                id="jsYearFilter">
                            <?php
                            foreach ($usedYears as $year) {
                                ?>
                                <option
                                        value="<?=$APPLICATION->GetCurPageParam("year={$year}", ['year'])?>"
                                    <?=($_REQUEST['year'] == $year ? 'selected' : '')?>>
                                    <?=$year?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                        <script>
                            document.getElementById('jsYearFilter').addEventListener('change', event => {
                                document.location.href = event.target.value;
                            });
                        </script>
                    </div>
                    <?php
                    $APPLICATION->IncludeComponent(
                        'journal:sale.personal.order.list',
                        '',
                        [
                            'SHOW_TYPE' => 'HISTORY',
                            'SHOW_YEAR' => $_REQUEST['year'],
                            'PATH_TO_DETAIL' => "{$arParams['SEF_FOLDER']}orders/#ID#/",
                            'PATH_TO_CANCEL' => "{$arParams['SEF_FOLDER']}orders/#ID#/",
                            'PATH_TO_CATALOG' => $arParams['PATH_TO_CATALOG'],
                            'PATH_TO_COPY' => '',
                            'PATH_TO_BASKET' => $arParams['PATH_TO_BASKET'],
                            'PATH_TO_PAYMENT' => "{$arParams['SEF_FOLDER']}payment/",
                            'SAVE_IN_SESSION' => 'N',
                            'ORDERS_PER_PAGE' => $arParams['ORDERS_ELEMENT_COUNT'],
                            'SET_TITLE' => 'N',
                            'ID' => $_REQUEST['ID'],
                            'NAV_TEMPLATE' => 'personal',
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
                        ],
                        $component,
                        ['HIDE_ICONS' => 'Y']
                    );
                }
            }
            ?>
        </div>
    </div>
</div>

