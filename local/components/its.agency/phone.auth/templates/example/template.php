<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(false);

?>
<div style="padding: 100px;" id="wrapX">
    <?
    foreach ($arResult['ERRORS'] as $key => &$message) {
        if (intval($key) !== $key) {
            $message = str_replace(
                '#FIELD#',
                Loc::getMessage("ITS_AGENCY_PHONE_AUTH_TEMPLATE_FIELD_{$key}"),
                $message
            );
        }
    }

    if (!empty($arResult['ERRORS'])) {
        ?>
        <p style="color:red;"><?=implode('<br>', $arResult['ERRORS'])?></p>
        <?
    }

    if ($arResult['TEMPLATE_TYPE'] === 'SUCCESS') {
        ?>
        <p>Вы успешно зарегистрированы\авторизованы!</p>
        <?
    } elseif ($arResult['TEMPLATE_TYPE'] === 'LAST_STEP') {
        ?>
        <p>Заполнить поля!</p>
        <a href="<?=$APPLICATION->GetCurPageParam('change_number=Y', ['change_number'])?>">Сменить номер телефона</a>
        <form action="#" method="post">
            <?
            foreach ($arResult['FIELDS'] as $code => $arField) {
                ?>
                <label style="padding-bottom:20px;display:block;">
                    <div>
                        <?=Loc::getMessage("ITS_AGENCY_PHONE_AUTH_TEMPLATE_FIELD_{$code}")?>
                        <?=($arField['REQUIRED'] === 'Y' ? '*' : '')?>
                    </div>
                    <input type="text" name="<?=$arField['FIELD_NAME']?>" value="<?=$arField['VALUE']?>" style="border: 1px solid;">
                </label>
                <?
            }
            ?>
            <input type="submit" name="SAVE_ADDITIONAL_FIELDS" value="Сохранить" style="border: 1px solid;">
        </form>
        <?
    } else {
        ?>
        <form action="#" method="post" id="auth">
            <div id="error" style="color:red;padding-bottom:15px;"></div>
            <label style="padding-bottom:20px;display:block;">
                <div>Телефон</div>
                <input type="text" name="phone" id="phone" value="" style="border: 1px solid;">
                <input type="button" id="phoneButton" value="Получить код" style="border: 1px solid;">
                <input type="button" id="phoneChangeButton" value="Сменить телефон" style="border: 1px solid;display:none;">
            </label>
            <label id="codeBlock" style="padding-bottom:20px;display:none;">
                <div>Код подтверждения</div>
                <input type="text" name="code" id="code" value="" style="border: 1px solid;">
                <div id="message" style="margin: 15px 0;">Новый код подтверждения можно запросить через <span id="counter"><?=intval($arParams['RESEND_LIMIT'])?></span> сек.</div>
                <a id="repeat" href="javascript:void(0);" style="display: none;margin: 15px 0;">Запросить новый код</a>
                <input type="button" id="codeButton" value="Подтвердить" style="border: 1px solid;">
            </label>
        </form>
        <?
        $params = [
            'sessId' => bitrix_sessid(),
            'currentPage' => $APPLICATION->GetCurPageParam('', ['change_number']),
            'wrap' => 'wrapX',
            'error' => 'error',
            'phone' => 'phone',
            'phoneButton' => 'phoneButton',
            'phoneChangeButton' => 'phoneChangeButton',
            'code' => 'code',
            'codeButton' => 'codeButton',
            'codeBlock' => 'codeBlock',
            'repeat' => 'repeat',
            'message' => 'message',
            'counter' => 'counter',
        ];
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.phoneAuth = new PhoneAuthComponent(<?=json_encode($params)?>)
            });
        </script>
        <?
    }
    ?>
</div>
