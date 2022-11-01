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

$this->setFrameMode(true);

$strSectionEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_EDIT');
$isSelectedSection = false;

?>
<div class="modal__content">
    <?php
    if ($arResult['SECTIONS_COUNT'] > 0) {
        ?>
        <div class="categories-modal">
            <ul class="categories-modal__list">
                <li class="categories-modal__item">
                    <a href="<?=$arParams['FOLDER']?>">
                        <span><?=Loc::getMessage('MODAL_CATEGORIES_ALL')?></span>
                    </a>
                </li>
                <?php
                foreach ($arResult['SECTIONS'] as $arSection) {
                    $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
                    if ($arSection['ID'] == $arParams['CURRENT_SECTION_ID'] || $arSection['CODE'] == $arParams['CURRENT_SECTION_CODE']) {
                        $this->SetViewTarget('CATALOG_CATEGORIES_SELECTOR');
                        echo $arSection['NAME'];
                        $this->EndViewTarget();
                        $isSelectedSection = true;
                    }
                    ?>
                    <li class="categories-modal__item" id="<?=$this->GetEditAreaId($arSection['ID'])?>">
                        <a href="<?=$arSection['SECTION_PAGE_URL']?>">
                            <span><?=$arSection['NAME']?></span>
                            <div class="count"><?=$arSection['ELEMENT_CNT']?></div>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
        <?php
    }
    ?>
</div>
<?php

if (!$isSelectedSection) {
    $this->SetViewTarget('CATALOG_CATEGORIES_SELECTOR');
    echo Loc::getMessage('MODAL_CATEGORIES_ALL');
    $this->EndViewTarget();
}
