<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/** @global $APPLICATION */
$APPLICATION->SetTitle("Магазины");
$APPLICATION->SetPageProperty('title', 'Магазины нижнего белья Le Journal Intime');
$APPLICATION->SetPageProperty('description', 'Контактная информация официальных магазинов нижнего белья Le Journal Intime: адреса, телефоны, режим работы, e-mail. Если у вас возникли вопросы, свяжитесь с нами любым удобным для вас способом.');

require __DIR__ . '/.index.php';
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
return;

# Это фикс для страниц с комплексным компонентом, который вынесен в ".index.php", bitrix ищет код компонента и если не находит параметры ЧПУ, удаляет его из "urlrewrite.php"
# Это случается когда правят мету страниц. Можно писать только в строчку и использовать только комментарий # (решетка)
# $APPLICATION->IncludeComponent('bitrix:news', '', ['SEF_MODE' => 'Y', 'SEF_FOLDER' => '/shops/']);
?>
Редактирование этой страницы запрещено!
