<?php

use AdminConstructor\Lang;
use AdminConstructor\Router;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

$router = new Router();

try {
    Lang::setLanguage('ru');

    $router->setNormalise(true);
    $router->setNamespace('Its\\Maxma\\Page');
    $router->setPrefix('its-maxma');
    $router->begin();

    if ($router->isModalMode()) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_popup_admin.php';
        $router->print();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_popup_admin.php';
    } else {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';
        $router->print();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
    }
} catch (\Throwable $e) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';
    $router->showError($e->getPrevious() ?? $e);
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
}
