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

$this->setFrameMode(true);
$request = Context::getCurrent()->getRequest();

?>
<div class="modal__content">
    <form class="filters" id="<?=$arResult['FILTER_NAME']?>_form" action="<?=$arResult['FORM_ACTION']?>">
        <div class="filters-body">
            <div class="filters-body__inner">
                <?php
                if (is_array($arParams['CATALOG_ORDER'])) {
                    ?>
                    <div class="accordion js-toggle media-min--tab" data-filter="single">
                        <div class="accordion__head js-toggle__btn">
                            <div class="accordion__title">
                                <span>
                                    <?=Loc::getMessage('CATALOG_SECTION_FILTER_ORDER')?>
                                </span>
                                <div class="filter-status js-filter__status">
                                    <span class="filter-status__counter"></span>
                                    <div class="filter-status__close"></div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion__content">
                            <ul class="filters-list">
                                <?php foreach ($arParams['CATALOG_ORDER'] as $key => $arOrderType) :?>
                                    <div class="filters-list__item">
                                        <input type="radio"
                                            name="order"
                                            id="order_<?=$key?>"
                                            class="jsCatalogSectionOrder"
                                            data-link="<?=$arOrderType['link']?>"
                                            value="<?=$arOrderType['name']?>"
                                            <?=($arOrderType['selected'] ? 'checked' : null)?>
                                        />
                                        <label for="order_<?=$key?>"><?=$arOrderType['name']?></label>
                                    </div>
                                <?php endforeach?>
                            </ul>
                        </div>
                    </div>
                    <?php
                }

                foreach ($arResult['HIDDEN'] as $arItem) {
                    ?>
                    <input class="jsFilterItem" type="hidden" name="<?=$arItem['CONTROL_NAME']?>"
                           id="<?=$arItem['CONTROL_ID']?>" value="<?=$arItem['HTML_VALUE']?>">
                    <?php
                }

                if ($request->get('q') !== null) {
                    ?>
                    <input class="jsFilterItem" type="hidden" name="q" value="<?=htmlspecialchars($request->get('q'))?>">
                    <?php
}

                usort($arResult['ITEMS'], function ($a, $b) {
                    if ($a['PRICE'] === $b['PRICE']) {
                        return 0;
                    }
                    return ($a['PRICE'] === true) ? -1 : 1;
                });

                $first = $arParams['IGNORE_LEFT_SELECTOR'] !== 'Y';
                foreach ($arResult['ITEMS'] as $arItem) {
                    if (
                        is_array($arParams['HIDE_FILTER_PROPERTIES'])
                        && in_array($arItem['CODE'], $arParams['HIDE_FILTER_PROPERTIES'])
                    ) {
                        continue;
                    }

                    if ($arItem['PRICE'] === true) {
                        $arItem['DISPLAY_TYPE'] = 'A';
                        continue;
                    }

                    if (empty($arItem['VALUES'])) {
                        continue;
                    }

                    if ($arItem['DISPLAY_TYPE'] == 'A' && ($arItem['VALUES']['MAX']['VALUE'] - $arItem['VALUES']['MIN']['VALUE'] <= 0)) {
                        continue;
                    }

                    // Calendar date
                    if ($arItem['DISPLAY_TYPE'] == 'U') {
                        continue;
                    }

                    switch ($arItem['DISPLAY_TYPE']) {
                        case 'A': // Number with range slider (or price)
                        case 'B': // Number inputs
                            $arMin = $arItem['VALUES']['MIN'];
                            $arMax = $arItem['VALUES']['MAX'];
                            ?>
                            <div class="accordion js-toggle" data-filter="single">
                                <div class="accordion__head js-toggle__btn">
                                    <div class="accordion__title">
                                        <span>
                                            <?=$arItem['NAME']?>
                                        </span>
                                        <div class="filter-status js-filter__status">
                                            <span class="filter-status__counter"></span>
                                            <div class="filter-status__close"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion__content">
                                    <div class="range js-range"
                                        data-min="<?=$arMin['VALUE']?>"
                                        data-max="<?=$arMax['VALUE']?>"
                                        data-from="<?=$arMin['HTML_VALUE']?>"
                                        data-to="<?=$arMax['HTML_VALUE']?>">
                                        <div class="range__fields">
                                            <div class="range__field js-range-from">0 ₽</div>
                                            <div class="range__field js-range-to">0 ₽</div>
                                        </div>
                                        <div class="range__slider js-range-slider"></div>
                                        <input type="hidden"
                                            class="jsFilterItem js-range-from-input"
                                            name="<?=$arMin['CONTROL_NAME']?>"
                                            value="<?=$arMin['HTML_VALUE']?>"
                                            id="<?=$arMin['CONTROL_ID']?>"
                                            onchange="smartFilter.change(this)"
                                            data-min
                                        />
                                        <input type="hidden"
                                            class="jsFilterItem js-range-to-input"
                                            name="<?=$arMax['CONTROL_NAME']?>"
                                            value="<?=$arMax['HTML_VALUE']?>"
                                            id="<?=$arMax['CONTROL_ID']?>"
                                            onchange="smartFilter.change(this)"
                                            data-max
                                        />
                                    </div>
                                </div>
                            </div>
                            <?php
                            break;

                        case 'G': // Checkboxes with picture
                        case 'H': // Checkboxes with picture and label
                            ?>
                            <div class="accordion js-toggle is-active" data-filter="multiply">
                                <div class="accordion__head js-toggle__btn">
                                    <div class="accordion__title">
                                        <span>
                                            <?=$arItem['NAME']?>
                                        </span>
                                        <div class="filter-status js-filter__status">
                                            <span class="filter-status__counter"></span>
                                            <div class="filter-status__close"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion__content">
                                    <div class="color-circles">
                                        <div class="color-circles">
                                            <?php
                                            foreach ($arItem['VALUES'] as $arValue) {
                                                ?>
                                                <div class="color-circles__item">
                                                    <input type="checkbox"
                                                        id="<?=$arValue['CONTROL_ID']?>"
                                                        value="<?=$arValue['HTML_VALUE']?>"
                                                        class="jsFilterItem <?=($arValue['DISABLED'] ? 'disabled' : null)?>"
                                                        onchange="smartFilter.change(this)"
                                                        name="<?=$arValue['CONTROL_NAME']?>"
                                                        <?=($arValue['CHECKED'] ? 'checked' : null)?>
                                                    />
                                                    <label for="<?=$arValue['CONTROL_ID']?>"></label>
                                                    <div class="color-circles__radio" style="background-image: url(<?=htmlspecialchars($arValue['FILE']['SRC'])?>)"></div>
                                                    <div class="color-circles__radio_text">
                                                        <div class="color-circles__radio_title">
                                                            <?=$arValue['VALUE']?>
                                                        </div>
                                                        
                                                        <?php if ($arParams['DISPLAY_ELEMENT_COUNT'] !== 'N' && isset($arValue['ELEMENT_COUNT'])) :?>
                                                            <span class="quantity" id="<?=$arValue['CONTROL_ID']?>_count">(<?=$arValue['ELEMENT_COUNT']?>)</span>
                                                        <?php endif?>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            break;

                        case 'P': // Select (radio drop)
                        case 'R': // Select (radio drop) with picture and label
                        case 'K': // Radio
                            break;

                        default: // Checkboxes
                            ?>
                            <div class="accordion js-toggle" data-filter="multiply">
                                <div class="accordion__head js-toggle__btn">
                                    <div class="accordion__title">
                                        <span><?=$arItem['NAME']?></span>
                                        <div class="filter-status js-filter__status">
                                            <span class="filter-status__counter"></span>
                                            <div class="filter-status__close"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion__content">
                                    <ul class="sizes sizes--bordered">
                                        <?php
                                        foreach ($arItem['VALUES'] as $arValue) {
                                            ?>
                                            <div class="sizes__item">
                                                <input type="checkbox"
                                                    id="<?=$arValue['CONTROL_ID']?>"
                                                    value="<?=$arValue['HTML_VALUE']?>"
                                                    class="jsFilterItem <?=($arValue['DISABLED'] ? 'disabled' : null)?>"
                                                    onchange="smartFilter.change(this)"
                                                    name="<?=$arValue['CONTROL_NAME']?>"
                                                    <?=($arValue['CHECKED'] ? 'checked' : null)?>
                                                />
                                                <label for="<?=$arValue['CONTROL_ID']?>">
                                                    <span>
                                                        <?=$arValue['VALUE']?>
                                                        <?php if ($arParams['DISPLAY_ELEMENT_COUNT'] !== 'N' && isset($arValue['ELEMENT_COUNT'])) :?>
                                                            <span class="quantity" id="<?=$arValue['CONTROL_ID']?>_count">(<?=$arValue['ELEMENT_COUNT']?>)</span>
                                                        <?php endif?>
                                                    </span>
                                                </label>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </ul>

                                    <?php
                                    if ($arItem['CODE'] == 'SIZE' || $arItem['CODE'] == 'RAZMER') {
                                        ?>
                                        <a href="/support/tablitsa-razmerov/" target="_blank" class="button-grey">
                                            <?=Loc::getMessage('CATALOG_SECTION_FILTER_SIZE_MODAL_LINK')?>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                            break;
                    }
                    $first = false;
                }
                ?>
            </div>
            <div class="filter-body__footer">
                <div class="filters__count media-max--tab js-filters-count jsSmartFilterCount">
                    <?=Loc::getMessage('CATALOG_SECTION_FILTER_COUNT')?>
                </div>
                <button class="t-ttu js-filter-reset" id="jsSmartFilterResetButton">
                    <?=Loc::getMessage('CATALOG_SECTION_FILTER_RESET')?>
                </button>
                <button class="t-ttu filter__set" id="jsSmartFilterApplyButton">
                    <div class="filter-status media-min--tab js-filters-count__mobile"></div>
                    <span>
                        <?=Loc::getMessage('CATALOG_SECTION_FILTER_APPLY')?>
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>

<?php

if (is_array($arResult['JS_FILTER_PARAMS'])) {
    $arResult['JS_FILTER_PARAMS'] = array_merge($arResult['JS_FILTER_PARAMS'], ['selector' => '.jsFilterItem', 'applyButton' => 'jsSmartFilterApplyButton']);
} else {
    $arResult['JS_FILTER_PARAMS'] = ['selector' => '.jsFilterItem', 'applyButton' => 'jsSmartFilterApplyButton'];
}

if ($request->get('q') !== null) {
    $arResult['JS_FILTER_PARAMS']['searchQuery'] = htmlspecialchars($request->get('q'));
}

?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('jsSmartFilterApplyButton').addEventListener('click', function (event) {
            event.preventDefault();
            window.lastAction = 'apply';
            let url = this.getAttribute('data-url');
            <?php
            if ($request->get('q') !== null) {
                ?>
                url = Tool.setParamToLink(url, 'q', <?=json_encode(htmlspecialchars($request->get('q')))?>);
                <?php
            }
            ?>
            if (!this.hasAttribute('disabled') && !!url) {
                history.pushState('', '', url);
                document.dispatchEvent(new CustomEvent('custom_section_filter_apply', {
                    detail: {url: url}
                }));
                document.dispatchEvent(new CustomEvent('modal:close', {detail: {name: 'filters'}}));
            }
        });

        document.getElementById('jsSmartFilterResetButton').addEventListener('click', function (event) {
            window.lastAction = 'reset';
        });

        smartFilter = new CatalogSmartFilter(
            <?=json_encode($arResult['FORM_ACTION'])?>,
            <?=json_encode("{$arResult['FILTER_NAME']}_form")?>,
            <?=json_encode($arResult['JS_FILTER_PARAMS'])?>,
            function (url, result) {
                <?php
                if ($request->get('q') !== null) {
                    ?>
                    url = Tool.setParamToLink(url, 'q', <?=json_encode(htmlspecialchars($request->get('q')))?>);
                    <?php
                }
                ?>
                if (window.lastAction === 'reset') {
                    window.lastAction = '';
                    history.pushState('', '', url);
                    document.dispatchEvent(new CustomEvent('custom_section_filter_apply', {
                        detail: {url: url, result: result}
                    }));
                    document.dispatchEvent(new CustomEvent('modal:close', {detail: {name: 'filters'}}));
                }
                document.dispatchEvent(new CustomEvent('filters:count', {detail: {
                    count: result.ELEMENT_COUNT,
                    show: window.lastAction !== 'reset',
                }}));
            }
        );
    });
</script>
