<?php

use Bitrix\Main\Localization\Loc;

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


$this->SetViewTarget('SHOPS_MAP_HEAD');
?>
<select class="select js-select js-contacts-region">
    <?php
    foreach ($arResult['SECTIONS'] as $arSection) {
        ?>
        <option
            value="<?=$arSection['ID']?>"
            <?=(intval($arSection['ID']) === intval($arParams['CURRENT_SECTION_ID']) ? 'selected' : '')?>
            data-href="<?=$arSection['SECTION_PAGE_URL']?>">
            <?=$arSection['NAME']?>
        </option>
        <?php
    }
    ?>
</select>
<?php
foreach ($arResult['SECTIONS'] as $arSection) {
    ?>
    <a href="<?=$arSection['SECTION_PAGE_URL']?>" style="display:none"><?=$arSection['NAME']?></a>
    <?php
}
$this->EndViewTarget();

$this->SetViewTarget('SHOPS_MAP_MODAL');
?>
<section class="modal modal--map-modal modal--right modal--bordered media-min--tab" data-modal="map-modal">
    <button class="modal__overlay" type="button" data-modal-close="map-modal">
        <button class="modal__mobile-close" data-modal-close="map-modal"></button>
    </button>
    <div class="modal__container">
        <div class="modal__content">
            <div class="contacts__map">
                <div class="map" data-map-platform="mobile"></div>
                <a class="contacts__email" href="#">
                    <svg class="i-mail"><use xlink:href="#i-mail"></use></svg>
                </a>
                <div class="contacts__map_content js-contacts-modal-content"></div>
            </div>
        </div>
    </div>
</section>
<?php
$this->EndViewTarget();

?>

<div class="sale-points js-sale-points">
    <?php
    $regionsMapData = [];
    foreach ($arResult['SECTIONS'] as $arSection) {
        $sectionMapData = [
            'type' => 'FeatureCollection',
            'id' => $arSection['ID'],
            'features' => [],
        ];
        if (intval($arSection['ID']) === intval($arParams['CURRENT_SECTION_ID'])) {
            $sectionMapData['active'] = true;
        }
        ?>
        <ul class="sale-points__list js-sale-point <?=(intval($arSection['ID']) === intval($arParams['CURRENT_SECTION_ID']) ? 'is-active' : '')?>" data-region-id="<?=$arSection['ID']?>">
            <?php
            foreach ($arResult['ITEMS'] as $arItem) {
                if (intval($arItem['IBLOCK_SECTION_ID']) !== intval($arSection['ID'])) {
                    continue;
                }
                $itemMapData = [
                    'type' => 'Feature',
                    'id' => $arItem['ID'],
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => array_reverse(explode(',', $arItem['PROPERTIES']['MAP']['VALUE'])),
                    ]
                ];
                if (intval($arItem['ID']) === intval($arParams['CURRENT_ELEMENT_ID'])) {
                    $itemMapData['centered'] = true;
                    $itemMapData['mainRegionPoint'] = true;
                }
                $sectionMapData['features'][] = $itemMapData;
                ?>
                <li class="sale-points__item <?=(intval($arItem['ID']) === intval($arParams['CURRENT_ELEMENT_ID']) ? 'is-active' : '')?>">
                    <div class="point-info">
                        <button class="point-info__title" data-map-point="<?=$arItem['ID']?>">
                            <span><?=$arItem['NAME']?></span>
                        </button>
                        <div class="point-info__labels">
                            <?php
                            if ($arItem['PROPERTIES']['VENDOR']['VALUE_XML_ID'] === 'Y') {
                                ?>
                                <div class="label label--sale point-info__label">
                                    <span>
                                        <?=Loc::getMessage('SHOPS_MAP_VENDOR')?>
                                    </span>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="label label--new point-info__label">
                                    <span>
                                        <?=Loc::getMessage('SHOPS_MAP_PARTNER')?>
                                    </span>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
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
                                            <?=Loc::getMessage('SHOPS_MAP_WHATSAPP')?>
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
                                <a class="button-route" href="https://yandex.ru/maps/?rtext=~<?=$arItem['PROPERTIES']['MAP']['VALUE']?>" target="_blank">
                                    <span class="link__underline--hover">
                                        <?=Loc::getMessage('SHOPS_MAP_NAVIGATION')?>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
        $regionsMapData[] = $sectionMapData;
    }
    ?>
</div>
<script>
    const regionsMapData = <?=json_encode($regionsMapData)?>;
</script>
