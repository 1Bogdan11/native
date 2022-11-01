<?php

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

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
<form class="profile__form" id="personalAddressForm">
    <?=bitrix_sessid_post()?>
    <?php $APPLICATION->IncludeComponent(
        'journal:sale.location.selector.search',
        'address',
        [
            'PROPERTY_NAME' => Loc::getMessage('PERSONAL_ADDRESS_CITY'),
            'INPUT_VALUE' => $arResult['USER']['UF_CITY'],
            'INPUT_NAME' => 'UF_CITY',
            'IS_REQUIRED' => 'N',
        ],
        true,
        ['HIDE_ICONS' => 'Y']
    )?>
    <div class="input">
        <input type="text"
            name="UF_ADDRESS"
            id="UF_ADDRESS"
            value="<?=htmlspecialchars($arResult['USER']['UF_ADDRESS'])?>"
        />
        <label class="input__label" for="UF_ADDRESS">
            <?=Loc::getMessage('PERSONAL_ADDRESS_ADDRESS')?>
        </label>
        <div class="input__bar"></div>
    </div>
    <button class="button-black profile__form_submit"><?=Loc::getMessage('PERSONAL_ADDRESS_SAVE')?></button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('personalAddressForm').addEventListener('submit', function (event) {
            event.preventDefault();
            event.stopPropagation();
            axios({
                url: <?=json_encode($APPLICATION->GetCurPage(false))?>,
                method: 'post',
                params: {
                    save_address: 'Y',
                    return_json: 'Y',
                },
                data: new FormData(this),
                timeout: 0,
                responseType: 'json',
            }).then(function (response) {
                let data = response.data;
                if (!data.success) {
                    showMessage({
                        title: <?=json_encode(Loc::getMessage('PERSONAL_ADDRESS_ERROR'))?>,
                        text: <?=json_encode(Loc::getMessage('PERSONAL_ADDRESS_ERROR_MESSAGE'))?>,
                        time: 10000,
                    });
                } else {
                    showMessage({
                        title: <?=json_encode(Loc::getMessage('PERSONAL_ADDRESS_SUCCESS'))?>,
                        text: <?=json_encode(Loc::getMessage('PERSONAL_ADDRESS_SUCCESS_MESSAGE'))?>,
                        time: 10000,
                    });
                }
            });
        });
    });
</script>
