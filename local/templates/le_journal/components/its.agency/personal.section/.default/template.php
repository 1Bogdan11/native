<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

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

$this->setFrameMode(false);

$APPLICATION->SetTitle(Loc::getMessage('PERSONAL_PROFILE_HEAD'));

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
            <?php $APPLICATION->IncludeComponent(
                'journal:main.profile',
                '',
                [
                    'USER_FIELDS' => [
                        'LAST_NAME',
                        'NAME',
                        'SECOND_NAME',
                        'EMAIL',
                        'PERSONAL_BIRTHDAY',
                        'PERSONAL_GENDER',
                        'PERSONAL_PHONE',
                    ],
                    'USER_FIELDS_REQUIRED' => [
                        'NAME',
                        'EMAIL',
                        'PERSONAL_PHONE',
                    ],
                    'CHECK_RIGHTS' => 'Y',
                ],
                $component,
                ['HIDE_ICONS' => 'Y']
            )?>
        </div>
    </div>
</div>

