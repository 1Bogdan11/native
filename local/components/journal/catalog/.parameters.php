<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arCurrentValues */

/** @global CUserTypeManager $USER_FIELD_MANAGER */

use Bitrix\Main\Config\Option,
    Bitrix\Main\Loader,
    Bitrix\Main\ModuleManager,
    Bitrix\Iblock,
    Bitrix\Catalog,
    Bitrix\Currency;

global $USER_FIELD_MANAGER;

if (!Loader::includeModule('iblock'))
    return;
$catalogIncluded = Loader::includeModule('catalog');

$usePropertyFeatures = Iblock\Model\PropertyFeature::isEnabledFeatures();

$iblockExists = (!empty($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0);

$compatibleMode = !(isset($arCurrentValues['COMPATIBLE_MODE']) && $arCurrentValues['COMPATIBLE_MODE'] === 'N');

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$offersIblock = [];
if ($catalogIncluded) {
    $iterator = Catalog\CatalogIblockTable::getList([
        'select' => ['IBLOCK_ID'],
        'filter' => ['!=PRODUCT_IBLOCK_ID' => 0],
    ]);
    while ($row = $iterator->fetch())
        $offersIblock[$row['IBLOCK_ID']] = true;
    unset($row, $iterator);
}

$arIBlock = [];
$iblockFilter = (
!empty($arCurrentValues['IBLOCK_TYPE'])
    ? ['TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y']
    : ['ACTIVE' => 'Y']
);
$rsIBlock = CIBlock::GetList(['SORT' => 'ASC'], $iblockFilter);
while ($arr = $rsIBlock->Fetch()) {
    $id = (int)$arr['ID'];
    if (isset($offersIblock[$id]))
        continue;
    $arIBlock[$id] = '[' . $id . '] ' . $arr['NAME'];
}
unset($id, $arr, $rsIBlock, $iblockFilter);
unset($offersIblock);

$arProperty = [];
$arProperty_N = [];
$arProperty_X = [];
$arProperty_F = [];
if ($iblockExists) {
    $propertyIterator = Iblock\PropertyTable::getList([
        'select' => ['ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'LINK_IBLOCK_ID', 'USER_TYPE', 'SORT'],
        'filter' => ['=IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], '=ACTIVE' => 'Y'],
        'order' => ['SORT' => 'ASC', 'NAME' => 'ASC'],
    ]);
    while ($property = $propertyIterator->fetch()) {
        $propertyCode = (string)$property['CODE'];
        if ($propertyCode == '')
            $propertyCode = $property['ID'];
        $propertyName = '[' . $propertyCode . '] ' . $property['NAME'];

        if ($property['PROPERTY_TYPE'] != Iblock\PropertyTable::TYPE_FILE) {
            $arProperty[$propertyCode] = $propertyName;

            if ($property['MULTIPLE'] == 'Y')
                $arProperty_X[$propertyCode] = $propertyName;
            else if ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST)
                $arProperty_X[$propertyCode] = $propertyName;
            else if ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_ELEMENT && (int)$property['LINK_IBLOCK_ID'] > 0)
                $arProperty_X[$propertyCode] = $propertyName;
        } else {
            if ($property['MULTIPLE'] == 'N')
                $arProperty_F[$propertyCode] = $propertyName;
        }

        if ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_NUMBER)
            $arProperty_N[$propertyCode] = $propertyName;
    }
    unset($propertyCode, $propertyName, $property, $propertyIterator);
}
$arProperty_LNS = $arProperty;

$arIBlock_LINK = [];
$iblockFilter = (
!empty($arCurrentValues['LINK_IBLOCK_TYPE'])
    ? ['TYPE' => $arCurrentValues['LINK_IBLOCK_TYPE'], 'ACTIVE' => 'Y']
    : ['ACTIVE' => 'Y']
);
$rsIblock = CIBlock::GetList(['SORT' => 'ASC'], $iblockFilter);
while ($arr = $rsIblock->Fetch())
    $arIBlock_LINK[$arr['ID']] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
unset($iblockFilter);

