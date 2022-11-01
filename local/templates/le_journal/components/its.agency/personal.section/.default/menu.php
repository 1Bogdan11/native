<?php

use Bitrix\Main\Localization\Loc;
use Journal\Favorite\Favorite;
use Its\Library\Iblock\Iblock;
use Bitrix\Sale\OrderTable;
use Bitrix\Main\Loader;

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

$favorite = new Favorite(Iblock::getInstance()->get('catalog'));

Loader::includeModule('catalog');
Loader::includeModule('sale');

$resActiveOrders = OrderTable::getList([
    'filter' => [
        '!STATUS_ID' => 'F',
        '!CANCELED' => 'Y',
    ],
    'select' => ['ID'],
]);

$arMenu = [
    'orders' => [
        'NAME' => Loc::getMessage('PERSONAL_MENU_ORDERS'),
        'URL' => 'orders/',
        'COUNT' => $resActiveOrders->getSelectedRowsCount(),
        'AUTH' => true,
    ],
    'favorite' => [
        'NAME' => Loc::getMessage('PERSONAL_MENU_FAVORITE'),
        'URL' => 'favorite/',
        'COUNT' => count($favorite->getItems()),
        'AUTH' => false,
    ],
    '' => [
        'NAME' => Loc::getMessage('PERSONAL_MENU_PROFILE'),
        'URL' => '',
        'AUTH' => true,
    ],
    'address' => [
        'NAME' => Loc::getMessage('PERSONAL_MENU_ADDRESS'),
        'URL' => 'address/',
        'AUTH' => true,
    ],
    'send' => [
        'NAME' => Loc::getMessage('PERSONAL_MENU_SEND'),
        'URL' => 'send/',
        'AUTH' => true,
    ],
];
?>
<div class="profile__main-left" data-scroll data-scroll-sticky data-scroll-target=".profile__main">
    <nav class="profile__nav">
        <?php
        foreach ($arMenu as $template => $arItem) {
            if (!$USER->IsAuthorized() && $arItem['AUTH']) {
                continue;
            }
            ?>
            <a class="profile__nav-link"
                <?=(($arParams['SELECTED_TEMPLATE'] ?? $arResult['TEMPLATE_NAME']) === $template ? 'data-active' : '')?>
                href="<?=$arParams['SEF_FOLDER'] . $arItem['URL']?>">
                <?=$arItem['NAME']?>
            </a>
            <?php
            if (($arParams['SELECTED_TEMPLATE'] ?? $arResult['TEMPLATE_NAME']) === $template && $arItem['COUNT'] > 0) {
                $APPLICATION->AddViewContent(
                    'PERSONAL_TITLE_COUNTER',
                    "<span class='profile__header-title-indicator'>{$arItem['COUNT']}</span>"
                );
            }
        }
        ?>
    </nav>
    <?php
    if ($USER->IsAuthorized()) {
        ?>
        <a class="profile__exit-button" href="<?=SITE_DIR . '?logout=yes&' . bitrix_sessid_get()?>">
            <?=Loc::getMessage('PERSONAL_MENU_EXIT')?>
        </a>
        <?php
    }
    ?>
</div>
<?php

$this->SetViewTarget('PERSONAL_MENU_MOBILE');
?>
<div class="profile__m-menu">
    <span class="profile__m-menu-arrow"></span>
    <div class="profile__m-menu-content js-toggle__btn">
        <div class="profile__m-menu-list">
            <?php
            foreach ($arMenu as $template => $arItem) {
                if (!$USER->IsAuthorized() && $arItem['AUTH']) {
                    continue;
                }
                ?>
                <a class="profile__m-menu-option <?=($arResult['TEMPLATE_NAME'] === $template ? 'is-active' : '')?>"
                    data-link
                    href="<?=$arParams['SEF_FOLDER'] . $arItem['URL']?>">
                    <?=$arItem['NAME']?>
                    <?php
                    if ($arItem['COUNT'] > 0) {
                        ?>
                        <span class="profile__m-menu-indicator"><?=$arItem['COUNT']?></span>
                        <?php
                    }
                    ?>
                </a>
                <?php
            }

            if ($USER->IsAuthorized()) {
                ?>
                <a class="profile__m-menu-option" href="<?=SITE_DIR . '?logout=yes&' . bitrix_sessid_get()?>" data-link>
                    <?=Loc::getMessage('PERSONAL_MENU_EXIT')?>
                </a>
                <?php
            }
            ?>
        </div>
    </div>
</div>
<?php
$this->EndViewTarget();
