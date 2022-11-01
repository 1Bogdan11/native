<?php

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Sale\Internals\StatusTable;
use Bitrix\Main\Entity\ExpressionField;

global $APPLICATION;
$documentRoot = Context::getCurrent()->getServer()->getDocumentRoot();
$arModule = require "module.php";
$module_id = $arModule['MODULE_ID']; // Bitrix core fix
$options = [];
$optionsErrors = [];

Loc::loadMessages($documentRoot . BX_ROOT . '/modules/main/options.php');
Loc::loadMessages(__FILE__);

$orderStatuses = [];

try {
    Loader::includeModule('sale');

    $resourceStatuses = StatusTable::getList([
        'order' => ['SORT' => 'ASC'],
        'filter' => [
            '=TYPE' => 'O',
            'STATUS_LANG.LID' => LANGUAGE_ID,
        ],
        'select' => [
            'ID',
            'NAME' => 'STATUS_LANG.NAME',
        ],
    ]);
    while ($status = $resourceStatuses->fetch()) {
        $orderStatuses[$status['ID']] = "[{$status['ID']}] {$status['NAME']}";
    }
} catch (\Throwable $e) {
    $optionsErrors[] = $e->getMessage();
}

$options[] = Loc::getMessage('ITS_MAXMA_LABEL_API');
$options[] = [
    'its_maxma_test_mode',
    Loc::getMessage('ITS_MAXMA_OPTION_TEST_MODE'),
    'N',
    ['checkbox', 'Y'],
];
$options[] = [
    'its_maxma_api_key',
    Loc::getMessage('ITS_MAXMA_OPTION_API_KEY'),
    '', // test key
    ['text', 50],
];
$options[] = [
    'its_maxma_shop_code',
    Loc::getMessage('ITS_MAXMA_OPTION_SHOP_CODE'),
    'EMPTY_CODE',
    ['text', 50],
];
$options[] = [
    'its_maxma_shop_name',
    Loc::getMessage('ITS_MAXMA_OPTION_SHOP_NAME'),
    'EMPTY_NAME',
    ['text', 50],
];
$options[] = [
    'its_maxma_phone_field_key',
    Loc::getMessage('ITS_MAXMA_OPTION_PHONE_FIELD_KEY'),
    'PERSONAL_PHONE',
    ['text', 50],
];
$options[] = [
    'its_maxma_order_prop_coupon',
    Loc::getMessage('ITS_MAXMA_OPTION_ORDER_PROP_COUPON'),
    'MAXMA_COUPON',
    ['text', 40],
];
$options[] = [
    'its_maxma_order_prop_bonus_count',
    Loc::getMessage('ITS_MAXMA_OPTION_ORDER_PROP_BONUS_COUNT'),
    'MAXMA_BONUS_COUNT',
    ['text', 40],
];
$options[] = [
    'its_maxma_order_prop_phone',
    Loc::getMessage('ITS_MAXMA_OPTION_ORDER_PROP_PHONE'),
    'PHONE',
    ['text', 40],
];
$options[] = [
    'its_maxma_element_card_code',
    Loc::getMessage('ITS_MAXMA_OPTION_ELEMENT_CARD_CODE'),
    'MAXMA_CERTIFICATE',
    ['text', 40],
];
$options[] = [
    'its_maxma_send_artnumber',
    Loc::getMessage('ITS_MAXMA_OPTION_SEND_ARTNUMBER'),
    'N',
    ['checkbox', 'Y'],
];
$options[] = [
    'its_maxma_send_artnumber_or_xml_id_sku',
    Loc::getMessage('ITS_MAXMA_OPTION_SEND_ARTNUMBER_OR_XML_ID_SKU'),
    'N',
    ['checkbox', 'Y'],
];
$options[] = [
    'its_maxma_element_artnumber_field',
    Loc::getMessage('ITS_MAXMA_OPTION_ELEMENT_ARTNUMBER_FIELD'),
    'CML2_ARTICLE',
    ['text', 40],
];
$options[] = [
    'its_maxma_element_artnumber_field_sku',
    Loc::getMessage('ITS_MAXMA_OPTION_ELEMENT_ARTNUMBER_FIELD_SKU'),
    'CML2_ARTICLE',
    ['text', 40],
];
$options[] = [
    'its_maxma_fake_user_phone',
    Loc::getMessage('ITS_MAXMA_OPTION_FAKE_USER_PHONE'),
    '+79777777777',
    ['text', 40],
];

