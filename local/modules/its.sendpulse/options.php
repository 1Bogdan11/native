<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'its.sendpulse');

if (!$USER->isAdmin()) {
    $APPLICATION->authForm('Nope');
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();
$save = $request->getPost('save');
$restore = $request->getPost('restore');
Loc::loadMessages($context->getServer()->getDocumentRoot() . "/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl(
    "tabControl", array(
    array(
        "DIV" => "edit1",
        "TAB" => Loc::getMessage("MAIN_TAB_SET"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_SET"),
    ),
)
);
if ((!empty($save) || !empty($restore)) && $request->isPost() && check_bitrix_sessid()) {
    if (!empty($restore)) {
        Option::delete(ADMIN_MODULE_NAME);
        CAdminMessage::showMessage(
            array(
                "MESSAGE" => Loc::getMessage("REFERENCES_OPTIONS_RESTORED"),
                "TYPE" => "OK",
            )
        );
    } elseif (($request->getPost('API_USER_ID')) && ($request->getPost('API_SECRET')) && ($request->getPost(
            'BOOK_ID'
        ))) {
        Option::set(
            ADMIN_MODULE_NAME,
            'API_USER_ID',
            $request->getPost('API_USER_ID')
        );
        Option::set(
            ADMIN_MODULE_NAME,
            'API_SECRET',
            $request->getPost('API_SECRET')
        );
        Option::set(
            ADMIN_MODULE_NAME,
            'BOOK_ID',
            $request->getPost('BOOK_ID')
        );
        Option::set(
            ADMIN_MODULE_NAME,
            'SUBSCRIBE_NAME',
            $request->getPost('BOOK_ID')
        );
        CAdminMessage::showMessage(
            array(
                "MESSAGE" => "Настройки успешно сохранены",
                "TYPE" => "OK",
            )
        );
    } else {
        CAdminMessage::showMessage("Ошибка сохранения. Все поля обязательны");
    }
}

$tabControl->begin();
?>

<form method="post"
      action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>">
    <?php
    echo bitrix_sessid_post();
    $tabControl->beginNextTab();
    ?>

    <tr>
        <td width="10%">
            <label for="sections_sort">API_USER_ID</label>
        <td width="90%">
            <textarea type="text"
                      name="API_USER_ID"
                      style="width:100%"
                      value="<?= Option::get(ADMIN_MODULE_NAME, 'API_USER_ID', ''); ?>"
            ><?= Option::get(ADMIN_MODULE_NAME, 'API_USER_ID', ''); ?></textarea>
        </td>
    </tr>
    <tr>
        <td width="10%">
            <label for="sections_sort">API_SECRET</label>
        <td width="90%">
            <textarea type="text"
                      name="API_SECRET"
                      style="width:100%"
                      value="<?= Option::get(ADMIN_MODULE_NAME, 'API_SECRET', ''); ?>"
            ><?= Option::get(ADMIN_MODULE_NAME, 'API_SECRET', ''); ?></textarea>
        </td>
    </tr>
    <tr>
        <td width="10%">
            <label for="sections_sort">BOOK_ID - Адресная книга</label>
        <td width="90%">
            <textarea type="text"
                      name="BOOK_ID"
                      style="width:100%"
                      value="<?= Option::get(ADMIN_MODULE_NAME, 'BOOK_ID', ''); ?>"
            ><?= Option::get(ADMIN_MODULE_NAME, 'BOOK_ID', ''); ?></textarea>
        </td>
    </tr>
    <?php
    $tabControl->buttons();
    ?>
    <input type="submit"
           name="save"
           value="<?= Loc::getMessage("MAIN_SAVE") ?>"
           title="<?= Loc::getMessage("MAIN_OPT_SAVE_TITLE") ?>"
           class="adm-btn-save"
    />
    <input type="submit"
           name="restore"
           title="<?= Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?= Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
    <?php
    $tabControl->end();
    ?>
</form>

