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

$message = !$arResult['ALREADY_SUBSCRIBED']
    ? Loc::getMessage('PRODUCT_SUBSCRIBE_BUTTON_NAME')
    : Loc::getMessage('PRODUCT_SUBSCRIBE_BUTTON_SUBSCRIBED');

$this->setFrameMode(true);

$display = $arResult['DEFAULT_DISPLAY'] ? '' : 'style="display:none;"';
$class = $arResult['ALREADY_SUBSCRIBED'] ? 'is-added' : '';
$message = !$arResult['ALREADY_SUBSCRIBED'] ? Loc::getMessage('PRODUCT_SUBSCRIBE_BUTTON_NAME') : Loc::getMessage(
    'PRODUCT_SUBSCRIBE_BUTTON_SUBSCRIBED'
);

$this->SetViewTarget('element_subscribe_form');
?>
<form class="want-to-buy" id="elementSubscribeForm">
    <div class="want-to-buy__head is-subscribed-text">
        <div class="product-modal__title"><?= Loc::getMessage('PRODUCT_SUBSCRIBE_FORM_HEAD_ISSUBSCRIBED') ?>
        </div>
        <div class="want-to-buy__description"><?= Loc::getMessage('PRODUCT_SUBSCRIBE_FORM_DESCRIPTION_ISSUBSCRIBED') ?></div>
    </div>
    <div class="want-to-buy__head">
        <div class="product-modal__title"><?= Loc::getMessage('PRODUCT_SUBSCRIBE_FORM_HEAD') ?></div>
        <div class="want-to-buy__description"><?= Loc::getMessage('PRODUCT_SUBSCRIBE_FORM_DESCRIPTION') ?></div>
    </div>
    <div class="want-to-buy__fields">
        <div class="input"><input type="text" name="name" data-extended="true" required><label
                    class="input__label">Имя</label>
            <div class="input__bar"></div>
        </div>
        <div class="input" id="elementSubscribeFormWrap"><input name="1" type="email" required><label
                    class="input__label">E-mail</label>
            <div class="input__bar"></div>
        </div>
        <div class="input"><input type="text" name="phone" data-extended="true" required><label class="input__label">Телефон</label>
            <div class="input__bar"></div>
        </div>
        <div class="want-to-buy__subscribe"><label class="checkbox-circle" for="ch1"><input
                        class="checkbox-circle__input" name="subscribe_emailing_list" type="checkbox" id="ch1" data-checkbox="ch1" value="on"><span
                        class="checkbox-circle__block"><span class="checkbox-circle__switch"></span></span>
                <div class="checkbox-circle__text"><?= Loc::getMessage('PRODUCT_SUBSCRIBE_FORM_SUBSCRIBE_NEWS') ?></div>
            </label></div>
    </div>
    <div class="want-to-buy__footer">
        <button type="submit" class="button-bordered"><span><?= Loc::getMessage(
                    'PRODUCT_SUBSCRIBE_FORM_BUTTON'
                ) ?></span></button>
        <div class="t-footnote"><?= Loc::getMessage('PRODUCT_SUBSCRIBE_FORM_PERSONAL_TEXT') ?></div>
    </div>
</form>
<?php
$this->EndViewTarget();
?>
<style>
    .is-subscribed .want-to-buy__head,
    .is-subscribed .want-to-buy__fields,
    .is-subscribed .want-to-buy__footer {
        display: none;
    }
    .is-subscribed-text {
        display: none;
    }
    .is-subscribed .is-subscribed-text {
        display: block;
    }
</style>

<button class="button-bordered $class" data-modal-open="want-to-buy" id="<?= $arResult['BUTTON_ID'] ?>" <?= $display ?>>
    <span><?=$message?></span>
</button>

<?php

$jsParams = [
    'message' => [
        'error' => Loc::getMessage('PRODUCT_SUBSCRIBE_ERROR'),
        'subscribe' => Loc::getMessage('PRODUCT_SUBSCRIBE_BUTTON_NAME'),
        'subscribed' => Loc::getMessage('PRODUCT_SUBSCRIBE_BUTTON_SUBSCRIBED'),
        'alreadySubscribed' => Loc::getMessage('PRODUCT_SUBSCRIBE_ALREADY_SUBSCRIBED'),
    ],
    'formId' => 'elementSubscribeForm',
    'formWrapId' => 'elementSubscribeFormWrap',
    'formModalId' => 'want-to-buy',
    'buttonId' => $arResult['BUTTON_ID'],
    'url' => "{$component->getPath()}/ajax.php",
    'siteId' => SITE_ID,
    'productId' => intval($arResult['PRODUCT_ID']),
];
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.subscribeElement = new SubscribeElement(<?=json_encode($jsParams)?>);
    });
</script>