$options[] = Loc::getMessage('ITS_MAXMA_LABEL_AUTOMATIC');
$options[] = [
    'its_maxma_enable_cancel_order',
    Loc::getMessage('ITS_MAXMA_OPTION_ENABLE_CANCEL_ORDER'),
    'N',
    ['checkbox', 'Y'],
];
$options[] = [
    'its_maxma_enable_accept_order',
    Loc::getMessage('ITS_MAXMA_OPTION_ENABLE_ACCEPT_ORDER'),
    'N',
    ['checkbox', 'Y'],
];
$options[] = [
    'its_maxma_order_end_status',
    Loc::getMessage('ITS_MAXMA_OPTION_ORDER_END_STATUS'),
    'F',
    ['selectbox', $orderStatuses],
];
$options[] = [
    'its_maxma_order_send_timeout',
    Loc::getMessage('ITS_MAXMA_OPTION_ORDER_SEND_TIMEOUT'),
    '20160',
    ['text', 40],
];

$options[] = Loc::getMessage('ITS_MAXMA_LABEL_MAIL');
$fields = ['COUPON', 'DISCOUNT_BONUS', 'DISCOUNT_COUPON', 'DISCOUNT_TOTAL', 'BONUS_COLLECT', 'ORDER_ITEMS', 'ORDER_DELIVERY', 'ORDER_TOTAL'];
foreach ($fields as $field) {
    $options[] = [
        'its_maxma_order_mail_html_' . mb_strtolower($field),
        Loc::getMessage('ITS_MAXMA_OPTION_ORDER_MAIL', ['#FIELD#' => $field]),
        '<p><b>' . Loc::getMessage("ITS_MAXMA_OPTION_ORDER_MAIL_FIELD_{$field}") . ':</b> #VALUE#</p>',
        ['text', 40],
    ];
}


$options[] = Loc::getMessage('ITS_MAXMA_LABEL_DEBUG');
$options[] = [
    'its_maxma_enable_error_log',
    Loc::getMessage('ITS_MAXMA_OPTION_ENABLE_ERROR_LOG'),
    'N',
    ['checkbox', 'Y'],
];
$options[] = [
    'its_maxma_error_log_path',
    Loc::getMessage('ITS_MAXMA_OPTION_ERROR_LOG_PATH'),
    '/maxma_errors_#DATE#.log',
    ['text', 50],
];

$arTabs = [
    [
        'DIV' => 'module_options',
        'TAB' => Loc::getMessage('MAIN_TAB_SET'),
        'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_SET'),
        'OPTIONS' => $options
    ],
    [
        'DIV' => 'rights',
        'TAB' => Loc::getMessage('MAIN_TAB_RIGHTS'),
        'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_RIGHTS'),
        'OPTIONS' => false
    ]
];

$request = Context::getCurrent()->getRequest();

if ($request->isPost() && strlen($request['save']) > 0 && check_bitrix_sessid()) {
    foreach ($arTabs as $arTab) {
        __AdmSettingsSaveOptions($module_id, $arTab['OPTIONS']);
    }

    LocalRedirect(
        $APPLICATION->GetCurPage()
        . '?lang=' . LANGUAGE_ID
        . '&mid_menu=1&mid=' . $module_id
        . '&tabControl_active_tab=' . urlencode($request['tabControl_active_tab'])
        . '&sid=' . SITE_ID
    );
}

if (count($optionsErrors) > 0) {
    echo (new \CAdminMessage(['MESSAGE' => implode('<br>', $optionsErrors), 'TYPE' => 'ERROR']))->Show();
}

$tabControl = new CAdminTabControl("tabControl", $arTabs);
$tabControl->Begin();

?>
<form method="post" action="<?=$APPLICATION->GetCurPage() . '?mid=' . $module_id . '&lang=' . LANGUAGE_ID?>">
<?php

echo bitrix_sessid_post();

foreach ($arTabs as $arTab) {
    $tabControl->BeginNextTab();

    if (is_array($arTab['OPTIONS'])) {
        __AdmSettingsDrawList($module_id, $arTab['OPTIONS']);
    } else {
        require $documentRoot . BX_ROOT . '/modules/main/admin/group_rights.php';
    }
}

$tabControl->EndTab();
$tabControl->Buttons([
    'btnSave' => true,
    'btnApply' => false,
    'btnCancel' => false,
    'btnSaveAndAdd' => false
]);

?>
</form>
<?php

$tabControl->End();
