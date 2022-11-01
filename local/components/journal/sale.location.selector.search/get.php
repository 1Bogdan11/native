<?php

const NO_KEEP_STATISTIC = true;
const NO_AGENT_STATISTIC = true;
const NO_AGENT_CHECK = true;
const NOT_CHECK_PERMISSIONS = true;

use Bitrix\Main;
use Bitrix\Main\Loader;

require_once $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php';

global $USER;
Loader::includeModule('sale');

require_once dirname(__FILE__) . '/class.php';

$result = true;
$errors = [];
$data = [];

try {
    CUtil::JSPostUnescape();

    $request = Main\Context::getCurrent()->getRequest()->getPostList();

    $params = [
        'select' => [
            'CODE',
            'TYPE_ID',
            'VALUE' => 'ID',
            'DISPLAY' => 'NAME.NAME',
        ],
        'additionals' => [
            'PATH',
        ],
        'version' => '2',
        'PAGE_SIZE' => '10',
        'PAGE' => '0',
        'filter' => [
            '=NAME.LANGUAGE_ID' => htmlspecialchars($request['lang']),
            '=SITE_ID' => htmlspecialchars($request['site']),
            '=PHRASE' => htmlspecialchars($request['search']),
        ],
    ];

    $data = CBitrixLocationSelectorSearchComponentCustom::processSearchRequestV2($params);
} catch (Main\SystemException $e) {
    $result = false;
    $errors[] = !$USER->IsAdmin() ? 'Internal error!' : $e->getMessage();
}

header('Content-Type: application/x-javascript; charset=' . LANG_CHARSET);
echo json_encode([
    'result' => $result,
    'errors' => $errors,
    'data' => $data,
]);
