<?php

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;

global $APPLICATION;
$documentRoot = Context::getCurrent()->getServer()->getDocumentRoot();
$arModule = require "module.php";
$module_id = $arModule['MODULE_ID']; // Bitrix core fix
$options = [];
$optionsErrors = [];

Loc::loadMessages($documentRoot . BX_ROOT . '/modules/main/options.php');
Loc::loadMessages(__FILE__);

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
