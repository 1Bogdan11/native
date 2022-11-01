<?php

const CUSTOM_PAGE_TEMPLATE = 'Y';

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/** @global $APPLICATION */
$APPLICATION->SetTitle('Native');
$APPLICATION->SetPageProperty('title', '');
$APPLICATION->SetPageProperty('description', '');

require __DIR__ . '/.index.php';
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
return;
?>
