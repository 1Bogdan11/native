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
<?
foreach ($arResult["ITEMS"] as $i => $arItem): ?>
    <?
    $resize = new Resize(
        intval($arItem['PREVIEW_PICTURE']['ID']),
        [1100, 1100],
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
    <div class="discount-section__part" data-scroll data-scroll-speed="<?= ($i + 1) ?>"><a
                class="discount-section__card" href="<?= $arItem["DISPLAY_PROPERTIES"]["LINK"]["DISPLAY_VALUE"] ?>">
            <?=$resize->getPictureTag(
                [
                    'alt' => '',
                    'no_photo' => '/assets/img/no_photo.svg',
                ]
            )?>
            <div class="discount-section__card-title t-h6"><?= $arItem["NAME"] ?></div>
        </a></div>
<?
endforeach; ?>



