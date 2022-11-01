<?php

use Its\Library\Iblock\Iblock;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/404.php')) {
        require $_SERVER['DOCUMENT_ROOT'] . '/404.php';
    }
    die();
}

/** @global $APPLICATION */

$APPLICATION->AddViewContent('MAIN_ATTRIBUTE', 'class="no-padding" id="js-scroll"');
$APPLICATION->AddViewContent('HEADER_CLASSES', 'is-inview');
$APPLICATION->AddViewContent('FOOTER_CLASSES', 'media-min--tab');
$APPLICATION->AddViewContent('HEADER_LEFT_MENU_ATTRIBUTE', 'style="display:none"');

ob_start();
?>
<div class="aside-tabs__links media-max--tab">
    <button class="back js-back">
        <svg class="i-arrow-left"><use xlink:href="#i-arrow-left"></use></svg>
    </button>
    <button class="aside-tabs__link is-active" data-tab="contacts">Contacts</button>
    <button class="aside-tabs__link" data-tab="requisites">Requisites</button>
</div>
<?php
$APPLICATION->AddViewContent('HEADER_LEFT_ADD_CONTENT', ob_get_clean());

?>
<div class="contacts js-contacts">
    <div class="contacts__aside">
        <div class="aside-tabs aside-tabs--partial">
            <div class="aside-tabs__area">
                <div class="aside-tabs__content is-active" data-tab-content="contacts">
                    <a href="<?=SITE_DIR?>shops/" class="link-circle">Store addresses</a>
                    <?php $APPLICATION->IncludeComponent(
                        'its.agency:form.sample',
                        'contacts',
                        [
                            'IBLOCK_ID' => Iblock::getInstance()->get('contactsform'),
                            'PROPERTIES' => [
                                'NAME',
                                'PROPERTY_EMAIL',
                                'PREVIEW_TEXT',
                            ],
                            'PROPERTIES_REQUIRE' => [
                                'NAME',
                                'PROPERTY_EMAIL',
                                'PREVIEW_TEXT',
                            ],
                            'USE_CAPTCHA' => 'Y',
                            'CAPTCHA_PUBLIC' => RECAPTCHA_PUBLIC_KEY,
                            'CAPTCHA_PRIVATE' => RECAPTCHA_PRIVATE_KEY,
                            'BUTTON_NAME' => 'contacts_form_send',
                            'MAIL_EVENT_NAME' => 'CONTACTS_FORM_SEND_EVENT',
                        ],
                        false
                    )?>
                </div>
                <div class="aside-tabs__content" data-tab-content="requisites">
                    <div class="contacts__head">
                        <div class="contacts__head">
                            <div class="contacts__head_title">Requisites</div>
                            <div class="contacts__head_description">
                                <?php $APPLICATION->IncludeComponent(
                                    'bitrix:main.include',
                                    '',
                                    [
                                        'AREA_FILE_SHOW' => 'file',
                                        'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/contacts_company.php',
                                    ],
                                    true
                                )?>
                            </div>
                        </div>
                    </div>
                    <div class="contacts__content">
                        <div class="contact-requisites">
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:main.include',
                                '',
                                [
                                    'AREA_FILE_SHOW' => 'file',
                                    'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/contacts_requisites.php',
                                ],
                                true
                            )?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="contacts__map media-max--tab">
        <div class="map" id="map" data-map="single"></div>
        <?php $APPLICATION->IncludeComponent(
            'bitrix:main.include',
            'contacts_email_icon',
            [
                'AREA_FILE_SHOW' => 'file',
                'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/template_email.php',
            ],
            true
        )?>
    </div>
</div>
<script>
    const regionsMapData = [
        {
            type: "FeatureCollection",
            id: "1",
            active: true,
            features: [
                {
                    mainRegionPoint: true,
                    type: "Feature",
                    id: "1",
                    geometry: {
                        type: "Point",
                        coordinates: [37.491081, 55.843121]
                    }
                }
            ]
        }
    ]
</script>
