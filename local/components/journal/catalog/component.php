<?php

use Bitrix\Iblock\ElementTable;
use Its\Library\Iblock\Iblock;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @global CMain $APPLICATION */
if (isset($arParams["USE_FILTER"]) && $arParams["USE_FILTER"] == "Y") {
    $arParams["FILTER_NAME"] = trim($arParams["FILTER_NAME"]);
    if ($arParams["FILTER_NAME"] === '' || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])) {
        $arParams["FILTER_NAME"] = "arrFilter";
    }
} else {
    $arParams["FILTER_NAME"] = "";
}

//default gifts
if (empty($arParams['USE_GIFTS_SECTION'])) {
    $arParams['USE_GIFTS_SECTION'] = 'Y';
}
if (empty($arParams['GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT'])) {
    $arParams['GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT'] = 3;
}
if (empty($arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'])) {
    $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'] = 4;
}
if (empty($arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'])) {
    $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'] = 4;
}

$arParams['ACTION_VARIABLE'] = (isset($arParams['ACTION_VARIABLE']) ? trim($arParams['ACTION_VARIABLE']) : 'action');
if ($arParams["ACTION_VARIABLE"] == '' || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["ACTION_VARIABLE"])) {
    $arParams["ACTION_VARIABLE"] = "action";
}

$smartBase = $arParams["SEF_URL_TEMPLATES"]["section"] ?: "#SECTION_ID#/";
$arDefaultUrlTemplates404 = [];
$arDefaultUrlTemplates404['sections'] = '';
$arDefaultUrlTemplates404['smart_filter_sections'] = 'filter/#SMART_FILTER_PATH#/apply/';

$arDefaultUrlTemplates404['collections'] = 'collections/';
$arDefaultUrlTemplates404['smart_filter_collections'] = 'collections/filter/#SMART_FILTER_PATH#/apply/';

$resCollections = ElementTable::getList([
    'filter' => ['IBLOCK_ID' => Iblock::getInstance()->get('collections'), 'ACTIVE' => 'Y'],
    'select' => ['ID', 'CODE', 'NAME'],
]);

$arCollections = [];
while ($arCollection = $resCollections->fetch()) {
    $arCollectionTemplate['ID'] = $arCollection['ID'];
    $arCollectionTemplate['NAME'] = $arCollection['NAME'];
    $arCollectionTemplate['TEMPLATE'] = "collection_{$arCollection['CODE']}";
    $arCollectionTemplate['FILTER_TEMPLATE'] = "smart_filter_collection_{$arCollection['CODE']}";
    $arDefaultUrlTemplates404[$arCollectionTemplate['TEMPLATE']] = "collections/{$arCollection['CODE']}/";
    $arDefaultUrlTemplates404[$arCollectionTemplate['FILTER_TEMPLATE']] = "collections/{$arCollection['CODE']}/filter/#SMART_FILTER_PATH#/apply/";
    $arCollections[$arCollection['CODE']] = $arCollectionTemplate;
}

$arDefaultUrlTemplates404['section'] = '#SECTION_ID#/';
$arDefaultUrlTemplates404['element'] = '#SECTION_ID#/#ELEMENT_ID#/';
$arDefaultUrlTemplates404['compare'] = 'compare.php?action=COMPARE';
$arDefaultUrlTemplates404['smart_filter'] = $smartBase . 'filter/#SMART_FILTER_PATH#/apply';

$arDefaultVariableAliases404 = [];

$arDefaultVariableAliases = [];

$arComponentVariables = [
    "SECTION_ID",
    "SECTION_CODE",
    "ELEMENT_ID",
    "ELEMENT_CODE",
    "action",
];

$arResult = [];

if ($arParams["SEF_MODE"] == "Y") {
    $arVariables = [];

    $engine = new CComponentEngine($this);
    if (\Bitrix\Main\Loader::includeModule('iblock')) {
        $engine->addGreedyPart("#SECTION_CODE_PATH#");
        $engine->addGreedyPart("#SMART_FILTER_PATH#");
        $engine->setResolveCallback(["CIBlockFindTools", "resolveComponentEngine"]);
    }
    $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
    $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

    $componentPage = $engine->guessComponentPath(
        $arParams["SEF_FOLDER"],
        $arUrlTemplates,
        $arVariables
    );

    if (in_array($componentPage, array_column($arCollections, 'FILTER_TEMPLATE'))) {
        $arResult['COLLECTION'] = $arCollections[str_replace('smart_filter_collection_', '', $componentPage)];
        $componentPage = 'collection';
    }
    if (in_array($componentPage, array_column($arCollections, 'TEMPLATE'))) {
        $arResult['COLLECTION'] = $arCollections[str_replace('collection_', '', $componentPage)];
        $componentPage = 'collection';
    }

    if ($componentPage === "smart_filter_sections") {
        if (isset($_REQUEST["q"])) {
            $componentPage = "search";
        } else {
            $componentPage = "sections";
        }
    }

    if ($componentPage === "smart_filter_collections") {
        $componentPage = "collections";
    }

    if ($componentPage === "smart_filter") {
        $componentPage = "section";
    }

    $b404 = false;
    if (!$componentPage) {
        if (isset($_REQUEST["q"])) {
            $componentPage = "search";
        } else {
            $componentPage = "sections";
        }
        $b404 = true;
    }

    if ($componentPage == "section") {
        if (isset($arVariables["SECTION_ID"])) {
            $b404 |= (intval($arVariables["SECTION_ID"]) . "" !== $arVariables["SECTION_ID"]);
        } else {
            $b404 |= !isset($arVariables["SECTION_CODE"]);
        }
    }

    if ($b404 && CModule::IncludeModule('iblock')) {
        $folder404 = str_replace("\\", "/", $arParams["SEF_FOLDER"]);
        if ($folder404 != "/") {
            $folder404 = "/" . trim($folder404, "/ \t\n\r\0\x0B") . "/";
        }
        if (mb_substr($folder404, -1) == "/") {
            $folder404 .= "index.php";
        }

        if ($folder404 != $APPLICATION->GetCurPage(true)) {
            \Bitrix\Iblock\Component\Tools::process404(
                "",
                $arParams["SET_STATUS_404"] === "Y",
                $arParams["SET_STATUS_404"] === "Y",
                $arParams["SHOW_404"] === "Y",
                $arParams["FILE_404"]
            );
        }
    }

    CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

    $arResult["FOLDER"] = $arParams["SEF_FOLDER"];
    $arResult["URL_TEMPLATES"] = $arUrlTemplates;
    $arResult["VARIABLES"] = $arVariables;
    $arResult["ALIASES"] = $arVariableAliases;
} else {
    $arVariables = [];

    $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
    CComponentEngine::initComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

    $componentPage = "";

    $arCompareCommands = [
        "COMPARE",
        "DELETE_FEATURE",
        "ADD_FEATURE",
        "DELETE_FROM_COMPARE_RESULT",
        "ADD_TO_COMPARE_RESULT",
        "COMPARE_BUY",
        "COMPARE_ADD2BASKET",
    ];

    if (isset($arVariables["action"]) && in_array($arVariables["action"], $arCompareCommands)) {
        $componentPage = "compare";
    } elseif (isset($arVariables["ELEMENT_ID"]) && intval($arVariables["ELEMENT_ID"]) > 0) {
        $componentPage = "element";
    } elseif (isset($arVariables["ELEMENT_CODE"]) && $arVariables["ELEMENT_CODE"] <> '') {
        $componentPage = "element";
    } elseif (isset($arVariables["SECTION_ID"]) && intval($arVariables["SECTION_ID"]) > 0) {
        $componentPage = "section";
    } elseif (isset($arVariables["SECTION_CODE"]) && $arVariables["SECTION_CODE"] <> '') {
        $componentPage = "section";
    } elseif (isset($_REQUEST["q"])) {
        $componentPage = "search";
    } else {
        $componentPage = "sections";
    }

    $currentPage = htmlspecialcharsbx($APPLICATION->GetCurPage()) . "?";
    $arResult = [
        "FOLDER" => "",
        "URL_TEMPLATES" => [
            "section" => $currentPage . $arVariableAliases["SECTION_ID"] . "=#SECTION_ID#",
            "element" => $currentPage . $arVariableAliases["SECTION_ID"] . "=#SECTION_ID#" . "&" . $arVariableAliases["ELEMENT_ID"] . "=#ELEMENT_ID#",
            "compare" => $currentPage . "action=COMPARE",
        ],
        "VARIABLES" => $arVariables,
        "ALIASES" => $arVariableAliases,
    ];
}

$this->IncludeComponentTemplate($componentPage);
