<?php

use Journal\Analytics;

if (!empty($_REQUEST['siteId'])) {
    define('SITE_ID', htmlspecialchars(strval($_REQUEST['siteId'])));
}

header('Content-Type: application/json; charset=utf-8');

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
    if (!intval($_REQUEST['id'])) {
        throw new Exception('Item not found!');
    }
    echo json_encode([
        'success' => true,
        'data' => Analytics::getGa4Item(intval($_REQUEST['id'])),
    ]);
    die();
} catch (\Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
    die();
}
