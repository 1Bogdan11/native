<?php

$main = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/its.maxma/admin/router.php';
$local = $_SERVER['DOCUMENT_ROOT'] . '/local/modules/its.maxma/admin/router.php';
require_once file_exists($local) ? $local : $main;
