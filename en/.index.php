<?php

use Its\Library\Iblock\Iblock;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/404.php')) {
        require $_SERVER['DOCUMENT_ROOT'] . '/404.php';
    }
    die();
}

/** @global $APPLICATION */

?>
<main class="no-padding is-plug" id="js-scroll" data-direction="vertical">
    <header class="header is-active is-fixed js-header">
        <div class="container header__inner">
            <div class="header__left"></div>
            <div class="header__center">
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
            </div>
            <div class="header__right">
                <div class="header__contacts">
                    <?php $APPLICATION->IncludeComponent(
                        'journal:site.selector',
                        '',
                        [],
                        true
                    )?>
                </div>
            </div>
        </div>
    </header>
    <div class="plug">
        <div class="plug__image">
            <img src="/assets/img/eng-plug.jpg" data-scroll data-scroll-speed="-2">
        </div>
        <div class="plug__content">
            <h1 class="star-title" data-observe="fade-y" data-scroll>
                <svg class="i-subtract"><use xlink:href="#i-subtract"></use></svg>
                <span>Perfect match</span>
            </h1>
            <div class="plug__description" data-observe="fade-y" data-scroll>
                New site will be here soon...
            </div>
            <button class="link__underline" data-observe="fade-y" data-scroll data-scrollto=".footer-form">
                Contact us
            </button>
        </div>
    </div>
    <div class="footer-form container">
        <?php $APPLICATION->IncludeComponent(
            'its.agency:form.sample',
            'en_feedback',
            [
                'IBLOCK_ID' => Iblock::getInstance()->get('en_feedback'),
                'PROPERTIES' => [
                    'NAME',
                    'PREVIEW_TEXT',
                ],
                'PROPERTIES_REQUIRE' => [
                    'NAME',
                    'PREVIEW_TEXT',
                ],
                'USE_CAPTCHA' => 'Y',
                'CAPTCHA_PUBLIC' => RECAPTCHA_PUBLIC_KEY,
                'CAPTCHA_PRIVATE' => RECAPTCHA_PRIVATE_KEY,
                'BUTTON_NAME' => 'en_form_send',
                'MAIL_EVENT_NAME' => 'EN_FORM_SEND_EVENT',
            ],
            false
        )?>
        <div class="footer-form__copyright">
            © Le Journal Intime
        </div>
    </div>
</main>
