<?php

use Bitrix\Main\Context;
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

$this->setFrameMode(true);
$request = Context::getCurrent()->getRequest();
$editParams = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');

if (empty($arResult['ITEMS'])) {
    ?>
    <script>
        let searchStatusWrap = document.getElementById('searchStatusWrap');
        if (searchStatusWrap) {
            searchStatusWrap.innerHTML = '<div class="search-form__status"><?=Loc::getMessage('SECTION_LIST_SEARCH_EMPTY')?></div>';
        }
    </script>
    <?php
    return;
}

if ($arParams['DISPLAY_TOP_PAGER']) {
    echo $arResult['NAV_STRING'];
}

?>
<ul class="catalog-section__list jsCatalogSectionElementListWrap <?=($request->get('ajax_request') === 'Y' ? 'is-inview' : '')?>" data-scroll>
    <?php
    foreach ($arResult['ITEMS'] as $arItem) {
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $editParams);
        $APPLICATION->IncludeComponent(
            'journal:catalog.item',
            '',
            [
                'CURRENT_FILTER' => $arParams['CURRENT_FILTER'],
                'ITEM' => $arItem,
                'AREA_ID' => $this->GetEditAreaId($arItem['ID']),
            ],
            $component,
            ['HIDE_ICONS' => 'Y']
        );
    }
    ?>
</ul>
<?php

if ($arParams['DISPLAY_BOTTOM_PAGER']) {
    echo $arResult['NAV_STRING'];
}
