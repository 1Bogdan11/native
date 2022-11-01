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

?>
<ul class="aside-tabs__list">
    <?php
    foreach ($arResult['ITEMS'] as $arItem) {
        ?>
        <li class="aside-tabs__item">
            <a href="<?=$arItem['DETAIL_PAGE_URL']?>" data-modal-level-open="showroom-<?=$arItem['ID']?>">
                <span><?=$arItem['NAME']?></span>
                <div class="city"><?=$arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['NAME']?></div>
            </a>
        </li>
        <?php
    }
    ?>
</ul>
<div class="aside-tabs__item">
    <a href="<?=reset($arResult['SECTIONS'])['SECTION_PAGE_URL']?>" data-modal-level-open>
        <span><?=Loc::getMessage('SHOPS_LIST_OTHER')?></span>
    </a>
</div>
<?php


$this->SetViewTarget('SHOPS_ASIDE_DETAIL');
foreach ($arResult['ITEMS'] as $arItem) {
    ?>
    <div class="aside-tabs__level" data-modal-level="showroom-<?=$arItem['ID']?>">
        <div class="showrooms__item">
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
                        <a class="button-route" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                            <span class="link__underline--hover">
                                <?=Loc::getMessage('SHOPS_LIST_MORE')?>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
$this->EndViewTarget();
