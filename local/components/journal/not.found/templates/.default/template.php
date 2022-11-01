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
$iblockHelper = Its\Library\Iblock\Iblock::getInstance();
$bannerIblockId = $iblockHelper->get('banneronmain');
$catalogIblockId = $iblockHelper->get('catalog');
?>
<div class="container section__wrapper not-found">
    <div class="not-found__head">
        <h1 class="star-title" data-observe="fade-y" data-scroll>
            <svg class="i-subtract">
                <use xlink:href="#i-subtract"></use>
            </svg>
            <span><?php
                $APPLICATION->ShowTitle(false) ?></span>
        </h1>
        <div class="not-found__head_description" data-scroll data-observe="fade-y">
            <?= Loc::getMessage('404_PAGE_MESSAGE') ?>
        </div>
    </div>

    <?
    if ($bannerIblockId) { ?>
        <div class="hero">
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "bannermain-1-row",
                array(
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",    // Формат показа даты
                    "ADD_SECTIONS_CHAIN" => "N",    // Включать раздел в цепочку навигации
                    "AJAX_MODE" => "N",    // Включить режим AJAX
                    "AJAX_OPTION_ADDITIONAL" => "",    // Дополнительный идентификатор
                    "AJAX_OPTION_HISTORY" => "N",    // Включить эмуляцию навигации браузера
                    "AJAX_OPTION_JUMP" => "N",    // Включить прокрутку к началу компонента
                    "AJAX_OPTION_STYLE" => "Y",    // Включить подгрузку стилей
                    "CACHE_FILTER" => "N",    // Кешировать при установленном фильтре
                    "CACHE_GROUPS" => "Y",    // Учитывать права доступа
                    "CACHE_TIME" => "36000000",    // Время кеширования (сек.)
                    "CACHE_TYPE" => "A",    // Тип кеширования
                    "CHECK_DATES" => "Y",    // Показывать только активные на данный момент элементы
                    "DETAIL_URL" => "",    // URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
                    "DISPLAY_BOTTOM_PAGER" => "N",    // Выводить под списком
                    "DISPLAY_DATE" => "Y",    // Выводить дату элемента
                    "DISPLAY_NAME" => "Y",    // Выводить название элемента
                    "DISPLAY_PICTURE" => "Y",    // Выводить изображение для анонса
                    "DISPLAY_PREVIEW_TEXT" => "N",    // Выводить текст анонса
                    "DISPLAY_TOP_PAGER" => "N",    // Выводить над списком
                    "FIELD_CODE" => array(    // Поля
                        0 => "NAME",
                    ),
                    "FILTER_NAME" => "",    // Фильтр
                    "HIDE_LINK_WHEN_NO_DETAIL" => "N",    // Скрывать ссылку, если нет детального описания
                    "IBLOCK_ID" => $bannerIblockId,    // Код информационного блока
                    "IBLOCK_TYPE" => "banners",    // Тип информационного блока (используется только для проверки)
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",    // Включать инфоблок в цепочку навигации
                    "INCLUDE_SUBSECTIONS" => "N",    // Показывать элементы подразделов раздела
                    "MESSAGE_404" => "",    // Сообщение для показа (по умолчанию из компонента)
                    "NEWS_COUNT" => "2",    // Количество новостей на странице
                    "PAGER_BASE_LINK_ENABLE" => "N",    // Включить обработку ссылок
                    "PAGER_DESC_NUMBERING" => "N",    // Использовать обратную навигацию
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",    // Время кеширования страниц для обратной навигации
                    "PAGER_SHOW_ALL" => "N",    // Показывать ссылку "Все"
                    "PAGER_SHOW_ALWAYS" => "N",    // Выводить всегда
                    "PAGER_TEMPLATE" => ".default",    // Шаблон постраничной навигации
                    "PAGER_TITLE" => "Новости",    // Название категорий
                    "PARENT_SECTION" => "",    // ID раздела
                    "PARENT_SECTION_CODE" => "firstrow",    // Код раздела
                    "PREVIEW_TRUNCATE_LEN" => "",    // Максимальная длина анонса для вывода (только для типа текст)
                    "PROPERTY_CODE" => array(    // Свойства
                        0 => "LINK",
                        1 => "",
                    ),
                    "SET_BROWSER_TITLE" => "N",    // Устанавливать заголовок окна браузера
                    "SET_LAST_MODIFIED" => "N",    // Устанавливать в заголовках ответа время модификации страницы
                    "SET_META_DESCRIPTION" => "N",    // Устанавливать описание страницы
                    "SET_META_KEYWORDS" => "N",    // Устанавливать ключевые слова страницы
                    "SET_STATUS_404" => "N",    // Устанавливать статус 404
                    "SET_TITLE" => "N",    // Устанавливать заголовок страницы
                    "SHOW_404" => "N",    // Показ специальной страницы
                    "SORT_BY1" => "SORT",    // Поле для первой сортировки новостей
                    "SORT_BY2" => "SORT",    // Поле для второй сортировки новостей
                    "SORT_ORDER1" => "ASC",    // Направление для первой сортировки новостей
                    "SORT_ORDER2" => "ASC",    // Направление для второй сортировки новостей
                    "STRICT_SECTION_CHECK" => "N",    // Строгая проверка раздела для показа списка
                    "COMPONENT_TEMPLATE" => "bannermain-1-row"
                ),
                false
            ); ?>
        </div>
        <?
    } ?>

    <?
    if ($catalogIblockId) { ?>
        <?
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.products.viewed",
            "section",
            array(
                "ACTION_VARIABLE" => "action_cpv",
                "ADDITIONAL_PICT_PROP_1" => "-",
                "ADDITIONAL_PICT_PROP_2" => "-",
                "ADD_PROPERTIES_TO_BASKET" => "N",
                "ADD_TO_BASKET_ACTION" => "ADD",
                "BASKET_URL" => "/personal/basket.php",
                "CACHE_GROUPS" => "Y",
                "CACHE_TIME" => "3600",
                "CACHE_TYPE" => "A",
                "CONVERT_CURRENCY" => "N",
                "DEPTH" => "2",
                "DISPLAY_COMPARE" => "N",
                "ENLARGE_PRODUCT" => "STRICT",
                "HIDE_NOT_AVAILABLE" => "N",
                "HIDE_NOT_AVAILABLE_OFFERS" => "N",
                "IBLOCK_ID" => $catalogIblockId,
                "IBLOCK_MODE" => "single",
                "IBLOCK_TYPE" => "1c_catalog",
                "LABEL_PROP_1" => array(),
                "LABEL_PROP_POSITION" => "top-left",
                "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                "MESS_BTN_BUY" => "Купить",
                "MESS_BTN_DETAIL" => "Подробнее",
                "MESS_BTN_SUBSCRIBE" => "Подписаться",
                "MESS_NOT_AVAILABLE" => "Нет в наличии",
                "PAGE_ELEMENT_COUNT" => "4",
                "PARTIAL_PRODUCT_PROPERTIES" => "N",
                "PRICE_CODE" => array(
                    0 => "Цены продажи",
                ),
                "PRICE_VAT_INCLUDE" => "Y",
                "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
                "PRODUCT_ID_VARIABLE" => "id",
                "PRODUCT_PROPS_VARIABLE" => "prop",
                "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false}]",
                "PRODUCT_SUBSCRIPTION" => "Y",
                "SECTION_CODE" => "",
                "SECTION_ELEMENT_CODE" => "",
                "SECTION_ELEMENT_ID" => $GLOBALS["CATALOG_CURRENT_ELEMENT_ID"],
                "SECTION_ID" => $GLOBALS["CATALOG_CURRENT_SECTION_ID"],
                "SHOW_CLOSE_POPUP" => "N",
                "SHOW_DISCOUNT_PERCENT" => "N",
                "SHOW_FROM_SECTION" => "N",
                "SHOW_MAX_QUANTITY" => "N",
                "SHOW_OLD_PRICE" => "N",
                "SHOW_PRICE_COUNT" => "1",
                "SHOW_SLIDER" => "N",
                "SLIDER_INTERVAL" => "3000",
                "SLIDER_PROGRESS" => "N",
                "TEMPLATE_THEME" => "blue",
                "USE_ENHANCED_ECOMMERCE" => "N",
                "USE_PRICE_COUNT" => "N",
                "USE_PRODUCT_QUANTITY" => "N",
                "COMPONENT_TEMPLATE" => "lejournal"
            ),
            false
        ); ?>
        <?
    } ?>

</div>
<?php
$APPLICATION->SetTitle(Loc::getMessage('404_PAGE_TITLE')); ?>
