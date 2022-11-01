<?php

const PERSONAL_SECTION = true;
const CUSTOM_PAGE_TEMPLATE = 'Y';

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/** @global $APPLICATION */
$APPLICATION->SetTitle('Избранное');

require __DIR__ . '/.index.php';
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
return;

?>
Редактирование этой страницы запрещено!
