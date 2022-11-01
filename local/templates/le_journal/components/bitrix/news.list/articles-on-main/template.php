<?

use Its\Library\Image\Resize;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
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
        <div class="diary-section__row diary-section__row--type-1">
            <?
            foreach ($arResult["ITEMS"] as $arItem): ?>
                <?
                $resize = new Resize(
                    intval($arItem['PREVIEW_PICTURE']['ID']),
                    [800, 800],
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    [Resize::SHARPEN_OFF]
                );
                $this->AddEditAction(
                    $arItem['ID'],
                    $arItem['EDIT_LINK'],
                    CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT")
                );
                $this->AddDeleteAction(
                    $arItem['ID'],
                    $arItem['DELETE_LINK'],
                    CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
                    array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM'))
                );
                ?>
                <?
                $itemSectionName = "";
                if ($arItem["IBLOCK_SECTION_ID"]) {
                    $itemSectionName = $arResult["ADD_DATA"]["SECTIONS"][$arItem["IBLOCK_SECTION_ID"]]["NAME"];
                };
                ?>
                <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>" class="diary-card" data-scroll id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                    <div class="diary-card__image">
                        <?=$resize->getPictureTag(
                            [
                                'alt' => '',
                                'no_photo' => '/assets/img/no_photo.svg',
                            ]
                        )?>
                    </div>
                    <div class="diary-card__content">
                        <div class="diary-card__head">
                            <div class="diary-card__category"><?= ($itemSectionName ? $itemSectionName : "") ?></div>
                            <?php
                            if ($arParams['DISPLAY_DATE'] != 'N' && $arItem['ACTIVE_FROM']) {
                                ?>
                                <div class="diary-card__date">
                                    <?php
                                    $date = new DateTime($arItem['ACTIVE_FROM']);
                                    echo FormatDate(
                                        $date->format('Y') === (new DateTime())->format('Y') ? 'j F' : 'j F Y',
                                        $date->getTimestamp()
                                    );
                                    ?>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="diary-card__title"><?
                            echo $arItem["NAME"] ?></div>
                        <?
                        if ($arParams["DISPLAY_PREVIEW_TEXT"] != "N" && $arItem["PREVIEW_TEXT"]): ?>
                            <div class="diary-card__description"><?
                                echo $arItem["PREVIEW_TEXT"]; ?></div>
                        <?
                        endif; ?>
                    </div>
                </a>
            <?
            endforeach; ?>
        </div>
    </div>
