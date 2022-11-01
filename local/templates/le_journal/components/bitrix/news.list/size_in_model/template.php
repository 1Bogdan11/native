<?php

use Bitrix\Main\Localization\Loc;
use Its\Library\Image\Resize;

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

if (empty($arResult['ITEMS'])) {
    return;
}

$this->SetViewTarget('ELEMENT_SIZE_MODEL_BUTTON');
?>
<button class="aside-tabs__link" data-modal-tab="stories" data-modal-state-add="is-fullscreen">
    <?=Loc::getMessage('ELEMENT_SIZE_IN_MODEL_HEAD')?>
</button>
<?php
$this->EndViewTarget();


$propertiesMap = [
    'SIZE' => '',
    'BUST' => Loc::getMessage('ELEMENT_SIZE_IN_MODEL_PROPERTY_BUST'),
    'UNDERBUST' => Loc::getMessage('ELEMENT_SIZE_IN_MODEL_PROPERTY_UNDERBUST'),
    'WAIST' => Loc::getMessage('ELEMENT_SIZE_IN_MODEL_PROPERTY_WAIST'),
    'HIPS' => Loc::getMessage('ELEMENT_SIZE_IN_MODEL_PROPERTY_HIPS'),
];

?>
<div class="aside-tabs__content" data-modal-content="stories">
    <div class="stories swiper-container js-stories">
        <div class="stories__inner swiper-wrapper">
            <?php
            foreach ($arResult['ITEMS'] as $arItem) {
                $resize = new Resize(
                    intval($arItem['PREVIEW_PICTURE']['ID']),
                    [600, 1000],
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    [Resize::SHARPEN_OFF]
                );
                ?>
                <div class="swiper-slide">
                    <button class="stories__prev js-stories-prev">
                        <svg class="i-arrow-simple"><use xlink:href="#i-arrow-simple"></use></svg>
                    </button>
                    <button class="stories__next js-stories-next">
                        <svg class="i-arrow-simple"><use xlink:href="#i-arrow-simple"></use></svg>
                    </button>
                    <div class="stories__slide">
                        <?=$resize->getPictureTag(
                            [
                                'alt' => '',
                                'img_class' => 'stories__slide_picture',
                                'no_photo' => '/assets/img/no_photo.svg',
                            ]
                        )?>
                        <div class="stories__slide_content">
                            <?php
                            foreach ($propertiesMap as $propertyKey => $propertyName) {
                                if (empty($arItem['PROPERTIES'][$propertyKey]['VALUE'])) {
                                    continue;
                                }
                                ?>
                                <span><?=($propertyName ? "$propertyName: " : '')?><?=trim($arItem['PROPERTIES'][$propertyKey]['VALUE'])?></span>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>
