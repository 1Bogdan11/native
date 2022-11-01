<?php

use Its\Library\Image\Resize;
use Bitrix\Main\Type\DateTime;

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

?>
<div class="diary-section__area">
    <div class="diary-section__row jsBlogSectionElementListWrap">
        <?php
        foreach (array_values($arResult['ITEMS']) as $i => $arItem) {
            $this->AddEditAction(
                $arItem['ID'],
                $arItem['EDIT_LINK'],
                CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT')
            );
            $this->AddDeleteAction(
                $arItem['ID'],
                $arItem['DELETE_LINK'],
                CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE')
            );
            $itemSectionName = '';
            if ($arItem['IBLOCK_SECTION_ID']) {
                $itemSectionName = $arResult['ADD_DATA']['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['NAME'];
            }
            $additionalClass = '';
            if (in_array($i + 1, [1, 5, 9])) {
                $additionalClass = 'diary-card--big';
            }
            ?>
            <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>" class="diary-card <?=$additionalClass?>" data-scroll id="<?=$this->GetEditAreaId($arItem['ID'])?>">
                <div class="diary-card__image">
                    <?php
                    $resize = new Resize(
                        intval($arItem['PREVIEW_PICTURE']['ID']),
                        [1000, 800],
                        BX_RESIZE_IMAGE_PROPORTIONAL,
                        [Resize::SHARPEN_OFF]
                    );
                    echo $resize->getPictureTag([
                        'alt' => htmlspecialchars($arItem['NAME']),
                        'no_photo' => '/assets/img/no_photo.svg',
                    ]);
                    ?>
                </div>
                <div class="diary-card__content">
                    <div class="diary-card__head">
                        <div class="diary-card__category"><?=$itemSectionName?></div>
                        <div class="diary-card__date">
                            <?php
                            $date = new DateTime($arItem['ACTIVE_FROM']);
                            echo FormatDate(
                                $date->format('Y') === (new DateTime())->format('Y') ? 'j F' : 'j F Y',
                                $date->getTimestamp()
                            );
                            ?>
                        </div>
                    </div>
                    <div class="diary-card__title">
                        <?=$arItem['NAME']?>
                    </div>
                    <?php
                    if ($arItem['PREVIEW_TEXT']) {
                        ?>
                        <div class="diary-card__description"><?=htmlspecialchars($arItem['PREVIEW_TEXT'])?></div>
                        <?php
                    }
                    ?>
                </div>
            </a>
            <?php
        }
        ?>
    </div>
    <?php
    if ($arParams['DISPLAY_BOTTOM_PAGER']) {
        echo $arResult['NAV_STRING'];
    }
    ?>
</div>
