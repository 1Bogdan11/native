<?php

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/** @global $APPLICATION */
$APPLICATION->SetTitle("Дневник");
$APPLICATION->SetPageProperty('title', 'Дневник Le Journal Intime: статьи, новости, обзоры');
$APPLICATION->SetPageProperty('description', 'В дневнике Le Journal Intime вы найдете полезные статьи на актуальные темы, свежие новости о деятельности бренда и обзоры последних новинок из наших коллекций нижнего белья, купальников и одежды.');

require __DIR__ . '/.index.php';
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
return;

# Это фикс для страниц с комплексным компонентом, который вынесен в ".index.php", bitrix ищет код компонента и если не находит параметры ЧПУ, удаляет его из "urlrewrite.php"
# Это случается когда правят мету страниц. Можно писать только в строчку и использовать только комментарий # (решетка)
# $APPLICATION->IncludeComponent('bitrix:news', '', ['SEF_MODE' => 'Y', 'SEF_FOLDER' => '/blog/']);
?>
Редактирование этой страницы запрещено!
