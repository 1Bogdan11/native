<?php

use Bitrix\Main\Localization\Loc;
use Its\Library\Asset\AssetManager;
use Journal\OpenGraph;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$asset = AssetManager::getInstance();
$asset->showInnerAsset(\CSite::InGroup([1])); // 1 - Администраторы

global $USER, $APPLICATION;
Loc::loadMessages(__FILE__);

$asset->addCss('/assets/css/main.css');

$asset->addJs('/assets/js/vendors.js')->defer();
$asset->addJs('/assets/js/main.js')->defer();

$asset->addJs(SITE_TEMPLATE_PATH . '/tool.js')->defer();
$asset->addJs(SITE_TEMPLATE_PATH . '/site.js')->defer();
?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <title><?php
        $APPLICATION->ShowTitle() ?></title>
    <?php
    $asset->showHead();
    OpenGraph::showMeta();
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.SiteManager = new Site({
                siteId: '<?=SITE_ID?>',
                sessId: '<?=bitrix_sessid()?>',
                goToFavoriteHeadMessage: <?=json_encode(Loc::getMessage('GO_TO_FAVORITE_POPUP_HEAD'))?>,
                goToFavoriteTextMessage: <?=json_encode(Loc::getMessage('GO_TO_FAVORITE_POPUP_TEXT'))?>,
                goToFavoriteButtonMessage: <?=json_encode(Loc::getMessage('GO_TO_FAVORITE_POPUP_BUTTON'))?>,
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.25.0/axios.min.js"></script>

    <link rel="apple-touch-icon" sizes="180x180" href="<?=SITE_TEMPLATE_PATH?>/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?=SITE_TEMPLATE_PATH?>/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=SITE_TEMPLATE_PATH?>/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?=SITE_TEMPLATE_PATH?>/favicon/site.webmanifest">
    <link rel="mask-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon/safari-pinned-tab.svg" color="#000000">
    <meta name="msapplication-TileColor" content="#000000">
    <meta name="theme-color" content="#ffffff">

    <meta name="google-site-verification" content="9s-a0sQS6BGyZUbGakkbbu4yoOU759VrCvEQBn1HMhs">

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)}; m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)}) (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
        ym(45257511, "init", { clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, ecommerce:"dataLayer" });
    </script>
    <noscript>
        <div>
            <img src="https://mc.yandex.ru/watch/45257511" style="position:absolute; left:-9999px;" alt="" />
        </div>
    </noscript>
    <!-- /Yandex.Metrika counter -->

    <!-- Google Tag Manager -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KR7PBD7QYC"></script>
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-N7QGXM9');

        window.dataLayer = window.dataLayer || [];
        dataLayer.push(['js', new Date()]);
        dataLayer.push(['config', 'G-KR7PBD7QYC']);
        dataLayer.push({ecommerce: true});
        function ga4(event, parameters) {
            console.log('GA4:', `"${event}"`, parameters);
            dataLayer.push({event: event, ecommerce: parameters});
        }
    </script>
    <!-- End Google Tag Manager -->

    <!-- VK pixel -->
    <script type="text/javascript">
        !function(){var t=document.createElement("script");t.type="text/javascript",t.async=!0,t.src='https://vk.com/js/api/openapi.js?169',t.onload=function(){VK.Retargeting.Init("VK-RTRG-1512164-fZG3B"),VK.Retargeting.Hit()},document.head.appendChild(t)}();
    </script>
    <!-- End VK pixel -->
</head>
<body style="visibility: hidden;">
<?php
$showPreloader = !$_SESSION['PRELOADER_SHOWED'];
if ($showPreloader) {
    ?>
    <div class="page-preloader js-page-preloader is-active">
    </div>
    <script>
        window.addEventListener('load', function () {
            const headerPreload = document.querySelector(".js-header-preloader");
            setTimeout(() => {
                document.querySelector(".js-page-preloader").classList.remove("is-active")
                headerPreload.classList.remove("is-preloader")
            }, 3000)
            setTimeout(() => {
                headerPreload.classList.remove("is-preloader-active")
            }, 4700)
        });
    </script>
    <?php
    $_SESSION['PRELOADER_SHOWED'] = true;
}

$APPLICATION->ShowPanel();
require __DIR__ . '/block/modals.php';
?>

<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N7QGXM9" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- VK pixel (noscript) -->
<noscript>
    <img src="https://vk.com/rtrg?p=VK-RTRG-1512164-fZG3B" style="position:fixed; left:-999px;" alt=""/>
</noscript>
<!-- End VK pixel (noscript) -->

<?php
/* Заглушка для EN сайта */
global $USER;
if (substr($_SERVER['REQUEST_URI'], 0, 4) === '/en/' && $_SERVER['REQUEST_URI'] !== '/en/' && !$USER->IsAdmin()) {
    LocalRedirect('/en/', false, '302 Found');
}
if (defined('EN_SITE_TEMPLATE') && !$USER->IsAdmin()) {
    return;
}
?>

<header class="header is-active js-header js-header-preloader <?=($showPreloader ? 'is-preloader is-preloader-active' : '')?> <?php $APPLICATION->ShowViewContent('HEADER_CLASSES')?>">
    <div class="container header__inner">
        <div class="header__left">
            <button class="burger media-min--tab" data-menu data-state="default" data-mobile-button="menu">
                <span></span>
            </button>
            <?php $APPLICATION->ShowViewContent('HEADER_LEFT_ADD_CONTENT')?>
            <ul class="menu" <?php $APPLICATION->ShowViewContent('HEADER_LEFT_MENU_ATTRIBUTE')?>>
                <li>
                    <button class="t-link" data-modal-open="aside-modal" data-modal-tab="catalog">
                        <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_CATALOG')?>
                    </button>
                </li>
                <li>
                    <button class="t-link" data-modal-open="aside-modal" data-modal-tab="collections">
                        <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_COLLECTIONS')?>
                    </button>
                </li>
                <li>
                    <a class="t-link" href="<?= SITE_DIR ?>about/">
                        <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_ABOUT')?>
                    </a>
                </li>
                <li>
                    <a class="t-link" href="<?= SITE_DIR ?>blog/">
                        <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_BLOG')?>
                    </a>
                </li>
                <li>
                    <button class="t-link" data-modal-open="aside-modal" data-modal-tab="shops">
                        <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_STORES')?>
                    </button>
                </li>
            </ul>
            <button class="t-button t-button--search" data-modal-open="search" <?php $APPLICATION->ShowViewContent('HEADER_LEFT_MENU_ATTRIBUTE')?>>
                <svg class="i-search"><use xlink:href="#i-search"></use></svg>
            </button>
        </div>
        <div class="header__center">
            <?php
            if ($APPLICATION->GetCurPage(false) !== SITE_DIR) {
                ?>
                <a class="is-dark logo" href="<?= SITE_DIR ?>">
                    <div class="logo__inner">
                        <div class="logo__part">
                            <svg class="i-logo"><use xlink:href="#i-logo"></use></svg>
                        </div>
                        <div class="logo__part">
                            <svg class="i-logo"><use xlink:href="#i-logo"></use></svg>
                        </div>
                    </div>
                </a>
                <?php
            } else {
                ?>
                <span class="is-dark logo">
                    <div class="logo__inner">
                        <div class="logo__part">
                            <svg class="i-logo"><use xlink:href="#i-logo"></use></svg>
                        </div>
                        <div class="logo__part">
                            <svg class="i-logo"><use xlink:href="#i-logo"></use></svg>
                        </div>
                    </div>
                </span>
                <?php
            }
            ?>
        </div>
        <div class="header__right">
            <div class="header__contacts">
                <?php
                $APPLICATION->IncludeComponent(
                    'bitrix:main.include',
                    'header_phone',
                    [
                        'AREA_FILE_SHOW' => 'file',
                        'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/template_phone.php',
                    ],
                    true
                );
                $APPLICATION->IncludeComponent(
                    'journal:site.selector',
                    '',
                    [],
                    true
                );

                if (!$USER->IsAuthorized()) {
                    ?>
                    <button class="t-link" data-modal-open="profile-modal">
                        <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_PROFILE')?>
                    </button>
                    <?php
                } else {
                    ?>
                    <a class="t-link" href="<?=SITE_DIR?>personal/">
                        <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_PROFILE')?>
                    </a>
                    <?php
                }
                ?>
                <button class="t-button basket-button js-basket-status" data-modal-url="/local/ajax/basket.php?siteId=<?=SITE_ID?>" data-modal-tab="basket">
                    <svg class="i-basket"><use xlink:href="#i-basket"></use></svg>
                    <div class="basket-button__count js-basket-status__count jsBasketCount">0</div>
                </button>
            </div>
        </div>
    </div>
</header>
<main <?php $APPLICATION->ShowViewContent('MAIN_ATTRIBUTE')?>>
    <?php
    if (!defined('CUSTOM_PAGE_TEMPLATE') && $APPLICATION->GetCurPage(false) !== SITE_DIR) : ?>
        <?php
        /*
        TODO Непонятно, будет ли у этого проекта типовая страница!?
        ?>
        <?php $APPLICATION->IncludeComponent(
            'bitrix:breadcrumb',
            '',
            [
                'PATH' => '',
                'SITE_ID' => SITE_ID,
                'START_FROM' => 0,
            ],
            false,
            ['HIDE_ICONS' => 'Y']
        )?>
        <?php $APPLICATION->ShowTitle(false)?>
        <?php
        */
        ?>
    <?php
    endif ?>
