<?php

const CUSTOM_PAGE_TEMPLATE = 'Y';

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/** @global $APPLICATION */
$APPLICATION->SetTitle("Каталог");
$APPLICATION->SetPageProperty('title', 'Каталог нижнего белья Le Journal Intime | Купить белье Le Journal Intime');
$APPLICATION->SetPageProperty('description', 'Купить удобное и красивое нижнее белье Le Journal Intime по лучшим ценам производителя в официальном интернет-магазине. ✔Качественный материал. ✔В нашем кателоге большой выбор моделей и размеров нижнего белья для женщин и мужчин. ✔Бесплатная доставка заказа от 15000 руб. ✔Самовывоз из магазина. ✔Примерка. ✔Доставка по России и СНГ. ☎Тел.: 8 (800) 600-32-69.');

require __DIR__ . '/.index.php';
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
return;

# Это фикс для страниц с комплексным компонентом, который вынесен в ".index.php", bitrix ищет код компонента и если не находит параметры ЧПУ, удаляет его из "urlrewrite.php"
# Это случается когда правят мету страниц. Можно писать только в строчку и использовать только комментарий # (решетка)
# $APPLICATION->IncludeComponent('journal:catalog', '', ['SEF_MODE' => 'Y', 'SEF_FOLDER' => '/catalog/']);
?>
Редактирование этой страницы запрещено!
