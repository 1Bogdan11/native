<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @global CMain $APPLICATION */
/** @global CUser $USER */

$APPLICATION->IncludeComponent(
    'journal:subscribe.form',
    'footer',
    [
        'FORM_NAME' => 'subscribe_footer_form',
    ]
);
