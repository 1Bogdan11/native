<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/urlrewrite.php';

if (!defined('CUSTOM_PAGE_TEMPLATE')) {
    define('CUSTOM_PAGE_TEMPLATE', 'Y');
}

\CHTTP::SetStatus('404 Not Found');
const ERROR_404 = 'Y';

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/** @global $APPLICATION */
$APPLICATION->SetPageProperty('NOT_SHOW_NAV_CHAIN', 'Y');
$APPLICATION->AddViewContent('MAIN_ATTRIBUTE', 'id="js-scroll" data-direction="vertical"');
?>

<?php
$APPLICATION->IncludeComponent('journal:not.found', '') ?>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'; ?>
