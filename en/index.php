<?php

session_start();
if (isset($_SESSION['USER_IS_ADMIN']) && $_SESSION['USER_IS_ADMIN']) {
    define('CUSTOM_SITE_TEMPLATE', 'Y');
} else {
    define('EN_SITE_TEMPLATE', 'Y');
}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

global $USER;
$_SESSION['USER_IS_ADMIN'] = $USER->IsAdmin();
if ($USER->IsAdmin() && defined('EN_SITE_TEMPLATE')) {
    LocalRedirect('/en/');
}

/** @global $APPLICATION */
$APPLICATION->SetTitle("Le Journal");

require __DIR__ . ($USER->IsAdmin() ? '/.en_index.php' : '/.index.php');
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
return;
?>
