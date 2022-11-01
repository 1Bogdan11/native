<?php

const CUSTOM_PAGE_TEMPLATE = 'Y';

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/** @global $APPLICATION */
$APPLICATION->SetTitle('Support');

require __DIR__ . '/.index.php';
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
return;

# Это фикс для страниц с комплексным компонентом, который вынесен в ".index.php", bitrix ищет код компонента и если не находит параметры ЧПУ, удаляет его из "urlrewrite.php"
# Это случается когда правят мету страниц. Можно писать только в строчку и использовать только комментарий # (решетка)
# $APPLICATION->IncludeComponent('bitrix:news', '', ['SEF_MODE' => 'Y', 'SEF_FOLDER' => '/en/support/']);
?>
Редактирование этой страницы запрещено!
