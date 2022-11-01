<?

use Its\Library\Iblock\Iblock;

global $APPLICATION;
$aMenuLinksExt = array();


if (CModule::IncludeModule('iblock')) {
    $arFilter = array(
        "TYPE" => "1c_catalog",
        "SITE_ID" => SITE_ID,
        "ID" => Iblock::getInstance()->get('catalog')
    );

    $dbIBlock = CIBlock::GetList(array('SORT' => 'ASC', 'ID' => 'ASC'), $arFilter);
    $dbIBlock = new CIBlockResult($dbIBlock);

    if ($arIBlock = $dbIBlock->GetNext()) {
        if (defined("BX_COMP_MANAGED_CACHE")) {
            $GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_" . $arIBlock["ID"]);
        }

        if ($arIBlock["ACTIVE"] == "Y") {
            $aMenuLinksExt = $APPLICATION->IncludeComponent(
                "journal:menu.sections",
                "",
                array(
                    "IS_SEF" => "Y",
                    "SEF_BASE_URL" => "",
                    "SECTION_PAGE_URL" => $arIBlock['SECTION_PAGE_URL'],
                    "DETAIL_PAGE_URL" => $arIBlock['DETAIL_PAGE_URL'],
                    "IBLOCK_TYPE" => $arIBlock['IBLOCK_TYPE_ID'],
                    "IBLOCK_ID" => $arIBlock['ID'],
                    "DEPTH_LEVEL" => "2",
                    "CACHE_TYPE" => "N",
                ),
                false,
                array('HIDE_ICONS' => 'Y')
            );

            foreach ($aMenuLinksExt as $item){
                if ($item[3]["UF_PRODINMENU"]){
                    $GLOBALS["menuProducts"][] = $item[3]["UF_PRODINMENU"];
                }
            }


        }
    }

    if (defined("BX_COMP_MANAGED_CACHE")) {
        $GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_new");
    }
}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>