$arProperty_LINK = [];
if (!empty($arCurrentValues['LINK_IBLOCK_ID']) && (int)$arCurrentValues['LINK_IBLOCK_ID'] > 0) {
    $propertyIterator = Iblock\PropertyTable::getList([
        'select' => ['ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'LINK_IBLOCK_ID', 'USER_TYPE', 'SORT'],
        'filter' => ['=IBLOCK_ID' => $arCurrentValues['LINK_IBLOCK_ID'], '=PROPERTY_TYPE' => Iblock\PropertyTable::TYPE_ELEMENT, '=ACTIVE' => 'Y'],
        'order' => ['SORT' => 'ASC', 'NAME' => 'ASC'],
    ]);
    while ($property = $propertyIterator->fetch()) {
        $propertyCode = (string)$property['CODE'];
        if ($propertyCode == '')
            $propertyCode = $property['ID'];
        $arProperty_LINK[$propertyCode] = '[' . $propertyCode . '] ' . $property['NAME'];
    }
    unset($propertyCode, $property, $propertyIterator);
}

$arUserFields_S = ["-" => " "];
$arUserFields_F = ["-" => " "];
if ($iblockExists) {
    $arUserFields = $USER_FIELD_MANAGER->GetUserFields('IBLOCK_' . $arCurrentValues['IBLOCK_ID'] . '_SECTION', 0, LANGUAGE_ID);
    foreach ($arUserFields as $FIELD_NAME => $arUserField) {
        $arUserField['LIST_COLUMN_LABEL'] = (string)$arUserField['LIST_COLUMN_LABEL'];
        $arProperty_UF[$FIELD_NAME] = $arUserField['LIST_COLUMN_LABEL'] ? '[' . $FIELD_NAME . ']' . $arUserField['LIST_COLUMN_LABEL'] : $FIELD_NAME;
        if ($arUserField["USER_TYPE"]["BASE_TYPE"] == "string")
            $arUserFields_S[$FIELD_NAME] = $arProperty_UF[$FIELD_NAME];
        if ($arUserField["USER_TYPE"]["BASE_TYPE"] == "file" && $arUserField['MULTIPLE'] == 'N')
            $arUserFields_F[$FIELD_NAME] = $arProperty_UF[$FIELD_NAME];
    }
    unset($arUserFields);
}

$offers = false;
$arProperty_Offers = [];
$arProperty_OffersWithoutFile = [];
if ($catalogIncluded && $iblockExists) {
    $offers = CCatalogSku::GetInfoByProductIBlock($arCurrentValues['IBLOCK_ID']);
    if (!empty($offers)) {
        $propertyIterator = Iblock\PropertyTable::getList([
            'select' => ['ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'LINK_IBLOCK_ID', 'USER_TYPE', 'SORT'],
            'filter' => ['=IBLOCK_ID' => $offers['IBLOCK_ID'], '=ACTIVE' => 'Y', '!=ID' => $offers['SKU_PROPERTY_ID']],
            'order' => ['SORT' => 'ASC', 'NAME' => 'ASC'],
        ]);
        while ($property = $propertyIterator->fetch()) {
            $propertyCode = (string)$property['CODE'];
            if ($propertyCode == '')
                $propertyCode = $property['ID'];
            $propertyName = '[' . $propertyCode . '] ' . $property['NAME'];

            $arProperty_Offers[$propertyCode] = $propertyName;
            if ($property['PROPERTY_TYPE'] != Iblock\PropertyTable::TYPE_FILE)
                $arProperty_OffersWithoutFile[$propertyCode] = $propertyName;
        }
        unset($propertyCode, $propertyName, $property, $propertyIterator);
    }
}

$arSort = CIBlockParameters::GetElementSortFields(
    ['SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'],
    ['KEY_LOWERCASE' => 'Y']
);

$arPrice = [];
if ($catalogIncluded) {
    $arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
    if (isset($arSort['CATALOG_AVAILABLE']))
        unset($arSort['CATALOG_AVAILABLE']);
    $arPrice = CCatalogIBlockParameters::getPriceTypesList();
} else {
    $arPrice = $arProperty_N;
}

$arAscDesc = [
    "asc" => GetMessage("IBLOCK_SORT_ASC"),
    "desc" => GetMessage("IBLOCK_SORT_DESC"),
];

$arComponentParameters = [
    "GROUPS" => [
        "FILTER_SETTINGS" => [
            "NAME" => GetMessage("T_IBLOCK_DESC_FILTER_SETTINGS"),
        ],
        "REVIEW_SETTINGS" => [
            "NAME" => GetMessage("T_IBLOCK_DESC_REVIEW_SETTINGS"),
        ],
        "ACTION_SETTINGS" => [
            "NAME" => GetMessage('IBLOCK_ACTIONS'),
        ],
        "COMPARE_SETTINGS" => [
            "NAME" => GetMessage("T_IBLOCK_DESC_COMPARE_SETTINGS_EXT"),
        ],
        "PRICES" => [
            "NAME" => GetMessage("IBLOCK_PRICES"),
        ],
        "BASKET" => [
            "NAME" => GetMessage("IBLOCK_BASKET"),
        ],
        "SEARCH_SETTINGS" => [
            "NAME" => GetMessage("T_IBLOCK_DESC_SEARCH_SETTINGS"),
        ],
        "TOP_SETTINGS" => [
            "NAME" => GetMessage("T_IBLOCK_DESC_TOP_SETTINGS"),
        ],
        "SECTIONS_SETTINGS" => [
            "NAME" => GetMessage("CP_BC_SECTIONS_SETTINGS"),
        ],
        "LIST_SETTINGS" => [
            "NAME" => GetMessage("T_IBLOCK_DESC_LIST_SETTINGS"),
        ],
        "DETAIL_SETTINGS" => [
            "NAME" => GetMessage("T_IBLOCK_DESC_DETAIL_SETTINGS"),
        ],
        "LINK" => [
            "NAME" => GetMessage("IBLOCK_LINK"),
        ],
        "ALSO_BUY_SETTINGS" => [
            "NAME" => GetMessage("T_IBLOCK_DESC_ALSO_BUY_SETTINGS"),
        ],
        "GIFTS_SETTINGS" => [
            "NAME" => GetMessage("SALE_T_DESC_GIFTS_SETTINGS"),
        ],
        "STORE_SETTINGS" => [
            "NAME" => GetMessage("T_IBLOCK_DESC_STORE_SETTINGS"),
        ],
        "OFFERS_SETTINGS" => [
            "NAME" => GetMessage("CP_BC_OFFERS_SETTINGS"),
        ],
        "BIG_DATA_SETTINGS" => [
            "NAME" => GetMessage("CP_BC_GROUP_BIG_DATA_SETTINGS"),
        ],
        'ANALYTICS_SETTINGS' => [
            'NAME' => GetMessage('ANALYTICS_SETTINGS'),
        ],
        "EXTENDED_SETTINGS" => [
            "NAME" => GetMessage("IBLOCK_EXTENDED_SETTINGS"),
            "SORT" => 10000,
        ],
    ],
    "PARAMETERS" => [
        "USER_CONSENT" => [],
        "VARIABLE_ALIASES" => [
            "ELEMENT_ID" => [
                "NAME" => GetMessage("CP_BC_VARIABLE_ALIASES_ELEMENT_ID"),
            ],
            "SECTION_ID" => [
                "NAME" => GetMessage("CP_BC_VARIABLE_ALIASES_SECTION_ID"),
            ],

        ],
        "AJAX_MODE" => [],
        "SEF_MODE" => [
            "sections" => [
                "NAME" => GetMessage("SECTIONS_TOP_PAGE"),
                "DEFAULT" => "",
                "VARIABLES" => [
                ],
            ],
            "section" => [
                "NAME" => GetMessage("SECTION_PAGE"),
                "DEFAULT" => "#SECTION_ID#/",
                "VARIABLES" => [
                    "SECTION_ID",
                    "SECTION_CODE",
                    "SECTION_CODE_PATH",
                ],
            ],
            "element" => [
                "NAME" => GetMessage("DETAIL_PAGE"),
                "DEFAULT" => "#SECTION_ID#/#ELEMENT_ID#/",
                "VARIABLES" => [
                    "ELEMENT_ID",
                    "ELEMENT_CODE",
                    "SECTION_ID",
                    "SECTION_CODE",
                    "SECTION_CODE_PATH",
                ],
            ],
            "compare" => [
                "NAME" => GetMessage("COMPARE_PAGE"),
                "DEFAULT" => "compare.php?action=#ACTION_CODE#",
                "VARIABLES" => [
                    "action",
                ],
            ],
        ],
        "IBLOCK_TYPE" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlockType,
            "REFRESH" => "Y",
        ],
        "IBLOCK_ID" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_IBLOCK"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
        ],
        "USE_FILTER" => [
            "PARENT" => "FILTER_SETTINGS",
            "NAME" => GetMessage("T_IBLOCK_DESC_USE_FILTER"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "Y",
        ],
        "USE_REVIEW" => [
            "PARENT" => "REVIEW_SETTINGS",
            "NAME" => GetMessage("T_IBLOCK_DESC_USE_REVIEW"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "Y",
        ],
        "USE_COMPARE" => [
            "PARENT" => "COMPARE_SETTINGS",
            "NAME" => GetMessage("T_IBLOCK_DESC_USE_COMPARE_EXT"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "Y",
        ],
        "SHOW_TOP_ELEMENTS" => [
            "PARENT" => "TOP_SETTINGS",
            "NAME" => GetMessage("NC_P_SHOW_TOP_ELEMENTS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "REFRESH" => "Y",
        ],
        "SECTION_COUNT_ELEMENTS" => [
            "PARENT" => "SECTIONS_SETTINGS",
            "NAME" => GetMessage('CP_BC_SECTION_COUNT_ELEMENTS'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "SECTION_TOP_DEPTH" => [
            "PARENT" => "SECTIONS_SETTINGS",
            "NAME" => GetMessage('CP_BC_SECTION_TOP_DEPTH'),
            "TYPE" => "STRING",
            "DEFAULT" => "2",
        ],
        "PAGE_ELEMENT_COUNT" => [
            "PARENT" => "LIST_SETTINGS",
            "NAME" => GetMessage("IBLOCK_PAGE_ELEMENT_COUNT"),
            "TYPE" => "STRING",
            'HIDDEN' => isset($templateProperties['LIST_PRODUCT_ROW_VARIANTS']) ? 'Y' : 'N',
            "DEFAULT" => "30",
        ],
        "LINE_ELEMENT_COUNT" => [
            "PARENT" => "LIST_SETTINGS",
            "NAME" => GetMessage("IBLOCK_LINE_ELEMENT_COUNT"),
            "TYPE" => "STRING",
            'HIDDEN' => isset($templateProperties['LIST_PRODUCT_ROW_VARIANTS']) ? 'Y' : 'N',
            "DEFAULT" => "3",
        ],
        "ELEMENT_SORT_FIELD" => [
            "PARENT" => "LIST_SETTINGS",
            "NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
            "TYPE" => "LIST",
            "VALUES" => $arSort,
            "ADDITIONAL_VALUES" => "Y",
            "DEFAULT" => "sort",
        ],
        "ELEMENT_SORT_ORDER" => [
            "PARENT" => "LIST_SETTINGS",
            "NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
            "TYPE" => "LIST",
            "VALUES" => $arAscDesc,
            "DEFAULT" => "asc",
            "ADDITIONAL_VALUES" => "Y",
        ],
        "ELEMENT_SORT_FIELD2" => [
            "PARENT" => "LIST_SETTINGS",
            "NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD2"),
            "TYPE" => "LIST",
            "VALUES" => $arSort,
            "ADDITIONAL_VALUES" => "Y",
            "DEFAULT" => "id",
        ],
        "ELEMENT_SORT_ORDER2" => [
            "PARENT" => "LIST_SETTINGS",
            "NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER2"),
            "TYPE" => "LIST",
            "VALUES" => $arAscDesc,
            "DEFAULT" => "desc",
            "ADDITIONAL_VALUES" => "Y",
        ],
        "LIST_PROPERTY_CODE" => [
            "PARENT" => "LIST_SETTINGS",
            "NAME" => GetMessage("IBLOCK_PROPERTY"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            'REFRESH' => isset($templateProperties['LIST_PROPERTY_CODE_MOBILE']) ? 'Y' : 'N',
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arProperty_LNS,
        ],
        'LIST_PROPERTY_CODE_MOBILE' => [],
        "INCLUDE_SUBSECTIONS" => [
            "PARENT" => "LIST_SETTINGS",
            "NAME" => GetMessage("CP_BC_INCLUDE_SUBSECTIONS"),
            "TYPE" => "LIST",
            "VALUES" => [
                "Y" => GetMessage('CP_BC_INCLUDE_SUBSECTIONS_ALL'),
                "A" => GetMessage('CP_BC_INCLUDE_SUBSECTIONS_ACTIVE'),
                "N" => GetMessage('CP_BC_INCLUDE_SUBSECTIONS_NO'),
            ],
            "DEFAULT" => "Y",
        ],
        "USE_MAIN_ELEMENT_SECTION" => [
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("CP_BC_USE_MAIN_ELEMENT_SECTION"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        "DETAIL_STRICT_SECTION_CHECK" => [
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("CP_BC_DETAIL_STRICT_SECTION_CHECK"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        "LIST_META_KEYWORDS" => [
            "PARENT" => "LIST_SETTINGS",
            "NAME" => GetMessage("CP_BC_LIST_META_KEYWORDS"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arUserFields_S,
        ],
        "LIST_META_DESCRIPTION" => [
            "PARENT" => "LIST_SETTINGS",
            "NAME" => GetMessage("CP_BC_LIST_META_DESCRIPTION"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arUserFields_S,
        ],
        "LIST_BROWSER_TITLE" => [
            "PARENT" => "LIST_SETTINGS",
            "NAME" => GetMessage("CP_BC_LIST_BROWSER_TITLE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "DEFAULT" => "-",
            "VALUES" => array_merge(["-" => " ", "NAME" => GetMessage("IBLOCK_FIELD_NAME")], $arUserFields_S),
        ],
        "SECTION_BACKGROUND_IMAGE" => [
            "PARENT" => "LIST_SETTINGS",
            "NAME" => GetMessage("CP_BC_BACKGROUND_IMAGE"),
            "TYPE" => "LIST",
            "DEFAULT" => "-",
            "MULTIPLE" => "N",
            "VALUES" => array_merge(["-" => " "], $arUserFields_F),
        ],
        "DETAIL_PROPERTY_CODE" => [
            "PARENT" => "DETAIL_SETTINGS",
            "NAME" => GetMessage("IBLOCK_PROPERTY"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arProperty_LNS,
        ],
        "DETAIL_META_KEYWORDS" => [
            "PARENT" => "DETAIL_SETTINGS",
            "NAME" => GetMessage("CP_BC_DETAIL_META_KEYWORDS"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => array_merge(["-" => " "], $arProperty_LNS),
        ],
        "DETAIL_META_DESCRIPTION" => [
            "PARENT" => "DETAIL_SETTINGS",
            "NAME" => GetMessage("CP_BC_DETAIL_META_DESCRIPTION"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => array_merge(["-" => " "], $arProperty_LNS),
        ],
        "DETAIL_BROWSER_TITLE" => [
            "PARENT" => "DETAIL_SETTINGS",
            "NAME" => GetMessage("CP_BC_DETAIL_BROWSER_TITLE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "DEFAULT" => "-",
            "VALUES" => array_merge(["-" => " ", "NAME" => GetMessage("IBLOCK_FIELD_NAME")], $arProperty_LNS),
        ],
        "DETAIL_SET_CANONICAL_URL" => [
            "PARENT" => "DETAIL_SETTINGS",
            "NAME" => GetMessage("CP_BC_DETAIL_SET_CANONICAL_URL"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        "SECTION_ID_VARIABLE" => [
            "PARENT" => "DETAIL_SETTINGS",
            "NAME" => GetMessage("IBLOCK_SECTION_ID_VARIABLE"),
            "TYPE" => "STRING",
            "DEFAULT" => "SECTION_ID",
        ],
        "DETAIL_CHECK_SECTION_ID_VARIABLE" => [
            "PARENT" => "DETAIL_SETTINGS",
            "NAME" => GetMessage("CP_BC_DETAIL_CHECK_SECTION_ID_VARIABLE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        "DETAIL_BACKGROUND_IMAGE" => [
            "PARENT" => "DETAIL_SETTINGS",
            "NAME" => GetMessage("CP_BC_BACKGROUND_IMAGE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "DEFAULT" => "-",
            "VALUES" => array_merge(["-" => " "], $arProperty_F),
        ],

        "SHOW_DEACTIVATED" => [
            "PARENT" => "DETAIL_SETTINGS",
            "NAME" => GetMessage('CP_BC_SHOW_DEACTIVATED'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],

        "SHOW_SKU_DESCRIPTION" => [
            "PARENT" => "DETAIL_SETTINGS",
            "NAME" => GetMessage("IBLOCK_SHOW_SKU_DESCRIPTION"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        "CACHE_TIME" => ["DEFAULT" => 36000000],
        "CACHE_FILTER" => [
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => GetMessage("IBLOCK_CACHE_FILTER"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        "CACHE_GROUPS" => [
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => GetMessage("CP_BC_CACHE_GROUPS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "SET_LAST_MODIFIED" => [
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("CP_BC_SET_LAST_MODIFIED"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        "SET_TITLE" => [],
        "ADD_SECTIONS_CHAIN" => [
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("CP_BC_ADD_SECTIONS_CHAIN"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "ADD_ELEMENT_CHAIN" => [
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("CP_BC_ADD_ELEMENT_CHAIN"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        "PRICE_CODE" => [
            "PARENT" => "PRICES",
            "NAME" => GetMessage("IBLOCK_PRICE_CODE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arPrice,
        ],
        "USE_PRICE_COUNT" => [
            "PARENT" => "PRICES",
            "NAME" => GetMessage("IBLOCK_USE_PRICE_COUNT"),
            "TYPE" => "CHECKBOX",
            "REFRESH" => isset($templateProperties['USE_RATIO_IN_RANGES']) ? "Y" : "N",
            "DEFAULT" => "N",
        ],
        "SHOW_PRICE_COUNT" => [
            "PARENT" => "PRICES",
            "NAME" => GetMessage("IBLOCK_SHOW_PRICE_COUNT"),
            "TYPE" => "STRING",
            "DEFAULT" => "1",
        ],
        "PRICE_VAT_INCLUDE" => [
            "PARENT" => "PRICES",
            "NAME" => GetMessage("IBLOCK_VAT_INCLUDE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "PRICE_VAT_SHOW_VALUE" => [
            "PARENT" => "PRICES",
            "NAME" => GetMessage("IBLOCK_VAT_SHOW_VALUE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        "BASKET_URL" => [
            "PARENT" => "BASKET",
            "NAME" => GetMessage("IBLOCK_BASKET_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => "/personal/basket.php",
        ],
        "ACTION_VARIABLE" => [
            "PARENT" => "ACTION_SETTINGS",
            "NAME" => GetMessage("IBLOCK_ACTION_VARIABLE"),
            "TYPE" => "STRING",
            "DEFAULT" => "action",
        ],
        "PRODUCT_ID_VARIABLE" => [
            "PARENT" => "ACTION_SETTINGS",
            "NAME" => GetMessage("IBLOCK_PRODUCT_ID_VARIABLE"),
            "TYPE" => "STRING",
            "DEFAULT" => "id",
        ],
        "USE_PRODUCT_QUANTITY" => [
            "PARENT" => "BASKET",
            "NAME" => GetMessage("CP_BC_USE_PRODUCT_QUANTITY"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "Y",
        ],
        "PRODUCT_QUANTITY_VARIABLE" => [
            "PARENT" => "BASKET",
            "NAME" => GetMessage("CP_BC_PRODUCT_QUANTITY_VARIABLE"),
            "TYPE" => "STRING",
            "DEFAULT" => "quantity",
            "HIDDEN" => (isset($arCurrentValues['USE_PRODUCT_QUANTITY']) && $arCurrentValues['USE_PRODUCT_QUANTITY'] == 'Y' ? 'N' : 'Y'),
        ],
        "ADD_PROPERTIES_TO_BASKET" => [
            "PARENT" => "BASKET",
            "NAME" => GetMessage("CP_BC_ADD_PROPERTIES_TO_BASKET"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "REFRESH" => "Y",
        ],
        "PRODUCT_PROPS_VARIABLE" => [
            "PARENT" => "BASKET",
            "NAME" => GetMessage("CP_BC_PRODUCT_PROPS_VARIABLE"),
            "TYPE" => "STRING",
            "DEFAULT" => "prop",
            "HIDDEN" => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N'),
        ],
        "PARTIAL_PRODUCT_PROPERTIES" => [
            "PARENT" => "BASKET",
            "NAME" => GetMessage("CP_BC_PARTIAL_PRODUCT_PROPERTIES"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "HIDDEN" => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N'),
        ],
        "PRODUCT_PROPERTIES" => [
            "PARENT" => "BASKET",
            "NAME" => GetMessage("CP_BC_PRODUCT_PROPERTIES"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_X,
            "HIDDEN" => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N'),
        ],
        "LINK_IBLOCK_TYPE" => [
            "PARENT" => "LINK",
            "NAME" => GetMessage("IBLOCK_LINK_IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlockType,
            "REFRESH" => "Y",
        ],
        "LINK_IBLOCK_ID" => [
            "PARENT" => "LINK",
            "NAME" => GetMessage("IBLOCK_LINK_IBLOCK_ID"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlock_LINK,
            "REFRESH" => "Y",
        ],
        "LINK_PROPERTY_SID" => [
            "PARENT" => "LINK",
            "NAME" => GetMessage("IBLOCK_LINK_PROPERTY_SID"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arProperty_LINK,
        ],
        "LINK_ELEMENTS_URL" => [
            "PARENT" => "LINK",
            "NAME" => GetMessage("IBLOCK_LINK_ELEMENTS_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
        ],

        "USE_ALSO_BUY" => [
            "PARENT" => "ALSO_BUY_SETTINGS",
            "NAME" => GetMessage("T_IBLOCK_DESC_USE_ALSO_BUY"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "Y",
        ],

        "USE_GIFTS_DETAIL" => [
            "PARENT" => "GIFTS_SETTINGS",
            "NAME" => GetMessage("SALE_T_DESC_USE_GIFTS_DETAIL"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "REFRESH" => "Y",
        ],

        "USE_GIFTS_SECTION" => [
            "PARENT" => "GIFTS_SETTINGS",
            "NAME" => GetMessage("SALE_T_DESC_USE_GIFTS_SECTION_LIST"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "REFRESH" => "Y",
        ],

        "USE_GIFTS_MAIN_PR_SECTION_LIST" => [
            "PARENT" => "GIFTS_SETTINGS",
            "NAME" => GetMessage("SALE_T_DESC_USE_GIFTS_MAIN_PR_DETAIL"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "REFRESH" => "Y",
        ],

        "USE_STORE" => [
            "PARENT" => "STORE_SETTINGS",
            "NAME" => GetMessage("T_IBLOCK_DESC_USE_STORE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "Y",
        ],
        'COMPATIBLE_MODE' => [
            'PARENT' => 'EXTENDED_SETTINGS',
            'NAME' => GetMessage('CP_BC_COMPATIBLE_MODE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
            'REFRESH' => 'Y',
        ],
        "USE_ELEMENT_COUNTER" => [
            "PARENT" => "EXTENDED_SETTINGS",
            "NAME" => GetMessage('CP_BC_USE_ELEMENT_COUNTER'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "DISABLE_INIT_JS_IN_COMPONENT" => [
            "PARENT" => "EXTENDED_SETTINGS",
            "NAME" => GetMessage('CP_BC_DISABLE_INIT_JS_IN_COMPONENT'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "HIDDEN" => (!$compatibleMode ? 'Y' : 'N'),
        ],
    ],
];

// hack for correct sort
if (isset($templateProperties['LIST_PROPERTY_CODE_MOBILE'])) {
    $arComponentParameters['PARAMETERS']['LIST_PROPERTY_CODE_MOBILE'] = $templateProperties['LIST_PROPERTY_CODE_MOBILE'];
    unset($templateProperties['LIST_PROPERTY_CODE_MOBILE']);
} else {
    unset($arComponentParameters['PARAMETERS']['LIST_PROPERTY_CODE_MOBILE']);
}

if ($usePropertyFeatures) {
    if (isset($arComponentParameters['PARAMETERS']['PRODUCT_PROPERTIES']))
        unset($arComponentParameters['PARAMETERS']['PRODUCT_PROPERTIES']);
    unset($arComponentParameters['PARAMETERS']['LIST_PROPERTY_CODE']);
    unset($arComponentParameters['PARAMETERS']['DETAIL_PROPERTY_CODE']);
}

CIBlockParameters::AddPagerSettings(
    $arComponentParameters,
    GetMessage("T_IBLOCK_DESC_PAGER_CATALOG"), //$pager_title
    true, //$bDescNumbering
    true, //$bShowAllParam
    true, //$bBaseLink
    $arCurrentValues["PAGER_BASE_LINK_ENABLE"] === "Y" //$bBaseLinkEnabled
);

CIBlockParameters::Add404Settings($arComponentParameters, $arCurrentValues);

if ($arCurrentValues["SEF_MODE"] == "Y") {
    $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"] = [];
    $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]["ELEMENT_ID"] = [
        "NAME" => GetMessage("CP_BC_VARIABLE_ALIASES_ELEMENT_ID"),
        "TEMPLATE" => "#ELEMENT_ID#",
    ];
    $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]["ELEMENT_CODE"] = [
        "NAME" => GetMessage("CP_BC_VARIABLE_ALIASES_ELEMENT_CODE"),
        "TEMPLATE" => "#ELEMENT_CODE#",
    ];
    $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]["SECTION_ID"] = [
        "NAME" => GetMessage("CP_BC_VARIABLE_ALIASES_SECTION_ID"),
        "TEMPLATE" => "#SECTION_ID#",
    ];
    $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]["SECTION_CODE"] = [
        "NAME" => GetMessage("CP_BC_VARIABLE_ALIASES_SECTION_CODE"),
        "TEMPLATE" => "#SECTION_CODE#",
    ];
    $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]["SECTION_CODE_PATH"] = [
        "NAME" => GetMessage("CP_BC_VARIABLE_ALIASES_SECTION_CODE_PATH"),
        "TEMPLATE" => "#SECTION_CODE_PATH#",
    ];
    $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]["SMART_FILTER_PATH"] = [
        "NAME" => GetMessage("CP_BC_VARIABLE_ALIASES_SMART_FILTER_PATH"),
        "TEMPLATE" => "#SMART_FILTER_PATH#",
    ];

    $smartBase = ($arCurrentValues["SEF_URL_TEMPLATES"]["section"] ? $arCurrentValues["SEF_URL_TEMPLATES"]["section"] : "#SECTION_ID#/");
    $arComponentParameters["PARAMETERS"]["SEF_MODE"]["smart_filter"] = [
        "NAME" => GetMessage("CP_BC_SEF_MODE_SMART_FILTER"),
        "DEFAULT" => $smartBase . "filter/#SMART_FILTER_PATH#/apply/",
        "VARIABLES" => [
            "SECTION_ID",
            "SECTION_CODE",
            "SECTION_CODE_PATH",
            "SMART_FILTER_PATH",
        ],
    ];
}

if ($arCurrentValues["USE_COMPARE"] == "Y") {
    $arComponentParameters["PARAMETERS"]["COMPARE_NAME"] = [
        "PARENT" => "COMPARE_SETTINGS",
        "NAME" => GetMessage("IBLOCK_COMPARE_NAME"),
        "TYPE" => "STRING",
        "DEFAULT" => "CATALOG_COMPARE_LIST",
    ];
    $arComponentParameters["PARAMETERS"]["COMPARE_FIELD_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("IBLOCK_FIELD"), "COMPARE_SETTINGS");
    $arComponentParameters["PARAMETERS"]["COMPARE_PROPERTY_CODE"] = [
        "PARENT" => "COMPARE_SETTINGS",
        "NAME" => GetMessage("IBLOCK_PROPERTY"),
        "TYPE" => "LIST",
        "MULTIPLE" => "Y",
        "VALUES" => $arProperty_LNS,
        "ADDITIONAL_VALUES" => "Y",
    ];
    if (!empty($offers)) {
        $arComponentParameters["PARAMETERS"]["COMPARE_OFFERS_FIELD_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("CP_BC_COMPARE_OFFERS_FIELD_CODE"), "COMPARE_SETTINGS");
        $arComponentParameters["PARAMETERS"]["COMPARE_OFFERS_PROPERTY_CODE"] = [
            "PARENT" => "COMPARE_SETTINGS",
            "NAME" => GetMessage("CP_BC_COMPARE_OFFERS_PROPERTY_CODE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_OffersWithoutFile,
            "ADDITIONAL_VALUES" => "Y",
        ];
    }
    $arComponentParameters["PARAMETERS"]["COMPARE_ELEMENT_SORT_FIELD"] = [
        "PARENT" => "COMPARE_SETTINGS",
        "NAME" => GetMessage("CP_BC_COMPARE_ELEMENT_SORT_FIELD"),
        "TYPE" => "LIST",
        "VALUES" => $arSort,
        "ADDITIONAL_VALUES" => "Y",
        "DEFAULT" => "sort",
    ];
    $arComponentParameters["PARAMETERS"]["COMPARE_ELEMENT_SORT_ORDER"] = [
        "PARENT" => "COMPARE_SETTINGS",
        "NAME" => GetMessage("CP_BC_COMPARE_ELEMENT_SORT_ORDER"),
        "TYPE" => "LIST",
        "VALUES" => $arAscDesc,
        "DEFAULT" => "asc",
        "ADDITIONAL_VALUES" => "Y",
    ];
    if ($compatibleMode) {
        $arComponentParameters["PARAMETERS"]["DISPLAY_ELEMENT_SELECT_BOX"] = [
            "PARENT" => "COMPARE_SETTINGS",
            "NAME" => GetMessage("T_IBLOCK_DESC_ELEMENT_BOX"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "Y",
        ];
        if (isset($arCurrentValues["DISPLAY_ELEMENT_SELECT_BOX"]) && $arCurrentValues["DISPLAY_ELEMENT_SELECT_BOX"] == "Y") {
            $arComponentParameters["PARAMETERS"]["ELEMENT_SORT_FIELD_BOX"] = [
                "PARENT" => "COMPARE_SETTINGS",
                "NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD_BOX"),
                "TYPE" => "LIST",
                "VALUES" => $arSort,
                "ADDITIONAL_VALUES" => "Y",
                "DEFAULT" => "name",
            ];
            $arComponentParameters["PARAMETERS"]["ELEMENT_SORT_ORDER_BOX"] = [
                "PARENT" => "COMPARE_SETTINGS",
                "NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER_BOX"),
                "TYPE" => "LIST",
                "VALUES" => $arAscDesc,
                "DEFAULT" => "asc",
                "ADDITIONAL_VALUES" => "Y",
            ];
            $arComponentParameters["PARAMETERS"]["ELEMENT_SORT_FIELD_BOX2"] = [
                "PARENT" => "COMPARE_SETTINGS",
                "NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD_BOX2"),
                "TYPE" => "LIST",
                "VALUES" => $arSort,
                "ADDITIONAL_VALUES" => "Y",
                "DEFAULT" => "id",
            ];
            $arComponentParameters["PARAMETERS"]["ELEMENT_SORT_ORDER_BOX2"] = [
                "PARENT" => "COMPARE_SETTINGS",
                "NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER_BOX2"),
                "TYPE" => "LIST",
                "VALUES" => $arAscDesc,
                "DEFAULT" => "desc",
                "ADDITIONAL_VALUES" => "Y",
            ];
        }
    }
    if ($usePropertyFeatures) {
        unset($arComponentParameters["PARAMETERS"]["COMPARE_PROPERTY_CODE"]);
        if (!empty($offers)) {
            unset($arComponentParameters["PARAMETERS"]["COMPARE_OFFERS_PROPERTY_CODE"]);
        }
    }
}

if (!empty($offers)) {
    $arComponentParameters["PARAMETERS"]["LIST_OFFERS_FIELD_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("CP_BC_LIST_OFFERS_FIELD_CODE"), "LIST_SETTINGS");
    if (!$usePropertyFeatures) {
        $arComponentParameters["PARAMETERS"]["LIST_OFFERS_PROPERTY_CODE"] = [
            "PARENT" => "LIST_SETTINGS",
            "NAME" => GetMessage("CP_BC_LIST_OFFERS_PROPERTY_CODE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_Offers,
            "ADDITIONAL_VALUES" => "Y",
        ];
    }
    $arComponentParameters["PARAMETERS"]["LIST_OFFERS_LIMIT"] = [
        "PARENT" => "LIST_SETTINGS",
        "NAME" => GetMessage("CP_BC_LIST_OFFERS_LIMIT"),
        "TYPE" => "STRING",
        "DEFAULT" => 5,
    ];

    $arComponentParameters["PARAMETERS"]["DETAIL_OFFERS_FIELD_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("CP_BC_DETAIL_OFFERS_FIELD_CODE"), "DETAIL_SETTINGS");
    if (!$usePropertyFeatures) {
        $arComponentParameters["PARAMETERS"]["DETAIL_OFFERS_PROPERTY_CODE"] = [
            "PARENT" => "DETAIL_SETTINGS",
            "NAME" => GetMessage("CP_BC_DETAIL_OFFERS_PROPERTY_CODE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_Offers,
            "ADDITIONAL_VALUES" => "Y",
        ];
    }
}

if ($arCurrentValues["SHOW_TOP_ELEMENTS"] != "N") {
    $arComponentParameters["PARAMETERS"]["TOP_ELEMENT_COUNT"] = [
        "PARENT" => "TOP_SETTINGS",
        "NAME" => GetMessage("CP_BC_TOP_ELEMENT_COUNT"),
        "TYPE" => "STRING",
        'HIDDEN' => isset($templateProperties['TOP_PRODUCT_ROW_VARIANTS']) ? 'Y' : 'N',
        "DEFAULT" => "9",
    ];
    $arComponentParameters["PARAMETERS"]["TOP_LINE_ELEMENT_COUNT"] = [
        "PARENT" => "TOP_SETTINGS",
        "NAME" => GetMessage("IBLOCK_LINE_ELEMENT_COUNT"),
        "TYPE" => "STRING",
        'HIDDEN' => isset($templateProperties['TOP_PRODUCT_ROW_VARIANTS']) ? 'Y' : 'N',
        "DEFAULT" => "3",
    ];
    $arComponentParameters["PARAMETERS"]["TOP_ELEMENT_SORT_FIELD"] = [
        "PARENT" => "TOP_SETTINGS",
        "NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
        "TYPE" => "LIST",
        "VALUES" => $arSort,
        "ADDITIONAL_VALUES" => "Y",
        "DEFAULT" => "sort",
    ];
    $arComponentParameters["PARAMETERS"]["TOP_ELEMENT_SORT_ORDER"] = [
        "PARENT" => "TOP_SETTINGS",
        "NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
        "TYPE" => "LIST",
        "VALUES" => $arAscDesc,
        "DEFAULT" => "asc",
        "ADDITIONAL_VALUES" => "Y",
    ];
    $arComponentParameters["PARAMETERS"]["TOP_ELEMENT_SORT_FIELD2"] = [
        "PARENT" => "TOP_SETTINGS",
        "NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD2"),
        "TYPE" => "LIST",
        "VALUES" => $arSort,
        "ADDITIONAL_VALUES" => "Y",
        "DEFAULT" => "id",
    ];
    $arComponentParameters["PARAMETERS"]["TOP_ELEMENT_SORT_ORDER2"] = [
        "PARENT" => "TOP_SETTINGS",
        "NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER2"),
        "TYPE" => "LIST",
        "VALUES" => $arAscDesc,
        "DEFAULT" => "desc",
        "ADDITIONAL_VALUES" => "Y",
    ];
    if (!$usePropertyFeatures) {
        $arComponentParameters["PARAMETERS"]["TOP_PROPERTY_CODE"] = [
            "PARENT" => "TOP_SETTINGS",
            "NAME" => GetMessage("BC_P_TOP_PROPERTY_CODE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            'REFRESH' => isset($templateProperties['TOP_PROPERTY_CODE_MOBILE']) ? 'Y' : 'N',
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arProperty,
        ];
    }

    if (isset($templateProperties['TOP_PROPERTY_CODE_MOBILE'])) {
        $arComponentParameters['PARAMETERS']['TOP_PROPERTY_CODE_MOBILE'] = $templateProperties['TOP_PROPERTY_CODE_MOBILE'];
        unset($templateProperties['TOP_PROPERTY_CODE_MOBILE']);
    }

    if (!empty($offers)) {
        $arComponentParameters["PARAMETERS"]["TOP_OFFERS_FIELD_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("CP_BC_TOP_OFFERS_FIELD_CODE"), "TOP_SETTINGS");
        if (!$usePropertyFeatures) {
            $arComponentParameters["PARAMETERS"]["TOP_OFFERS_PROPERTY_CODE"] = [
                "PARENT" => "TOP_SETTINGS",
                "NAME" => GetMessage("CP_BC_TOP_OFFERS_PROPERTY_CODE"),
                "TYPE" => "LIST",
                "MULTIPLE" => "Y",
                "VALUES" => $arProperty_Offers,
                "ADDITIONAL_VALUES" => "Y",
            ];
        }
        $arComponentParameters["PARAMETERS"]["TOP_OFFERS_LIMIT"] = [
            "PARENT" => "TOP_SETTINGS",
            "NAME" => GetMessage("CP_BC_TOP_OFFERS_LIMIT"),
            "TYPE" => "STRING",
            "DEFAULT" => 5,
        ];
    }
}
if ($arCurrentValues["USE_FILTER"] == "Y") {
    $arComponentParameters["PARAMETERS"]["FILTER_NAME"] = [
        "PARENT" => "FILTER_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_FILTER"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
    ];
    if ($compatibleMode) {
        $arComponentParameters["PARAMETERS"]["FILTER_FIELD_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("IBLOCK_FIELD"), "FILTER_SETTINGS");
        $arComponentParameters["PARAMETERS"]["FILTER_PROPERTY_CODE"] = [
            "PARENT" => "FILTER_SETTINGS",
            "NAME" => GetMessage("T_IBLOCK_PROPERTY"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNS,
            "ADDITIONAL_VALUES" => "Y",
        ];
        $arComponentParameters["PARAMETERS"]["FILTER_PRICE_CODE"] = [
            "PARENT" => "FILTER_SETTINGS",
            "NAME" => GetMessage("IBLOCK_PRICE_CODE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arPrice,
        ];
        if (!empty($offers)) {
            $arComponentParameters["PARAMETERS"]["FILTER_OFFERS_FIELD_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("CP_BC_FILTER_OFFERS_FIELD_CODE"), "FILTER_SETTINGS");
            $arComponentParameters["PARAMETERS"]["FILTER_OFFERS_PROPERTY_CODE"] = [
                "PARENT" => "FILTER_SETTINGS",
                "NAME" => GetMessage("CP_BC_FILTER_OFFERS_PROPERTY_CODE"),
                "TYPE" => "LIST",
                "MULTIPLE" => "Y",
                "VALUES" => $arProperty_OffersWithoutFile,
                "ADDITIONAL_VALUES" => "Y",
            ];
        }
    }
}

if ($compatibleMode) {
    if (!ModuleManager::isModuleInstalled('forum')) {
        unset($arComponentParameters["PARAMETERS"]["USE_REVIEW"]);
        unset($arComponentParameters["GROUPS"]["REVIEW_SETTINGS"]);
    } else if ($arCurrentValues["USE_REVIEW"] == "Y") {
        $arForumList = [];
        if (Loader::includeModule("forum")) {
            $rsForum = CForumNew::GetList();
            while ($arForum = $rsForum->Fetch())
                $arForumList[$arForum["ID"]] = $arForum["NAME"];
        }
        $arComponentParameters["PARAMETERS"]["MESSAGES_PER_PAGE"] = [
            "PARENT" => "REVIEW_SETTINGS",
            "NAME" => GetMessage("F_MESSAGES_PER_PAGE"),
            "TYPE" => "STRING",
            "DEFAULT" => (int)COption::GetOptionString("forum", "MESSAGES_PER_PAGE", "10"),
        ];
        $arComponentParameters["PARAMETERS"]["USE_CAPTCHA"] = [
            "PARENT" => "REVIEW_SETTINGS",
            "NAME" => GetMessage("F_USE_CAPTCHA"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ];
        $arComponentParameters["PARAMETERS"]["REVIEW_AJAX_POST"] = [
            "PARENT" => "REVIEW_SETTINGS",
            "NAME" => GetMessage("F_REVIEW_AJAX_POST"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ];
        $arComponentParameters["PARAMETERS"]["PATH_TO_SMILE"] = [
            "PARENT" => "REVIEW_SETTINGS",
            "NAME" => GetMessage("F_PATH_TO_SMILE"),
            "TYPE" => "STRING",
            "DEFAULT" => "/bitrix/images/forum/smile/",
        ];
        $arComponentParameters["PARAMETERS"]["FORUM_ID"] = [
            "PARENT" => "REVIEW_SETTINGS",
            "NAME" => GetMessage("F_FORUM_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arForumList,
            "DEFAULT" => "",
        ];
        $arComponentParameters["PARAMETERS"]["URL_TEMPLATES_READ"] = [
            "PARENT" => "REVIEW_SETTINGS",
            "NAME" => GetMessage("F_READ_TEMPLATE"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ];
        $arComponentParameters["PARAMETERS"]["SHOW_LINK_TO_FORUM"] = [
            "PARENT" => "REVIEW_SETTINGS",
            "NAME" => GetMessage("F_SHOW_LINK_TO_FORUM"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ];
    }
} else {
    unset($arComponentParameters["PARAMETERS"]["USE_REVIEW"]);
    unset($arComponentParameters["GROUPS"]["REVIEW_SETTINGS"]);
    unset($arComponentParameters["PARAMETERS"]["LIST_OFFERS_LIMIT"]);
    if (isset($arComponentParameters["PARAMETERS"]["TOP_OFFERS_LIMIT"]))
        unset($arComponentParameters["PARAMETERS"]["TOP_OFFERS_LIMIT"]);
}

if ($catalogIncluded && $arCurrentValues["USE_STORE"] == 'Y') {
    $arStore = [];
    $storeIterator = CCatalogStore::GetList(
        [],
        ['ISSUING_CENTER' => 'Y'],
        false,
        false,
        ['ID', 'TITLE']
    );
    while ($store = $storeIterator->GetNext())
        $arStore[$store['ID']] = "[" . $store['ID'] . "] " . $store['TITLE'];

    $userFields = $USER_FIELD_MANAGER->GetUserFields("CAT_STORE", 0, LANGUAGE_ID);
    $propertyUF = [];

    foreach ($userFields as $fieldName => $userField)
        $propertyUF[$fieldName] = $userField["LIST_COLUMN_LABEL"] ? $userField["LIST_COLUMN_LABEL"] : $fieldName;

    $arComponentParameters["PARAMETERS"]['STORES'] = [
        'PARENT' => 'STORE_SETTINGS',
        'NAME' => GetMessage('STORES'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'VALUES' => $arStore,
        'ADDITIONAL_VALUES' => 'Y',
    ];
    $arComponentParameters["PARAMETERS"]['USE_MIN_AMOUNT'] = [
        'PARENT' => 'STORE_SETTINGS',
        'NAME' => GetMessage('USE_MIN_AMOUNT'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        "REFRESH" => "Y",
    ];
    $arComponentParameters["PARAMETERS"]['USER_FIELDS'] = [
        "PARENT" => "STORE_SETTINGS",
        "NAME" => GetMessage("STORE_USER_FIELDS"),
        "TYPE" => "LIST",
        "MULTIPLE" => "Y",
        "ADDITIONAL_VALUES" => "Y",
        "VALUES" => $propertyUF,
    ];
    $arComponentParameters["PARAMETERS"]['FIELDS'] = [
        'NAME' => GetMessage("STORE_FIELDS"),
        'PARENT' => 'STORE_SETTINGS',
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'ADDITIONAL_VALUES' => 'Y',
        'VALUES' => [
            'TITLE' => GetMessage("STORE_TITLE"),
            'ADDRESS' => GetMessage("ADDRESS"),
            'DESCRIPTION' => GetMessage('DESCRIPTION'),
            'PHONE' => GetMessage('PHONE'),
            'SCHEDULE' => GetMessage('SCHEDULE'),
            'EMAIL' => GetMessage('EMAIL'),
            'IMAGE_ID' => GetMessage('IMAGE_ID'),
            'COORDINATES' => GetMessage('COORDINATES'),
        ],
    ];
    if ($arCurrentValues['USE_MIN_AMOUNT'] != "N") {
        $arComponentParameters["PARAMETERS"]["MIN_AMOUNT"] = [
            "PARENT" => "STORE_SETTINGS",
            "NAME" => GetMessage("MIN_AMOUNT"),
            "TYPE" => "STRING",
            "DEFAULT" => 10,
        ];
    }
    $arComponentParameters["PARAMETERS"]['SHOW_EMPTY_STORE'] = [
        'PARENT' => 'STORE_SETTINGS',
        'NAME' => GetMessage('SHOW_EMPTY_STORE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
    ];
    $arComponentParameters["PARAMETERS"]['SHOW_GENERAL_STORE_INFORMATION'] = [
        'PARENT' => 'STORE_SETTINGS',
        'NAME' => GetMessage('SHOW_GENERAL_STORE_INFORMATION'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
    ];
    $arComponentParameters["PARAMETERS"]['STORE_PATH'] = [
        'PARENT' => 'STORE_SETTINGS',
        'NAME' => GetMessage('STORE_PATH'),
        "TYPE" => "STRING",
        "DEFAULT" => "/store/#store_id#",
    ];
    $arComponentParameters["PARAMETERS"]['MAIN_TITLE'] = [
        'PARENT' => 'STORE_SETTINGS',
        'NAME' => GetMessage('MAIN_TITLE'),
        "TYPE" => "STRING",
        "DEFAULT" => GetMessage('MAIN_TITLE_VALUE'),
    ];
}

if (!ModuleManager::isModuleInstalled("sale") || isset($templateProperties['HIDE_USE_ALSO_BUY'])) {
    unset($templateProperties['HIDE_USE_ALSO_BUY']);
    unset($arComponentParameters["PARAMETERS"]["USE_ALSO_BUY"]);
    unset($arComponentParameters["GROUPS"]["ALSO_BUY_SETTINGS"]);
} else if ($arCurrentValues["USE_ALSO_BUY"] == "Y") {
    $arComponentParameters["PARAMETERS"]["ALSO_BUY_ELEMENT_COUNT"] = [
        "PARENT" => "ALSO_BUY_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_DESC_ALSO_BUY_ELEMENT_COUNT"),
        "TYPE" => "STRING",
        "DEFAULT" => 5,
    ];
    $arComponentParameters["PARAMETERS"]["ALSO_BUY_MIN_BUYES"] = [
        "PARENT" => "ALSO_BUY_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_DESC_ALSO_BUY_MIN_BUYES"),
        "TYPE" => "STRING",
        "DEFAULT" => 1,
    ];
}

if (!ModuleManager::isModuleInstalled("sale")) {
    unset($arComponentParameters["PARAMETERS"]["USE_GIFTS_DETAIL"]);
    unset($arComponentParameters["PARAMETERS"]["USE_GIFTS_SECTION"]);
    unset($arComponentParameters["PARAMETERS"]["USE_GIFTS_MAIN_PR_SECTION_LIST"]);
    unset($arComponentParameters["GROUPS"]["GIFTS_SETTINGS"]);
} else {
    $useGiftsDetail = $arCurrentValues["USE_GIFTS_DETAIL"] === null && $arComponentParameters['PARAMETERS']['USE_GIFTS_DETAIL']['DEFAULT'] == 'Y' || $arCurrentValues["USE_GIFTS_DETAIL"] == "Y";
    $useGiftsSection = $arCurrentValues["USE_GIFTS_SECTION"] === null && $arComponentParameters['PARAMETERS']['USE_GIFTS_SECTION']['DEFAULT'] == 'Y' || $arCurrentValues["USE_GIFTS_SECTION"] == "Y";
    $useGiftsMainPrSectionList = $arCurrentValues["USE_GIFTS_MAIN_PR_SECTION_LIST"] === null && $arComponentParameters['PARAMETERS']['USE_GIFTS_MAIN_PR_SECTION_LIST']['DEFAULT'] == 'Y' || $arCurrentValues["USE_GIFTS_MAIN_PR_SECTION_LIST"] == "Y";
    if ($useGiftsDetail || $useGiftsSection || $useGiftsMainPrSectionList) {
        if ($useGiftsDetail) {
            $arComponentParameters["PARAMETERS"]["GIFTS_DETAIL_PAGE_ELEMENT_COUNT"] = [
                "PARENT" => "GIFTS_SETTINGS",
                "NAME" => GetMessage("SGP_PAGE_ELEMENT_COUNT_DETAIL"),
                "TYPE" => "STRING",
                "DEFAULT" => "4",
            ];
            $arComponentParameters["PARAMETERS"]["GIFTS_DETAIL_HIDE_BLOCK_TITLE"] = [
                "PARENT" => "GIFTS_SETTINGS",
                "NAME" => GetMessage("SGP_PARAMS_HIDE_BLOCK_TITLE_DETAIL"),
                "TYPE" => "CHECKBOX",
                "DEFAULT" => "",
            ];
            $arComponentParameters["PARAMETERS"]["GIFTS_DETAIL_BLOCK_TITLE"] = [
                "PARENT" => "GIFTS_SETTINGS",
                "NAME" => GetMessage("SGP_PARAMS_BLOCK_TITLE_DETAIL"),
                "TYPE" => "STRING",
                "DEFAULT" => GetMessage("SGB_PARAMS_BLOCK_TITLE_DETAIL_DEFAULT"),
            ];
            $arComponentParameters["PARAMETERS"]["GIFTS_DETAIL_TEXT_LABEL_GIFT"] = [
                "PARENT" => "GIFTS_SETTINGS",
                "NAME" => GetMessage("SGP_PARAMS_TEXT_LABEL_GIFT_DETAIL"),
                "TYPE" => "STRING",
                "DEFAULT" => GetMessage("SGP_PARAMS_TEXT_LABEL_GIFT_DEFAULT"),
            ];
        }
        if ($useGiftsSection) {
            $arComponentParameters["PARAMETERS"]["GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT"] = [
                "PARENT" => "GIFTS_SETTINGS",
                "NAME" => GetMessage("SGP_PAGE_ELEMENT_COUNT_SECTION_LIST"),
                "TYPE" => "STRING",
                "DEFAULT" => "4",
            ];
            $arComponentParameters["PARAMETERS"]["GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE"] = [
                "PARENT" => "GIFTS_SETTINGS",
                "NAME" => GetMessage("SGP_PARAMS_HIDE_BLOCK_TITLE_SECTION_LIST"),
                "TYPE" => "CHECKBOX",
                "DEFAULT" => "",
            ];
            $arComponentParameters["PARAMETERS"]["GIFTS_SECTION_LIST_BLOCK_TITLE"] = [
                "PARENT" => "GIFTS_SETTINGS",
                "NAME" => GetMessage("SGP_PARAMS_BLOCK_TITLE_SECTION_LIST"),
                "TYPE" => "STRING",
                "DEFAULT" => GetMessage("SGB_PARAMS_BLOCK_TITLE_SECTION_LIST_DEFAULT"),
            ];
            $arComponentParameters["PARAMETERS"]["GIFTS_SECTION_LIST_TEXT_LABEL_GIFT"] = [
                "PARENT" => "GIFTS_SETTINGS",
                "NAME" => GetMessage("SGP_PARAMS_TEXT_LABEL_GIFT_SECTION_LIST"),
                "TYPE" => "STRING",
                "DEFAULT" => GetMessage("SGP_PARAMS_TEXT_LABEL_GIFT_DEFAULT"),
            ];
        }

        if ($useGiftsDetail || $useGiftsSection) {
            $arComponentParameters["PARAMETERS"]["GIFTS_SHOW_DISCOUNT_PERCENT"] = [
                'PARENT' => 'GIFTS_SETTINGS',
                'NAME' => GetMessage('CVP_SHOW_DISCOUNT_PERCENT'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'Y',
            ];
            $arComponentParameters["PARAMETERS"]["GIFTS_SHOW_OLD_PRICE"] = [
                'PARENT' => 'GIFTS_SETTINGS',
                'NAME' => GetMessage('CVP_SHOW_OLD_PRICE'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'Y',
            ];
            $arComponentParameters["PARAMETERS"]["GIFTS_SHOW_NAME"] = [
                "PARENT" => "GIFTS_SETTINGS",
                "NAME" => GetMessage("CVP_SHOW_NAME"),
                "TYPE" => "CHECKBOX",
                "DEFAULT" => "Y",
            ];
            $arComponentParameters["PARAMETERS"]["GIFTS_SHOW_IMAGE"] = [
                "PARENT" => "GIFTS_SETTINGS",
                "NAME" => GetMessage("CVP_SHOW_IMAGE"),
                "TYPE" => "CHECKBOX",
                "DEFAULT" => "Y",
            ];
            $arComponentParameters["PARAMETERS"]['GIFTS_MESS_BTN_BUY'] = [
                'PARENT' => 'GIFTS_SETTINGS',
                'NAME' => GetMessage('CVP_MESS_BTN_BUY_GIFT'),
                'TYPE' => 'STRING',
                'DEFAULT' => GetMessage('CVP_MESS_BTN_BUY_GIFT_DEFAULT'),
            ];
        }
        if ($useGiftsMainPrSectionList) {
            $arComponentParameters["PARAMETERS"]["GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT"] = [
                "PARENT" => "GIFTS_SETTINGS",
                "NAME" => GetMessage("SGP_PAGE_ELEMENT_COUNT_MAIN_PR_DETAIL"),
                "TYPE" => "STRING",
                "DEFAULT" => "4",
            ];
            $arComponentParameters["PARAMETERS"]["GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE"] = [
                "PARENT" => "GIFTS_SETTINGS",
                "NAME" => GetMessage("SGP_PARAMS_HIDE_BLOCK_TITLE_MAIN_PR_DETAIL"),
                "TYPE" => "CHECKBOX",
                "DEFAULT" => "",
            ];
            $arComponentParameters["PARAMETERS"]["GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE"] = [
                "PARENT" => "GIFTS_SETTINGS",
                "NAME" => GetMessage("SGP_MAIN_PRODUCT_PARAMS_BLOCK_TITLE"),
                "TYPE" => "STRING",
                "DEFAULT" => GetMessage('SGB_MAIN_PRODUCT_PARAMS_BLOCK_TITLE_DEFAULT'),
            ];
        }
    }
}

if ($catalogIncluded) {
    $arComponentParameters["PARAMETERS"]['HIDE_NOT_AVAILABLE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE'),
        'TYPE' => 'LIST',
        'DEFAULT' => 'N',
        'VALUES' => [
            'Y' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_HIDE'),
            'L' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_LAST'),
            'N' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_SHOW'),
        ],
        'ADDITIONAL_VALUES' => 'N',
    ];
    $arComponentParameters['PARAMETERS']['HIDE_NOT_AVAILABLE_OFFERS'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_OFFERS'),
        'TYPE' => 'LIST',
        'DEFAULT' => 'N',
        'VALUES' => [
            'Y' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_OFFERS_HIDE'),
            'L' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_OFFERS_SUBSCRIBE'),
            'N' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_OFFERS_SHOW'),
        ],
    ];
    $arComponentParameters["PARAMETERS"]['CONVERT_CURRENCY'] = [
        'PARENT' => 'PRICES',
        'NAME' => GetMessage('CP_BC_CONVERT_CURRENCY'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y',
    ];

    if (isset($arCurrentValues['CONVERT_CURRENCY']) && $arCurrentValues['CONVERT_CURRENCY'] == 'Y') {
        $arComponentParameters['PARAMETERS']['CURRENCY_ID'] = [
            'PARENT' => 'PRICES',
            'NAME' => GetMessage('CP_BC_CURRENCY_ID'),
            'TYPE' => 'LIST',
            'VALUES' => Currency\CurrencyManager::getCurrencyList(),
            'DEFAULT' => Currency\CurrencyManager::getBaseCurrency(),
            "ADDITIONAL_VALUES" => "Y",
        ];
    }

    $hiddenParam = 'N';
    if (
        !$compatibleMode
        || ((string)Option::get('catalog', 'enable_viewed_products') === 'N')
    ) {
        $hiddenParam = 'Y';
    }
    $arComponentParameters['PARAMETERS']['DETAIL_SET_VIEWED_IN_COMPONENT'] = [
        "PARENT" => "EXTENDED_SETTINGS",
        "NAME" => GetMessage('CP_BC_DETAIL_SET_VIEWED_IN_COMPONENT'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
        "HIDDEN" => $hiddenParam,
    ];
}

if (empty($offers)) {
    unset($arComponentParameters["GROUPS"]["OFFERS_SETTINGS"]);
} else {
    if (!$usePropertyFeatures) {
        $arComponentParameters["PARAMETERS"]["OFFERS_CART_PROPERTIES"] = [
            "PARENT" => "BASKET",
            "NAME" => GetMessage("CP_BC_OFFERS_CART_PROPERTIES"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_OffersWithoutFile,
            "HIDDEN" => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N'),
        ];
    }

    $arComponentParameters["PARAMETERS"]["OFFERS_SORT_FIELD"] = [
        "PARENT" => "OFFERS_SETTINGS",
        "NAME" => GetMessage("CP_BC_OFFERS_SORT_FIELD"),
        "TYPE" => "LIST",
        "VALUES" => $arSort,
        "ADDITIONAL_VALUES" => "Y",
        "DEFAULT" => "sort",
    ];
    $arComponentParameters["PARAMETERS"]["OFFERS_SORT_ORDER"] = [
        "PARENT" => "OFFERS_SETTINGS",
        "NAME" => GetMessage("CP_BC_OFFERS_SORT_ORDER"),
        "TYPE" => "LIST",
        "VALUES" => $arAscDesc,
        "DEFAULT" => "asc",
        "ADDITIONAL_VALUES" => "Y",
    ];
    $arComponentParameters["PARAMETERS"]["OFFERS_SORT_FIELD2"] = [
        "PARENT" => "OFFERS_SETTINGS",
        "NAME" => GetMessage("CP_BC_OFFERS_SORT_FIELD2"),
        "TYPE" => "LIST",
        "VALUES" => $arSort,
        "ADDITIONAL_VALUES" => "Y",
        "DEFAULT" => "id",
    ];
    $arComponentParameters["PARAMETERS"]["OFFERS_SORT_ORDER2"] = [
        "PARENT" => "OFFERS_SETTINGS",
        "NAME" => GetMessage("CP_BC_OFFERS_SORT_ORDER2"),
        "TYPE" => "LIST",
        "VALUES" => $arAscDesc,
        "DEFAULT" => "desc",
        "ADDITIONAL_VALUES" => "Y",
    ];
}
