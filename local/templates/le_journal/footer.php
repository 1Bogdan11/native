<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $USER, $APPLICATION;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);


/* Заглушка для EN сайта */
if (!defined('EN_SITE_TEMPLATE') || $USER->IsAdmin()) {
?>
        <?php if (!defined('CUSTOM_PAGE_TEMPLATE')) :?>
            <?php
            // TODO Непонятно, будет ли у этого проекта типовая страница!?
            ?>
        <?php endif?>

        <footer class="footer container <?php $APPLICATION->ShowViewContent('FOOTER_CLASSES')?>">
            <div class="footer__column footer__column--span-1">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:menu',
                    'footer',
                    [
                        'ROOT_MENU_TYPE' => 'footer_1',
                        'MENU_CACHE_TYPE' => 'N',
                        'MENU_CACHE_TIME' => '3600',
                        'MENU_CACHE_USE_GROUPS' => 'Y',
                        'MENU_CACHE_GET_VARS' => [],
                        'MAX_LEVEL' => 1,
                        'CHILD_MENU_TYPE' => '',
                        'USE_EXT' => 'N',
                        'DELAY' => 'N',
                        'ALLOW_MULTI_SELECT' => 'N',
                    ],
                    true,
                )?>
            </div>
            <div class="footer__column footer__column--span-1">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:menu',
                    'footer',
                    [
                        'ROOT_MENU_TYPE' => 'footer_2',
                        'MENU_CACHE_TYPE' => 'N',
                        'MENU_CACHE_TIME' => '3600',
                        'MENU_CACHE_USE_GROUPS' => 'Y',
                        'MENU_CACHE_GET_VARS' => [],
                        'MAX_LEVEL' => 1,
                        'CHILD_MENU_TYPE' => '',
                        'USE_EXT' => 'N',
                        'DELAY' => 'N',
                        'ALLOW_MULTI_SELECT' => 'N',
                    ],
                    true,
                )?>
            </div>
            <div class="footer__column footer__column--span-1">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:menu',
                    'footer',
                    [
                        'ROOT_MENU_TYPE' => 'footer_3',
                        'MENU_CACHE_TYPE' => 'N',
                        'MENU_CACHE_TIME' => '3600',
                        'MENU_CACHE_USE_GROUPS' => 'Y',
                        'MENU_CACHE_GET_VARS' => [],
                        'MAX_LEVEL' => 1,
                        'CHILD_MENU_TYPE' => '',
                        'USE_EXT' => 'N',
                        'DELAY' => 'N',
                        'ALLOW_MULTI_SELECT' => 'N',
                    ],
                    true,
                )?>
            </div>
            <div class="footer__column footer__socials">
                <ul class="socials">
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:main.include',
                        'footer_social_icon',
                        [
                            'SOCIAL_TYPE' => 'VK',
                            'AREA_FILE_SHOW' => 'file',
                            'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/template_vk.php',
                        ],
                        true
                    )?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:main.include',
                        'footer_social_icon',
                        [
                            'SOCIAL_TYPE' => 'YOUTUBE',
                            'AREA_FILE_SHOW' => 'file',
                            'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/template_youtube.php',
                        ],
                        true
                    )?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:main.include',
                        'footer_social_icon',
                        [
                            'SOCIAL_TYPE' => 'TELEGRAM',
                            'AREA_FILE_SHOW' => 'file',
                            'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/template_telegram.php',
                        ],
                        true
                    )?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:main.include',
                        'footer_social_icon',
                        [
                            'SOCIAL_TYPE' => 'WHATSAPP',
                            'AREA_FILE_SHOW' => 'file',
                            'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/template_whatsapp.php',
                        ],
                        true
                    )?>
                </ul>
            </div>
            <div class="footer__column footer__column--span-2">
                <?php require __DIR__ . '/block/subscribe.php'?>
            </div>
            <div class="footer__column footer__column--span-3 footer__column--bottom">
                <div class="copyright">
                    <p class="copyright__text">
                        <?=Loc::getMessage('FOOTER_COPYRIGHT')?><br>
                        <span class="media-max--tab"><?=Loc::getMessage('FOOTER_COPYRIGHT_TITLE')?></span>
                    </p>
                </div>
                <a class="t-link--m link__underline--hover footer__politic" href="<?=SITE_DIR?>support/politic/">
                    <?=Loc::getMessage('FOOTER_PRIVACY_POLICY')?>
                </a>
            </div>
            <div class="footer__column footer__column--last footer__column--bottom">
                <div class="payment-list footer__payment-list">
                    <div class="payment-list__item">
                        <svg class="i-visa"><use xlink:href="#i-visa"></use></svg>
                    </div>
                    <div class="payment-list__item">
                        <svg class="i-mir-pay"><use xlink:href="#i-mir-pay"></use></svg>
                    </div>
                    <div class="payment-list__item payment-list__item--nofill">
                        <svg class="i-master-card"><use xlink:href="#i-master-card"></use></svg>
                    </div>
                </div>
                <div class="footer__company">
                    <?=Loc::getMessage('FOOTER_VENDOR')?>
                </div>
            </div>
        </footer>
        <?php $APPLICATION->IncludeComponent(
            'bitrix:main.include',
            'schema',
            [
                'AREA_FILE_SHOW' => 'file',
                'IMAGE' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/template_schema_image.php',
                'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/template_schema.php',
            ],
            true
        )?>
    </main>
<?php
}
?>
</body>
</html>
