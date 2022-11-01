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

if (!empty($arResult['ERRORS'])) {
    ?>
    <p style="color:red">
        <?=implode($arResult['ERRORS'])?>
    </p>
    <?php
}
?>

<div class="profile__loyalty">
    <div class="profile__loyalty-head">
        <h2 class="profile__loyalty-title">
            <?=Loc::getMessage('ITS_MAXMA_LOYALTY_USER_TITLE')?>
        </h2>
        <a class="profile__loyalty-link link__underline" href="/support/loyalty-program/">
            <?=Loc::getMessage('ITS_MAXMA_LOYALTY_USER_MORE')?>
        </a>
    </div>
    <table class="profile__loyalty-content">
        <tbody>
        <tr>
            <td>Статус</td>
            <td class="profile__loyalty-active <?=(!empty($arResult['USER']) ? 'is-active' : '')?>">
                <?php
                if (!empty($arResult['USER'])) {
                    echo Loc::getMessage('ITS_MAXMA_LOYALTY_USER_ACTIVE');
                } else {
                    echo Loc::getMessage('ITS_MAXMA_LOYALTY_USER_UNACTIVE');
                }
                ?>
            </td>
        </tr>
        <?php
        if (!empty($arResult['USER'])) {
            if (!empty($arResult['USER']['CARD']['NUMBER'])) {
                ?>
                <tr>
                    <td><?=Loc::getMessage('ITS_MAXMA_LOYALTY_USER_CARD')?></td>
                    <td><?=$arResult['USER']['CARD']['NUMBER']?></td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td><?=Loc::getMessage('ITS_MAXMA_LOYALTY_USER_BALANCE')?></td>
                <td><?=$arResult['USER']['BALANCE']?></td>
            </tr>
            <?php
            if ($arResult['USER']['BALANCE_PENDING']) {
                ?>
                <tr>
                    <td><?=Loc::getMessage('ITS_MAXMA_LOYALTY_USER_PENDING')?></td>
                    <td><?=$arResult['USER']['BALANCE_PENDING']?></td>
                </tr>
                <?php
            }
            foreach ($arResult['USER']['BALANCE_EXPIRE'] as $item) {
                if (empty($item['EXPIRED'])) {
                    continue;
                }
                $date = !empty($item['EXPIRED']) ?  : 'более чем 50 дней';
                ?>
                <tr>
                    <td>
                        <?=Loc::getMessage(
                            'ITS_MAXMA_LOYALTY_USER_EXPIRED',
                            ['#DATE#' => $item['EXPIRED']]
                        )?>
                    </td>
                    <td><?=$item['QUANTITY']?></td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
</div>
<?php

if ($arResult['USER']['CARD']['LINK']) {
    ?>
    <div class="profile__info-button" data-ios="true">
        <a href="<?=$arResult['USER']['CARD']['LINK']?>" class="button-black button-black--icon">
            <img src="/assets/img/apple-wallet.svg" alt="">
            <span>
                <?=Loc::getMessage('ITS_MAXMA_LOYALTY_USER_CARD_SAVE_IOS')?>
            </span>
        </a>
    </div>
    <div class="profile__info-button" data-ios="false">
        <a href="<?=$arResult['USER']['CARD']['LINK']?>" class="button-black button-black--icon">
            <img src="/assets/img/gpay.svg" alt="">
            <span>
                <?=Loc::getMessage('ITS_MAXMA_LOYALTY_USER_CARD_SAVE_ANDROID')?>
            </span>
        </a>
    </div>
    <?php
}
