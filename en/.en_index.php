<?php

use Its\Library\Iblock\Iblock;
use Its\Library\Image\Resize;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/404.php')) {
        require $_SERVER['DOCUMENT_ROOT'] . '/404.php';
    }
    die();
}
$iblockHelper = Iblock::getInstance();
$bannerIblockId = $iblockHelper->get('banneronmain');
/** @global $APPLICATION */
$APPLICATION->AddViewContent('MAIN_ATTRIBUTE', 'id="js-scroll" data-direction="vertical"');
?>
<div class="container section__wrapper">
    <div class="hero">
        <h1 class="star-title" data-observe="fade-y" data-scroll>
            <svg class="i-subtract">
                <use xlink:href="#i-subtract"></use>
            </svg>
            <span>
            <?php $APPLICATION->IncludeComponent(
                'bitrix:main.include',
                'html',
                [
                    'AREA_FILE_SHOW' => 'file',
                    'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/main_h1.php',
                ],
                true
            )?>
            </span>
        </h1>
        <?php
        if ($bannerIblockId) {
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
            );
        }
        ?>
    </div>
    <section class="discount-section">
        <?
        if ($bannerIblockId) { ?>
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "bannermain-2-row",
                array(
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",    // Формат показа даты
                    "ADD_SECTIONS_CHAIN" => "N",    // Включать раздел в цепочку навигации
                    "AJAX_MODE" => "N",    // Включить режим AJAX
                    "AJAX_OPTION_ADDITIONAL" => "",    // Дополнительный идентификатор
                    "AJAX_OPTION_HISTORY" => "N",    // Включить эмуляцию навигации браузера
                    "AJAX_OPTION_JUMP" => "N",    // Включить прокрутку к началу компонента
                    "AJAX_OPTION_STYLE" => "N",    // Включить подгрузку стилей
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
                        1 => "",
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
                    "PARENT_SECTION_CODE" => "secondrow",    // Код раздела
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
                    "COMPONENT_TEMPLATE" => "bannermain-2-row"
                ),
                false
            ); ?>
        <?
        } ?>
        <div class="discount-section__part" data-scroll data-scroll-speed="3">
            <?php $APPLICATION->IncludeComponent(
                'journal:subscribe.form',
                'main',
                [
                    'FORM_NAME' => 'subscribe_main_form',
                ]
            )?>
        </div>
    </section>
    <section class="circles-section u-flex-col">
        <div class="circles-section__head u-flex-col">
            <div class="t-h4 t-tac">Белье в стиле Smart & Sexy</div>
            <p>Технологичное белье, в котором Вам не придется выбирать между комфортом и сексуальностью</p>
        </div>
        <?
        if ($bannerIblockId) { ?>
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "bannermain-3-row",
                array(
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",    // Формат показа даты
                    "ADD_SECTIONS_CHAIN" => "N",    // Включать раздел в цепочку навигации
                    "AJAX_MODE" => "N",    // Включить режим AJAX
                    "AJAX_OPTION_ADDITIONAL" => "",    // Дополнительный идентификатор
                    "AJAX_OPTION_HISTORY" => "N",    // Включить эмуляцию навигации браузера
                    "AJAX_OPTION_JUMP" => "N",    // Включить прокрутку к началу компонента
                    "AJAX_OPTION_STYLE" => "N",    // Включить подгрузку стилей
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
                        1 => "",
                    ),
                    "FILTER_NAME" => "",    // Фильтр
                    "HIDE_LINK_WHEN_NO_DETAIL" => "N",    // Скрывать ссылку, если нет детального описания
                    "IBLOCK_ID" => $bannerIblockId,    // Код информационного блока
                    "IBLOCK_TYPE" => "banners",    // Тип информационного блока (используется только для проверки)
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",    // Включать инфоблок в цепочку навигации
                    "INCLUDE_SUBSECTIONS" => "N",    // Показывать элементы подразделов раздела
                    "MESSAGE_404" => "",    // Сообщение для показа (по умолчанию из компонента)
                    "NEWS_COUNT" => "6",    // Количество новостей на странице
                    "PAGER_BASE_LINK_ENABLE" => "N",    // Включить обработку ссылок
                    "PAGER_DESC_NUMBERING" => "N",    // Использовать обратную навигацию
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",    // Время кеширования страниц для обратной навигации
                    "PAGER_SHOW_ALL" => "N",    // Показывать ссылку "Все"
                    "PAGER_SHOW_ALWAYS" => "N",    // Выводить всегда
                    "PAGER_TEMPLATE" => ".default",    // Шаблон постраничной навигации
                    "PAGER_TITLE" => "Новости",    // Название категорий
                    "PARENT_SECTION" => "",    // ID раздела
                    "PARENT_SECTION_CODE" => "thirdrow",    // Код раздела
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
                    "COMPONENT_TEMPLATE" => ".default"
                ),
                false
            ); ?>
        <?
        } ?>
    </section>
    <?php $APPLICATION->IncludeComponent(
        'bitrix:main.include',
        '',
        [
            'AREA_FILE_SHOW' => 'file',
            'PATH' => SITE_TEMPLATE_PATH . '/block/main_products.php',
        ],
        true
    )?>

    <section class="showroom-section u-flex-col u-aic u-jcsb" data-header-mode="dark">
        <?php
        $imageId = intval(file_get_contents($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/main_head_shop_image.php'));
        $resize = new Resize($imageId, [2800, 1200]);
        ?>
        <div class="showroom-section__background" style="background-image:url(<?=$resize->getResult()['src']?>)" data-scroll data-scroll-speed="-1"></div>
        <a class="is-light logo" href="<?=SITE_DIR?>">
            <svg class="i-logo"><use xlink:href="#i-logo"></use></svg>
        </a>
        <div class="showroom-section__content u-flex-col">
            <div class="t-h2">Флагманский магазин</div>
            <div class="t-ttu">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:main.include',
                    '',
                    [
                        'AREA_FILE_SHOW' => 'file',
                        'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/main_head_shop_address.php',
                    ],
                    true
                )?>
            </div>
        </div>
        <a class="showroom-section__link link__underline" href="<?=SITE_DIR?>shops/">Наши магазины</a>
    </section>

    <?php $APPLICATION->IncludeComponent(
        'bitrix:main.include',
        '',
        [
            'AREA_FILE_SHOW' => 'file',
            'PATH' => SITE_TEMPLATE_PATH . '/block/main_articles.php',
        ],
        true
    )?>
</div>
