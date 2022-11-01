<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */
//получим список разделов в текущем ИБ
$arResult['ADD_DATA']['SECTIONS'] = [] ;
$iblockId = $arResult['ID'];
$rsSection = \Bitrix\Iblock\SectionTable::getList(
    [
        'order' => ['SORT' => 'ASC'],
        'filter' => [
            'IBLOCK_ID' => $iblockId,
            'ACTIVE' => 'Y',
            'GLOBAL_ACTIVE' => 'Y',
        ],
        'select' => [
            'ID',
            'NAME',
            'IBLOCK_SECTION_PAGE_URL' => 'IBLOCK.SECTION_PAGE_URL',
        ],
    ]
);
while ($arSection = $rsSection->fetch()) {
    $arResult['ADD_DATA']['SECTIONS'][$arSection['ID']] =$arSection;
}

