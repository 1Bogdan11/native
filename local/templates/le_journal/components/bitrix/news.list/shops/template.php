<?php

use Journal\Tool\Template;
use Its\Library\Image\Resize;
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
$editParams = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$arFirstSection = \CIBlockSection::GetList(
    ['SORT' => 'ASC'],
    ['DEPTH_LEVEL' => 1, 'ACTIVE' => 'Y', 'IBLOCK_ID' => $arParams['IBLOCK_ID']]
)->GetNext();

?>
<div class="showrooms js-booster-mobile-x" data-scroll>
    <div class="showrooms__inner js-booster__inner">
        <ul class="showrooms__list">
            <?php
            foreach ($arResult['ITEMS'] as $arItem) {
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $editParams);
                ?>
                <div class="showrooms__item" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
                    <div class="showrooms__item-image img">
                        <?php
                        $pictures = Template::selectPictures($arItem);
                        $resize = new Resize(intval(reset($pictures)), [650, 1000]);
                        echo $resize->getPictureTag(
                            [
                                'alt' => htmlspecialchars($arItem['NAME']),
                                'no_photo' => '/assets/img/no_photo.svg',
                                'img_class' => 'img__i',
                                'img_attribute' => 'data-scroll data-scroll-speed="-0.8"',
                            ]
                        );
                        ?>
                    </div>
                    <div class="point-info">
                        <a class="point-info__title" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                            <?=$arItem['NAME']?>
                        </a>
                        <div class="point-info__contacts">
                            <div class="point-info__contacts_part">
                                <p><?=$arItem['PROPERTIES']['ADDRESS']['VALUE']?></p>
                                <?php
                                if (!empty($arItem['PROPERTIES']['WORKING_HOURS']['VALUE'])) {
                                    ?>
                                    <p><?=$arItem['PROPERTIES']['WORKING_HOURS']['VALUE']?></p>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="point-info__contacts_part">
                                <?php
                                if (!empty($arItem['PROPERTIES']['PHONE']['VALUE'])) {
                                    $phone = preg_replace('/[^+0-9]/', '', $arItem['PROPERTIES']['PHONE']['VALUE']);
                                    ?>
                                    <a class="link__underline--hover" href="tel:<?=$phone?>">
                                        <?=$arItem['PROPERTIES']['PHONE']['VALUE']?>
                                    </a>
                                    <?php
                                }
                                ?>
                                <?php
                                if (!empty($arItem['PROPERTIES']['WHATSAPP']['VALUE'])) {
                                    $phone = preg_replace('/[^+0-9]/', '', $arItem['PROPERTIES']['WHATSAPP']['VALUE']);
                                    ?>
                                    <a target="_blank" href="https://wa.me/<?=$phone?>?text=">
                                        <svg class="i-whatsapp"><use xlink:href="#i-whatsapp"></use></svg>
                                        <span class="link__underline--hover">
                                            <?=Loc::getMessage('SHOPS_LIST_WHATSAPP')?>
                                        </span>
                                    </a>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="point-info__contacts_part">
                                <?php
                                if (!empty($arItem['PROPERTIES']['EMAIL']['VALUE'])) {
                                    ?>
                                    <a class="link__underline--hover" href="mailto:<?=$arItem['PROPERTIES']['EMAIL']['VALUE']?>">
                                        <?=$arItem['PROPERTIES']['EMAIL']['VALUE']?>
                                    </a>
                                    <?php
                                }
                                ?>
                                <a class="button-route media-max--tab" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                    <span class="link__underline--hover">
                                        <?=Loc::getMessage('SHOPS_LIST_ON_MAP')?>
                                    </span>
                                </a>
                                <a class="button-route media-min--tab" href="https://yandex.ru/maps/?rtext=~<?=$arItem['PROPERTIES']['MAP']['VALUE']?>" target="_blank">
                                    <?=Loc::getMessage('SHOPS_LIST_NAVIGATION')?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="showrooms__item">
                <div class="showrooms__item-image img">
                    <?php
                    $pictureId = intval(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/shops_sales_locations.php')));
                    $resize = new Resize($pictureId, [650, 1000]);
                    echo $resize->getPictureTag(
                        [
                            'alt' => htmlspecialchars(Loc::getMessage('SHOPS_LIST_ON_MAP')),
                            'no_photo' => '/assets/img/no_photo.svg',
                            'img_class' => 'img__i',
                            'img_attribute' => 'data-scroll data-scroll-speed="-0.8"',
                        ]
                    );
                    ?>
                </div>
                <div class="point-info">
                    <a class="point-info__title" href="<?=$arFirstSection['SECTION_PAGE_URL']?>">
                        <?=Loc::getMessage('SHOPS_LIST_OTHER')?>
                    </a>
                    <div class="point-info__contacts">
                        <div class="point-info__contacts_part">
                            <a class="button-route" href="<?=$arFirstSection['SECTION_PAGE_URL']?>">
                                <span class="link__underline--hover">
                                    <?=Loc::getMessage('SHOPS_LIST_ON_MAP')?>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </ul>
    </div>
</div>
