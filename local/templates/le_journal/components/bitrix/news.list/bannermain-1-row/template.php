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
<div class="hero__area">
    <?
    foreach ($arResult["ITEMS"] as $i => $arItem): ?>
        <?
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
        if ($i == 0) {
            $resize = new Resize(
                intval($arItem['PREVIEW_PICTURE']['ID']),
                [1200, 1200],
                BX_RESIZE_IMAGE_PROPORTIONAL,
                [Resize::SHARPEN_OFF]
            );
            ?>
            <div class="hero__area-part" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                <a class="hero__image"
                   href="<?= $arItem["DISPLAY_PROPERTIES"]["LINK"]["DISPLAY_VALUE"] ?>"
                   data-observe="fade-y"
                   data-scroll>
                    <?=$resize->getPictureTag(
                        [
                            'alt' => '',
                            'no_photo' => '/assets/img/no_photo.svg',
                            'img_attribute' => 'data-scroll data-scroll-speed="-2"',
                        ]
                    )?>
                </a>
            </div>
            <?
        } elseif ($i == 1) {
            $resize = new Resize(
                intval($arItem['PREVIEW_PICTURE']['ID']),
                [600, 600],
                BX_RESIZE_IMAGE_PROPORTIONAL,
                [Resize::SHARPEN_OFF]
            );
            ?>
            <div class="hero__area-part" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                <div class="hero__area-item">
                    <a class="hero__image"
                       href="<?= $arItem["DISPLAY_PROPERTIES"]["LINK"]["DISPLAY_VALUE"]; ?>"
                       data-observe="fade-y" data-scroll>
                        <?=$resize->getPictureTag(
                            [
                                'alt' => '',
                                'no_photo' => '/assets/img/no_photo.svg',
                                'img_attribute' => 'data-scroll data-scroll-speed="-1"',
                            ]
                        )?>
                    </a>
                    <div class="hero__area-item_title"><?= $arItem["NAME"] ?></div>
                </div>
            </div>
            <?
        } ?>
    <?
    endforeach; ?>
</div>


