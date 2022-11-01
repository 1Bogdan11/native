<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
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

?>
<div class="cities-select js-cities-select" data-function="personalLocationSearch">
    <div class="input">
        <input type="hidden"
            value="<?=$arResult['INPUT_VALUE']?>"
            name="<?=$arParams['INPUT_NAME']?>"
            class="js-cities-select__hidden <?=($arParams['IS_REQUIRED'] === 'Y' ? 'validate' : '')?>"
        />
        <input type="text" class="js-cities-select__input" value="<?=$arResult['INPUT_VALUE_DISPLAY']?>">
        <label class="input__label" for="city"><?=$arParams['PROPERTY_NAME']?><?=($arParams['IS_REQUIRED'] === 'Y' ? '*' : '')?></label>
        <div class="input__bar"></div>
    </div>
    <div class="cities-select__dropdown">
        <div class="cities-select__preloader">
            <img src="/assets/img/loader.svg" alt="">
        </div>
        <div class="cities-select__list js-cities-select__list"></div>
        <div class="cities-select__empty js-cities-select__empty">
            <?=Loc::getMessage('LOCATION_SEARCH_NOT_FOUND')?>
        </div>
    </div>
</div>

<?php
$params = [
    'lang' => LANGUAGE_ID,
    'site' => SITE_ID,
    'url' => "{$this->__component->getPath()}/get.php",
];
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.personalLocationSelector = new PersonalLocationSearch(<?=json_encode($params)?>);
        window.personalLocationSearch = function (value, callback) {
            window.personalLocationSelector.send(value, callback);
        }
    });
</script>
