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

$this->setFrameMode(false);

$arCurrentSite = $arResult['SITES'][$arResult['SITE_SELECTED']];
?>
<div class="currency" data-modal-open="lang-modal">
    <div class="currency__locale"><?=strtoupper($arCurrentSite['LANGUAGE_ID'])?></div>
    <?php
    global $USER;
    if (!defined('EN_SITE_TEMPLATE') || $USER->IsAdmin()) {
        ?>
        <div class="currency__symbol"><?=$arCurrentSite['CURRENCY_SYMBOL']?></div>
        <?php
    }
    ?>
</div>

<?php
$this->SetViewTarget('MODAL_SITE_SELECTOR');
?>
    <section class="modal modal--lang-modal modal--center modal--bordered" data-modal="lang-modal" data-tabs-modal>
        <button class="modal__overlay" type="button" data-modal-close="lang-modal">
            <button class="modal__mobile-close"></button>
        </button>
        <div class="modal__container">
            <div class="modal__content">
                <div class="lang-modal" id="jsSiteSelector">
                    <p class="lang-modal__title">
                        <?=Loc::getMessage('SITE_SELECTOR_MODAL_TITLE')?>
                    </p>
                    <p class="lang-modal__selects">
                        <select class="select js-select js-incomes-select select--filled lang-modal__select" data-scroll data-observe="fade-y">
                            <?php
                            foreach ($arResult['SITES'] as $index => $arSite) {
                                ?>
                                <option value="<?=$arSite['ID']?>"
                                    <?=($index === $arResult['SITE_SELECTED'] ? 'selected' : '')?>
                                    data-path="<?=$arSite['PATH']?>">
                                    <?=$arSite['LANGUAGE_NAME']?> <?=$arSite['CURRENCY_SYMBOL']?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </p>
                    <button class="btn btn--underline lang-modal__button" type="button">
                        <?=Loc::getMessage('SITE_SELECTOR_MODAL_ACTION')?>
                    </button>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let selector, select;
                selector = document.getElementById('jsSiteSelector');
                select = selector.querySelector('select');
                selector.querySelector('button').addEventListener('click', () => {
                    document.location.href = select.options[select.selectedIndex].dataset.path;
                });
            });
        </script>
    </section>
<?php
$this->EndViewTarget();
