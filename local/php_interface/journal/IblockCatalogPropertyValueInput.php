<?php

namespace Journal;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Localization\Loc;
use Its\Library\Iblock\Iblock;
use Bitrix\Iblock\PropertyEnumerationTable;

Loc::loadMessages(__FILE__);

class IblockCatalogPropertyValueInput
{
    protected static array $properties = [];

    public function getUserTypeDescription()
    {
        global $APPLICATION;

        ob_start();
        ?>
        <script data-skip-moving="true">
            function select_iblock_property_catalog_property_value_input(select, init) {
                let propId = $(select).val(),
                    $child = $(`#${$(select).attr('data-child')}`),
                    $values = $child.find('option');

                if (!init) {
                    $values.hide()
                }

                $values.filter(`[data-property="${propId}"]`).show();
                $values.filter('[data-property="0"]').show();

                if (!init) {
                    $child.val(0);
                }
            }
            $(document).ready(function () {
                $('.iblock_property_catalog_property_value_input').each(function () {
                    select_iblock_property_catalog_property_value_input(this, true);
                });
                $(document).on('change', '.iblock_property_catalog_property_value_input', function () {
                    select_iblock_property_catalog_property_value_input(this, false);
                });
            });
        </script>
        <?php
        $APPLICATION->AddHeadString(ob_get_clean());

        return array(
            'PROPERTY_TYPE' => PropertyTable::TYPE_NUMBER,
            'USER_TYPE' => 'iblock_property_catalog_property_value_input',
            'DESCRIPTION' => Loc::getMessage('IBLOCK_CATALOG_PROPERTY_VALUE_INPUT_NAME'),

            'GetPropertyFieldHtml' => [__CLASS__, 'getFieldHtml'],
            'GetAdminListViewHTML' => [__CLASS__,'getListValue'],
            'GetPublicViewHTML' => [__CLASS__, 'getPublicValue'],
            'ConvertToDB' => [__CLASS__, 'convertToDB'],
            'GetSettingsHTML' => [__CLASS__, 'getSettings'],
        );
    }

    protected static function getIblockIds(): array
    {
        $iblock = Iblock::getInstance();
        return array_merge(
            $iblock->getAll('catalog'),
            $iblock->getAll('offers')
        );
    }

    protected static function getAllProperties(): array
    {
        if (empty(static::$properties)) {
            $resource = PropertyEnumerationTable::getList([
                'filter' => [
                    '=PROPERTY.IBLOCK_ID' => static::getIblockIds() ?: false,
                ],
                'select' => [
                    'ID',
                    'VALUE',
                    'PROPERTY_ID',
                    'PROPERTY_NAME' => 'PROPERTY.NAME',
                    'IBLOCK_ID' => 'PROPERTY.IBLOCK_ID',
                ],
                'order' => [
                    'PROPERTY.IBLOCK_ID' => 'ASC',
                    'PROPERTY_ID' => 'ASC',
                    'ID' => 'ASC',
                ],
            ]);
            while ($valueData = $resource->fetch()) {
                static::$properties[$valueData['PROPERTY_ID']]['ID'] = $valueData['PROPERTY_ID'];
                static::$properties[$valueData['PROPERTY_ID']]['NAME'] = "[{$valueData['IBLOCK_ID']}_{$valueData['PROPERTY_ID']}] {$valueData['PROPERTY_NAME']}";
                static::$properties[$valueData['PROPERTY_ID']]['VALUES'][$valueData['ID']] = [
                    'ID' => $valueData['ID'],
                    'VALUE' => "[{$valueData['ID']}] {$valueData['VALUE']}",
                ];
            }
        }

        return static::$properties;
    }

    protected static function getViewByEnumId($id): string
    {
        if (!$id) {
            return '';
        }

        foreach (static::getAllProperties() as $propertyData) {
            foreach ($propertyData['VALUES'] as $variantData) {
                if ($variantData['ID'] == $id) {
                    return "{$propertyData['NAME']} {$variantData['VALUE']}";
                }
            }
        }

        return '';
    }

    public function getFieldHtml($propertyData, $valueData, $controlData): string
    {
        \CJSCore::Init(['jquery3']);
        $controlData['VALUE_ID'] = preg_replace('/[^a-zA-Z0-9_]/', '_', $controlData['VALUE']);

        ob_start();

        ?>
        <div style="margin-bottom: 5px">
            <select name="<?=$controlData['VALUE']?>_PROP" id="<?=$controlData['VALUE_ID']?>_PROP" data-child="<?=$controlData['VALUE_ID']?>" class="typeselect iblock_property_catalog_property_value_input" style="width:400px">
                <option value="0"><?=Loc::getMessage('IBLOCK_CATALOG_PROPERTY_VALUE_INPUT_NO_SELECT')?></option>
                <?php
                foreach (static::getAllProperties() as $propertyData) {
                    ?>
                    <option value="<?=$propertyData['ID']?>" <?=(in_array($valueData['VALUE'], array_keys($propertyData['VALUES'])) ? 'selected' : '')?>>
                        <?=$propertyData['NAME']?>
                    </option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div>
        </div>
            <select name="<?=$controlData['VALUE']?>" id="<?=$controlData['VALUE_ID']?>" class="typeselect" style="width:400px">
                <option value="0"><?=Loc::getMessage('IBLOCK_CATALOG_PROPERTY_VALUE_INPUT_NO_SELECT')?></option>
                <?php
                foreach (static::getAllProperties() as $propertyData) {
                    foreach ($propertyData['VALUES'] as $variantData) {
                        ?>
                        <option data-property="<?=$propertyData['ID']?>" value="<?=$variantData['ID']?>" <?=($valueData['VALUE'] == $variantData['ID'] ? 'selected' : 'style="display:none"')?>>
                            <?=$variantData['VALUE']?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>
        <?php

        return ob_get_clean();
    }

    public static function getListValue($arProperty, $value, $strHTMLControlName): string
    {
        return static::getViewByEnumId($value['VALUE']);
    }

    public static function getPublicValue($arProperty, $value, $strHTMLControlName)
    {
        return static::getViewByEnumId($value['VALUE']);
    }

    public static function convertToDb($arProperty, $value)
    {
        return $value['VALUE'];
    }

    public function getSettings($arProperty, $strHTMLControlName, &$arPropertyFields): string
    {
        return '';
    }
}
