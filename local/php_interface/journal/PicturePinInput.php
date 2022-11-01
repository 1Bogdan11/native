<?php

namespace Journal;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Context;
use Bitrix\Iblock\ElementTable;

class PicturePinInput
{
    public function getUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE' => PropertyTable::TYPE_STRING,
            'USER_TYPE' => 'its_picture_pin_property',
            'DESCRIPTION' => Loc::getMessage('ITS_PICTURE_PIN_PROPERTY'),
            'GetPropertyFieldHtml' => [__CLASS__, 'getPropertyFieldHtml'],
            'GetPublicViewHTML' => [__CLASS__, 'getPublicViewHTML'],
            'GetAdminListViewHTML' => [__CLASS__,'getAdminListViewHTML'],
            'ConvertToDB' => [__CLASS__, 'convertToDB'],
            'GetSettingsHTML' => [__CLASS__, 'getSettingsHTML'],

        );
    }

    protected static function getRowTemplate(array $arResize, string $fieldName, string $coords = '', string $name = '', string $description = ''): string
    {
        ob_start();
        ?>
        <div class="admin-position">
            <div class="admin-position-left">
                <div class="admin-position-box" style="background-image: url(<?=$arResize['src']?>); height: <?=$arResize['height']?>px;">
                    <?php
                    $stepsCount = 20;
                    for ($row = 0; $row <= $stepsCount; $row++) {
                        for ($col = 0; $col <= $stepsCount; $col++) {
                            $position = (100 / $stepsCount * $row) . ':' . (100 / $stepsCount * $col);
                            ?>
                            <input type="radio"
                                name="<?=$fieldName?>[COORDS]"
                                <?=($position === $coords || (empty($coords) && $position === '0:0') ? 'checked' : '')?>
                                value="<?=$position?>"
                                title="<?=$position?>"
                            />
                            <i></i>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="admin-position-right">
                <div class="admin-position-value">&nbsp;</div>
                <div class="admin-position-name">
                    <textarea
                        name="<?=$fieldName?>[NAME]"
                        rows="2"
                        placeholder="<?=Loc::getMessage('ITS_PICTURE_PIN_PROPERTY_NAME')?>"
                    ><?=$name?></textarea>
                </div>
                <div class="admin-position-description">
                    <textarea
                        name="<?=$fieldName?>[DESCRIPTION]"
                        rows="4"
                        placeholder="<?=Loc::getMessage('ITS_PICTURE_PIN_PROPERTY_DESCRIPTION')?>"
                    ><?=$description?></textarea>
                </div>
                <div class="admin-position-remove">
                    <a href="javascript:void(0);" onclick="if (confirm('<?=Loc::getMessage('ITS_PICTURE_PIN_PROPERTY_DELETE_CONFIRM')?>')) {$(this).parents('.admin-position').remove()}">
                        <?=Loc::getMessage('ITS_PICTURE_PIN_PROPERTY_DELETE')?>
                    </a>
                </div>
            </div>
            <div class="admin-position-clear"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function getPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        global $APPLICATION;
        \CJSCore::Init('jquery3');

        $elementId = intval($_REQUEST['ID']);
        $arElement = ElementTable::getList([
            'filter' => ['=ID' => $elementId],
            'select' => ['DETAIL_PICTURE'],
        ])->fetch();

        if (!$arElement) {
            return BeginNote() . Loc::getMessage('ITS_PICTURE_PIN_PROPERTY_NEW_ERROR') . EndNote();
        }

        $arResize = \CFile::ResizeImageGet(
            $arElement['DETAIL_PICTURE'],
            [
                'width' => 400,
                'height' => 400,
            ],
            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
            true
        );

        if (!$arResize) {
            return BeginNote() . Loc::getMessage('ITS_PICTURE_PIN_PROPERTY_PICT_ERROR') . EndNote();
        }

        $arResize['ratio'] = $arResize['height'] / $arResize['width'];
        $arResize['width'] = 228;
        $arResize['height'] = $arResize['width'] * $arResize['ratio'];

        ob_start();

        ?>
        <div class="admin-position-wrap">
            <template id="jsTemplatePositionProperty<?=$arProperty['ID']?>">
                <?=static::getRowTemplate($arResize, strval($strHTMLControlName['VALUE']) . '[NN]')?>
            </template>
            <?php

            $values = json_decode($value['VALUE'], true);

            if (is_array($values)) {
                $counter = 0;
                foreach ($values as $arValue) {
                    if (empty($arValue['COORDS'])) {
                        continue;
                    }
                    echo static::getRowTemplate(
                        $arResize,
                        strval($strHTMLControlName['VALUE']) . "[$counter]",
                        strval($arValue['COORDS']),
                        strval($arValue['NAME']),
                        strval($arValue['DESCRIPTION'])
                    );
                    $counter++;
                }
            }
            ?>
            <div>
                <button class="adm-btn" type="button" onclick="addAdminPositionInput<?=$arProperty['ID']?>(this)" style="min-width: 100px;">
                    <?=Loc::getMessage('ITS_PICTURE_PIN_PROPERTY_ADD')?>
                </button>
            </div>
            <script>
                function addAdminPositionInput<?=$arProperty['ID']?>(button) {
                    let n = $(button).parents('.admin-position-wrap').find('.admin-position').length;
                    $(button).parent().before(
                        $('#jsTemplatePositionProperty<?=$arProperty['ID']?>').html().replace(/NN/g, n)
                    );
                    if (document.detailPicturePath) {
                        $('.admin-position-box').css('background-image', `url(${document.detailPicturePath})`);
                    }
                    changeAdminPositionInput<?=$arProperty['ID']?>();
                }
                function changeAdminPositionInput<?=$arProperty['ID']?>() {
                    $('.admin-position-box input:checked').each(function () {
                        $(this).parents('.admin-position').find('.admin-position-value').html(
                            '&darr;' + $(this).val().split(':').join('%&nbsp;&rarr;') + '%'
                        );
                    })
                }
                $(document).on('change', '.admin-position-box input', changeAdminPositionInput<?=$arProperty['ID']?>);
                changeAdminPositionInput<?=$arProperty['ID']?>();
            </script>
            <style>
                .admin-position {
                    box-sizing: border-box;
                    position: relative;
                    display: block;
                    margin-bottom: 10px;
                    background: #e0e8ea;
                    border-radius: 3px;
                    border: 1px solid;
                    border-color: #87919c #959ea9 #9ea7b1 #959ea9;
                    width: 500px;
                    font-size: 0;
                    line-height: 0;
                }
                .admin-position * {
                    box-sizing: border-box;
                }
                .admin-position-left {
                    vertical-align: top;
                    display: block;
                    width: 238px;
                    padding: 5px;
                    float:left;
                }
                .admin-position-right {
                    vertical-align: top;
                    display: block;
                    width: 260px;
                    padding: 5px;
                    float:right;
                }
                .admin-position-clear {
                    display:block;
                    clear:both;
                }
                .admin-position-value {
                    display: block;
                    margin-top: 8px;
                    margin-bottom: 10px;
                    font-size: 14px;
                    line-height: 1;
                }
                .admin-position-name {
                    display: block;
                    margin-bottom: 5px;
                }
                .admin-position-description {
                    display: block;
                    margin-bottom: 5px;
                }
                .admin-position-remove {
                    font-size: 14px;
                    line-height: 1;;
                }
                .admin-position-name textarea,
                .admin-position-description textarea{
                    width: 100%;
                }
                .admin-position-box {
                    position: relative;
                    background-color: white;
                    background-position: center center;
                    background-size: 226px;
                    display: block;
                    border: 1px solid;
                    border-color: #87919c #959ea9 #9ea7b1 #959ea9;
                    width: 228px;
                    height: 153px;
                    font-size: 0 !important;
                    line-height: 0 !important;
                    border-radius: 3px;
                }
                .admin-position-box input {
                    vertical-align: top;
                    display: inline-block;
                    width: <?=(100 / 21)?>%;
                    height: <?=(100 / 21)?>%;
                    margin: 0;
                    padding: 0;
                    border: 0;
                    opacity: 0;
                    position: relative;
                    z-index: 2;
                }
                .admin-position-box i {
                    display: none;
                }
                .admin-position-box input:checked + i {
                    vertical-align: top;
                    display: inline-block;
                    width: <?=(100 / 21 - 1)?>%;
                    margin-left: -<?=(100 / 21 - 1)?>%;
                    border-radius: 50%;
                    box-shadow: 0 0 0 3px red;
                    background-color: white;
                    position: relative;
                    z-index: 3;
                }
                .admin-position-box input:checked + i:before {
                    content: '';
                    padding-top: 100%;
                    display: block;
                }
            </style>
        </div>
        <?php
        return ob_get_clean();
    }

    public function getAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return $value['VALUE'];
    }

    public function getSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        return '';
    }

    public function getPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return '';
    }

    public function convertToDB($arProperty, $value)
    {
        $result = [];

        if (!is_array($value['VALUE'])) {
            $value['VALUE'] = json_decode($value['VALUE'], true);
        }

        foreach ($value['VALUE'] as $arPin) {
            if (empty($arPin['COORDS'])) {
                continue;
            }
            $result[] = $arPin;
        }

        return ['VALUE' => json_encode($result)];
    }
}
