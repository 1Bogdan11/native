<?php

use Its\Library\Iblock\Iblock;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/404.php')) {
        require $_SERVER['DOCUMENT_ROOT'] . '/404.php';
    }
    die();
}

/** @global $APPLICATION */

$APPLICATION->AddViewContent('MAIN_ATTRIBUTE', 'class="is-horizontal no-padding" id="js-scroll" data-direction="horizontal"');
?>
<div class="about">
    <div class="about__base">

        <div class="about__hero about__section">
            <div class="breadcrumbs media-max--tab breadcrumbs--absolute breadcrumbs--full" data-scroll data-observe="fade-y">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:breadcrumb',
                    '',
                    [
                        'PATH' => '',
                        'SITE_ID' => SITE_ID,
                        'START_FROM' => '0',
                    ],
                    false,
                    ['HIDE_ICONS' => 'Y']
                )?>
            </div>
            <div class="about__hero-content">
                <div class="about__hero-text" data-scroll>
                    <h1 class="about__hero-title">О нас</h1>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:main.include',
                        '',
                        [
                            'AREA_FILE_SHOW' => 'file',
                            'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/about_intro.php',
                        ],
                        true
                    )?>
                    <p class="about__hero-quote">Le Journal Intime</p>
                </div>
            </div>
            <div class="about__hero-image about__poster" data-scroll>
                <div class="about__hero-image_inner">
                    <img src="/assets/img/about/about-1.jpg" data-scroll data-scroll-speed="-1" alt="">
                </div>
            </div>
        </div>

        <div class="about__first about__content">
            <div class="about__first-content">
                <div class="about__first-content_image">
                    <img src="/assets/img/about/about-2.jpg" data-scroll data-scroll-speed="-1" alt="">
                </div>
                <div class="about__content_title">Создатели</div>
                <div class="about__content_description">
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:main.include',
                        '',
                        [
                            'AREA_FILE_SHOW' => 'file',
                            'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/about_vendor.php',
                        ],
                        true
                    )?>
                </div>
                <div class="about__content_description about__content_description--right">
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:main.include',
                        '',
                        [
                            'AREA_FILE_SHOW' => 'file',
                            'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/about_vendor_2.php',
                        ],
                        true
                    )?>
                </div>
            </div>
            <div class="about__first-images">
                <div class="about__first-images_part">
                    <img src="/assets/img/about/about-3.jpg" data-scroll data-scroll-speed="-0.5" alt="">
                    <img src="/assets/img/about/about-4.jpg" data-scroll data-scroll-speed="-1" alt="">
                </div>
                <div class="about__first-images_part">
                    <div class="about__first-images_single">
                        <img src="/assets/img/about/about-5.jpg" data-scroll data-scroll-speed="-1" alt="">
                    </div>
                </div>
            </div>
        </div>

        <div class="about__poster">
            <img src="/assets/img/about/about-6.jpg" data-scroll data-scroll-speed="-1" alt="">
        </div>

        <?php $APPLICATION->IncludeComponent(
            'bitrix:news.list',
            'about_benefits',
            [
                'IBLOCK_TYPE' => 'content',
                'IBLOCK_ID' => Iblock::getInstance()->get('about_benefits'),
                'NEWS_COUNT' => '100',
                'SORT_BY1' => 'SORT',
                'SORT_ORDER1' => 'ASC',
                'SORT_BY2' => 'ID',
                'SORT_ORDER2' => 'DESC',
                'FILTER_NAME' => '',
                'FIELD_CODE' => ['DETAIL_PICTURE'],
                'PROPERTY_CODE' => ['FAKE'],
                'CHECK_DATES' => 'Y',
                'DETAIL_URL' => '',
                'AJAX_MODE' => 'N',
                'CACHE_TYPE' => 'A',
                'CACHE_TIME' => '36000000',
                'CACHE_FILTER' => 'N',
                'CACHE_GROUPS' => 'Y',
                'PREVIEW_TRUNCATE_LEN' => '',
                'ACTIVE_DATE_FORMAT' => 'd.m.Y',
                'SET_TITLE' => 'N',
                'SET_BROWSER_TITLE' => 'N',
                'SET_META_KEYWORDS' => 'N',
                'SET_META_DESCRIPTION' => 'N',
                'SET_LAST_MODIFIED' => 'N',
                'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
                'ADD_SECTIONS_CHAIN' => 'N',
                'INCLUDE_SUBSECTIONS' => 'Y',
                'STRICT_SECTION_CHECK' => 'N',
                'PAGER_TEMPLATE' => '.default',
                'DISPLAY_TOP_PAGER' => 'N',
                'DISPLAY_BOTTOM_PAGER' => 'N',
                'PAGER_DESC_NUMBERING' => 'N',
                'SET_STATUS_404' => 'N',
            ],
            false,
            ['HIDE_ICONS' => 'Y']
        )?>

        <div class="about__poster media-max--tab">
            <img src="/assets/img/hero/hero-image-1.jpg" data-scroll data-scroll-speed="-1" alt="">
        </div>

        <div class="about__second">
            <div class="about__second-content about__content">
                <div class="about__second-inner">
                    <div class="about__second-part">
                        <div class="about__content_title">Производство</div>
                        <div class="about__content_description">
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:main.include',
                                '',
                                [
                                    'AREA_FILE_SHOW' => 'file',
                                    'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/about_media.php',
                                ],
                                true
                            )?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="about__lines">
                <div class="about__lines-part js-booster-mobile-x">
                    <ul class="about__line js-booster__inner" data-scroll data-scroll-speed="1" data-scroll-direction="vertical">
                        <li><img src="/assets/img/about/about-line-1.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-3.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-5.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-1.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-3.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-5.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-1.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-3.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-5.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-1.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-3.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-5.jpg" alt=""></li>
                    </ul>
                </div>
                <div class="about__lines-part js-booster-mobile-x" data-booster-start="100">
                    <ul class="about__line js-booster__inner" data-scroll data-scroll-speed="-1" data-scroll-direction="vertical">
                        <li><img src="/assets/img/about/about-line-1.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-2.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-4.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-3.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-2.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-4.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-1.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-5.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-2.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-4.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-5.jpg" alt=""></li>
                        <li><img src="/assets/img/about/about-line-4.jpg" alt=""></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="about__poster">
            <img src="/assets/img/about/about-7.jpg" data-scroll data-scroll-speed="-1" alt="">
        </div>

        <div class="about__third about__content">
            <div class="about__third-content">
                <div class="about__third-content_text">
                    <div class="about__content_title">Коллаборации</div>
                    <div class="about__content_description">
                        <?php $APPLICATION->IncludeComponent(
                            'bitrix:main.include',
                            '',
                            [
                                'AREA_FILE_SHOW' => 'file',
                                'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/about_friends.php',
                            ],
                            true
                        )?>
                    </div>
                </div>
                <div class="about__third-image" data-scroll>
                    <img src="/assets/img/about/about-8.jpg" data-scroll data-scroll-speed="-1" alt="">
                </div>
            </div>
            <div class="about__third-gallery">
                <div class="about__third-gallery_item">
                    <img src="/assets/img/about/about-gallery-1.jpg" data-scroll data-scroll-speed="1.8" alt="">
                </div>
                <div class="about__third-gallery_item">
                    <img src="/assets/img/about/about-gallery-2.jpg" data-scroll data-scroll-speed="0.9" alt="">
                </div>
                <div class="about__third-gallery_item">
                    <img src="/assets/img/about/about-gallery-3.jpg" data-scroll data-scroll-speed="0.6" alt="">
                </div>
                <div class="about__third-gallery_item">
                    <img src="/assets/img/about/about-gallery-4.jpg" data-scroll data-scroll-speed="1.5" alt="">
                </div>
            </div>
        </div>

        <div class="about__full">
            <img src="/assets/img/about/about-9.jpg" data-scroll data-scroll-speed="-2" alt="">
            <a class="is-light logo" href="<?=SITE_DIR?>">
                <svg class="i-logo"><use xlink:href="#i-logo"></use></svg>
            </a>
        </div>

        <div class="about__marque" data-scroll data-scroll-speed="-1" data-scroll-direction="vertical">
            <ul class="about__marque-part">
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
            </ul>
            <ul class="about__marque-part">
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
                <li>le journal intime </li>
            </ul>
            <ul class="about__marque-part">
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
                <li>Избранные коллекции </li>
            </ul>
        </div>

        <?php $APPLICATION->IncludeComponent(
            'bitrix:news.list',
            'about_collections',
            [
                'IBLOCK_TYPE' => 'content',
                'IBLOCK_ID' => Iblock::getInstance()->get('about_collections'),
                'NEWS_COUNT' => '100',
                'SORT_BY1' => 'SORT',
                'SORT_ORDER1' => 'ASC',
                'SORT_BY2' => 'ID',
                'SORT_ORDER2' => 'DESC',
                'FILTER_NAME' => '',
                'FIELD_CODE' => ['DETAIL_PICTURE'],
                'PROPERTY_CODE' => ['FAKE'],
                'CHECK_DATES' => 'Y',
                'DETAIL_URL' => '',
                'AJAX_MODE' => 'N',
                'CACHE_TYPE' => 'A',
                'CACHE_TIME' => '36000000',
                'CACHE_FILTER' => 'N',
                'CACHE_GROUPS' => 'Y',
                'PREVIEW_TRUNCATE_LEN' => '',
                'ACTIVE_DATE_FORMAT' => 'd.m.Y',
                'SET_TITLE' => 'N',
                'SET_BROWSER_TITLE' => 'N',
                'SET_META_KEYWORDS' => 'N',
                'SET_META_DESCRIPTION' => 'N',
                'SET_LAST_MODIFIED' => 'N',
                'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
                'ADD_SECTIONS_CHAIN' => 'N',
                'INCLUDE_SUBSECTIONS' => 'Y',
                'STRICT_SECTION_CHECK' => 'N',
                'PAGER_TEMPLATE' => '.default',
                'DISPLAY_TOP_PAGER' => 'N',
                'DISPLAY_BOTTOM_PAGER' => 'N',
                'PAGER_DESC_NUMBERING' => 'N',
                'SET_STATUS_404' => 'N',
            ],
            false,
            ['HIDE_ICONS' => 'Y']
        )?>

    </div>
</div>

