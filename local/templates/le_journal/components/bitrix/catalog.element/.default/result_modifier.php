<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

$arResult['DETAIL_PICTURE_PINS'] = [];
$arPins = json_decode($arResult['PROPERTIES']['DETAIL_PICTURE_PINS']['~VALUE'], true);
if (is_array($arPins)) {
    foreach ($arPins as $arPin) {
        $pin = ['location' => array_reverse(explode(':', strval($arPin['COORDS'])))];
        if (!empty($arPin['NAME'])) {
            $pin['title'] = strval($arPin['NAME']);
        }
        if (!empty($arPin['DESCRIPTION'])) {
            $pin['description'] = strval($arPin['DESCRIPTION']);
        }
        $arResult['DETAIL_PICTURE_PINS'][] = $pin;
    }
}

if (!empty($arResult['OFFERS'])) {
    foreach ($arResult['OFFERS'] as &$arOffer) {
        $arOffer['DETAIL_PICTURE_PINS'] = [];
        $arPins = json_decode($arOffer['PROPERTIES']['DETAIL_PICTURE_PINS']['~VALUE'], true);
        if (is_array($arPins)) {
            foreach ($arPins as $arPin) {
                $pin = ['location' => array_reverse(explode(':', strval($arPin['COORDS'])))];
                if (!empty($arPin['NAME'])) {
                    $pin['title'] = strval($arPin['NAME']);
                }
                if (!empty($arPin['DESCRIPTION'])) {
                    $pin['description'] = strval($arPin['DESCRIPTION']);
                }
                $arOffer['DETAIL_PICTURE_PINS'][] = $pin;
            }
        }
    }
    unset($arOffer);

    $colorPicturesMap = [];
    array_walk($arResult['OFFERS'], function ($arOffer) use (&$colorPicturesMap) {
        foreach ($arOffer['PROPERTIES'] as $arProperty) {
            $color = strval(is_array($arProperty['VALUE']) ? reset($arProperty['VALUE']) : $arProperty['VALUE']);
            if ($arProperty['CODE'] !== 'TSVET') {
                continue;
            }

            if (!intval($colorPicturesMap[$color]['~PREVIEW_PICTURE']) && intval($arOffer['~PREVIEW_PICTURE'])) {
                $colorPicturesMap[$color]['~PREVIEW_PICTURE'] = $arOffer['~PREVIEW_PICTURE'];
                $colorPicturesMap[$color]['PREVIEW_PICTURE'] = $arOffer['PREVIEW_PICTURE'];
            }

            if (!intval($colorPicturesMap[$color]['~DETAIL_PICTURE']) && intval($arOffer['~DETAIL_PICTURE'])) {
                $colorPicturesMap[$color]['~DETAIL_PICTURE'] = $arOffer['~DETAIL_PICTURE'];
                $colorPicturesMap[$color]['DETAIL_PICTURE'] = $arOffer['DETAIL_PICTURE'];
            }

            if (!$colorPicturesMap[$color]['MORE_PHOTO']['VALUE'] && $arOffer['PROPERTIES']['MORE_PHOTO']['VALUE']) {
                $colorPicturesMap[$color]['MORE_PHOTO'] = $arOffer['PROPERTIES']['MORE_PHOTO'];
            }
        }
    });
    array_walk($arResult['OFFERS'], function (&$arOffer) use ($colorPicturesMap) {
        foreach ($arOffer['PROPERTIES'] as $arProperty) {
            $color = strval(is_array($arProperty['VALUE']) ? reset($arProperty['VALUE']) : $arProperty['VALUE']);
            if ($arProperty['CODE'] !== 'TSVET') {
                continue;
            }

            if (!intval($arOffer['~PREVIEW_PICTURE']) && intval($colorPicturesMap[$color]['~PREVIEW_PICTURE'])) {
                $arOffer['~PREVIEW_PICTURE'] = $colorPicturesMap[$color]['~PREVIEW_PICTURE'];
                $arOffer['PREVIEW_PICTURE'] = $colorPicturesMap[$color]['PREVIEW_PICTURE'];
            }

            if (!intval($arOffer['~DETAIL_PICTURE']) && intval($colorPicturesMap[$color]['~DETAIL_PICTURE'])) {
                $arOffer['~DETAIL_PICTURE'] = $colorPicturesMap[$color]['~DETAIL_PICTURE'];
                $arOffer['DETAIL_PICTURE'] = $colorPicturesMap[$color]['DETAIL_PICTURE'];
            }

            if (!$arOffer['PROPERTIES']['MORE_PHOTO']['VALUE'] && $colorPicturesMap[$color]['MORE_PHOTO']['VALUE']) {
                $arOffer['PROPERTIES']['MORE_PHOTO'] = $colorPicturesMap[$color]['MORE_PHOTO'];
            }
        }
    });
    unset($colorPicturesMap);
}
