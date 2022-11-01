<?php

use Bitrix\Main\Localization\Loc;
use Its\Library\Iblock\Iblock;
use Journal\Tool\Template;
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

if (!function_exists('GetPicturesMapForGallery')) {
    function GetPicturesMapForGallery(array $pictures, string $alt = '', array $detailPictureIds = [], array $pins = []): array
    {
        $map = [];
        foreach ($pictures as $pictureId) {
            $src = (new Resize($pictureId, [960, 960]))->getResult();
            $srcset = (new Resize($pictureId, [563, 933]))->getResult();
            $preview = (new Resize($pictureId, [64, 90], BX_RESIZE_IMAGE_EXACT))->getResult();
            $fullscreen = \CFile::GetPath($pictureId);
            $item = [
                'id' => $pictureId,
                'src' => $src['src'],
                'srcset' => $srcset['src'],
                'preview' => $preview['src'],
                'fullscreen' => $fullscreen,
                'title' => $alt,
                'alt' => $alt,
                'pins' => [],
            ];

            if (in_array($pictureId, $detailPictureIds)) {
                $index = array_search($pictureId, $detailPictureIds);
                $item['pins'] = $pins[$index];
            }

            $map[] = $item;
        }
        return $map;
    }
}

$this->setFrameMode(true);

$isHaveOffers = !empty($arResult['OFFERS']);
$arSelectedItem = &$arResult;
$arTreeValues = [];

$arGallery[] = [
    'id' => 'default',
    'photos' => GetPicturesMapForGallery(
        Template::selectPictures($arResult, true),
        $arResult['NAME'],
        [intval($arResult['DETAIL_PICTURE']['ID'])],
        [$arResult['DETAIL_PICTURE_PINS']]
    ),
];
$currentGallery = &$arGallery[0]['photos'];

if ($isHaveOffers) {
    $arSelectedItem = &$arResult['OFFERS'][$arResult['OFFERS_SELECTED']];

    foreach ($arResult['OFFERS'] as $arOffer) {
        $offerGallery = GetPicturesMapForGallery(
            (
                $arResult['DETAIL_PICTURE_PINS']
                    ? [...Template::selectPictures($arOffer, true), intval($arResult['DETAIL_PICTURE']['ID'])]
                    : Template::selectPictures($arOffer, true)
            ),
            $arResult['NAME'],
            [
                intval($arOffer['DETAIL_PICTURE']['ID']),
                intval($arResult['DETAIL_PICTURE']['ID']),
            ],
            [
                $arOffer['DETAIL_PICTURE_PINS'],
                $arResult['DETAIL_PICTURE_PINS'],
            ]
        );
        if (!empty($offerGallery)) {
            $arGallery[] = [
                'id' => intval($arOffer['ID']),
                'photos' => $offerGallery,
            ];
            if ($arSelectedItem['ID'] === $arOffer['ID']) {
                $currentGallery = &$arGallery[count($arGallery) - 1]['photos'];
            }
        }
    }

    foreach ($arResult['JS_OFFERS'] as $arJsOffer) {
        foreach ($arJsOffer['TREE'] as $strProperty => $valueId) {
            $propertyId = str_replace('PROP_', '', $strProperty);
            if (!in_array($valueId, $arTreeValues[$propertyId])) {
                $arTreeValues[$propertyId][] = $valueId;
            }
        }
    }

    foreach ($arResult['SKU_PROPS'] as $key => $arSkuProperty) {
        $propertyId = $arSkuProperty['ID'];
        $propertyCode = $arSkuProperty['CODE'];

        if (!isset($arTreeValues[$propertyId])) {
            continue;
        }

        $arResult['SKU_PROPERTIES'][$key] = $arSkuProperty;
        $arResult['SKU_PROPERTIES'][$key]['NAME'] = htmlspecialchars($arSkuProperty['NAME']);
        $arResult['SKU_PROPERTIES'][$key]['VALUES'] = [];

        foreach ($arSkuProperty['VALUES'] as $arValue) {
            if (!in_array($arValue['ID'], $arTreeValues[$propertyId])) {
                continue;
            }
            $arValue['NAME'] = htmlspecialchars($arValue['NAME']);
            $arResult['SKU_PROPERTIES'][$key]['VALUES'][] = $arValue;
        }
    }

    if (isset($arResult['SKU_PROPERTIES']['TSVET'])) {
        foreach ($arResult['OFFERS'] as $offer) {
            $value = $offer['PROPERTIES']['TSVET']['VALUE'];
            $pictures = Template::selectPictures($offer);
            if (count($pictures) > 0) {
                $arResult['SKU_PROPERTIES']['TSVET']['PICTURES'][$value] = reset($pictures);
            }
        }
    }
}

$arPrice = $arSelectedItem['ITEM_PRICES'][$arSelectedItem['ITEM_PRICE_SELECTED']];




if ($isHaveOffers) {
    $actualItem = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']] ?? reset($arResult['OFFERS']);
} else {
    $actualItem = $arResult;
}
$skuProps = array();

?>
<?php
//its
$displaySizeTable = false;//показывать ссылку на таблицу размеров
$sOverlaySizeForSku = ''; //строка для выбора размера в overlay

?>
<div class="product__hero" id="product-hero">
    <div id="<?=$this->GetEditAreaId($arResult['ID'])?>" class="product__view js-gallery-view" data-scroll>
        <div class="product-gallery js-gallery">
            <div class="product-gallery__pagination swiper-pagination"></div>
            <button class="product-gallery__close js-gallery-close">
                <svg class="i-close"><use xlink:href="#i-close"></use></svg>
            </button>
            <button class="product-gallery__prev js-gallery-prev">
                <svg class="i-arrow"><use xlink:href="#i-arrow"></use></svg>
            </button>
            <button class="product-gallery__next js-gallery-next">
                <svg class="i-arrow"><use xlink:href="#i-arrow"></use></svg>
            </button>
            <div class="icon-scroll media-max--tab">
                <div class="icon-scroll__inner">
                    <div class="mouse">
                        <div class="wheel"></div>
                    </div>
                    <div class="icon-scroll__dots">
                        <span></span>
                    </div>
                </div>
            </div>
            <div class="product-gallery__mobile-popup media-min--tab js-gallery__mobile-popup">
                <div class="product-gallery__mobile-popup_title" data-title></div>
                <div class="product-gallery__mobile-popup_description" data-description></div>
            </div>
            <div class="product-gallery__slider swiper-container js-gallery__slider">
                <div class="swiper-wrapper">
                    <?php
                    foreach ($currentGallery as $arPicture) {
                        ?>
                        <div class="product-gallery__slider_item swiper-slide">
                            <div class="product-gallery__slider_item-outer js-scrollbooster-wrapper">
                                <div class="product-gallery__slider_item-inner js-scrollbooster-content">
                                    <picture>
                                        <source media="(max-width: 1100px)" srcset="<?=$arPicture['srcset']?>">
                                        <img src="<?=$arPicture['src']?>"
                                            alt="<?=$arPicture['alt']?>"
                                            data-fullscreen="<?=$arPicture['fullscreen']?>"
                                            data-preview="<?=$arPicture['preview']?>"
                                            itemprop="image"
                                        />
                                    </picture>
                                    <?php
                                    if (!empty($arPicture['pins'])) {
                                        ?>
                                        <div class="product-gallery__pins">
                                            <?php
                                            foreach ($arPicture['pins'] as $arPin) {
                                                ?>
                                                <div class="product-gallery__pin js-pin" style="left:<?=$arPin['location'][0]?>%;top:<?=$arPin['location'][1]?>%">
                                                    <div class="product-gallery__pin_button js-pin__button"></div>
                                                    <div class="product-gallery__pin_popup js-pin__popup">
                                                        <?php
                                                        if ($arPin['title']) {
                                                            ?>
                                                            <div class="product-gallery__pin_title" data-title><?=$arPin['title']?></div>
                                                            <?php
                                                        }
                                                        ?>
                                                        <?php
                                                        if ($arPin['description']) {
                                                            ?>
                                                            <div class="product-gallery__pin_description" data-description><?=$arPin['description']?></div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
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
            <div class="product-gallery__thumbs js-gallery__thumbs js-toggle-list media-max--tab">
                <?php
                foreach ($currentGallery as $arPicture) {
                    ?>
                    <div class="product-gallery__thumbs_item js-toggle-item">
                        <img src="<?=$arPicture['preview']?>" alt="">
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="product-gallery__intersect">
                <div class="product-gallery__intersect_part" data-intersect="left"></div>
                <div class="product-gallery__intersect_part" data-intersect="right"></div>
                <div class="product-gallery__intersect_part" data-intersect="top"></div>
                <div class="product-gallery__intersect_part" data-intersect="bottom"></div>
            </div>
        </div>
    </div>
    <div class="product__right" data-scroll>
        <form class="product__form js-form" id="jsElementSku">
            <div class="product__head">
                <div class="product__head_inner">
                    <div id="jsBreadcrumbsDesktop" class="breadcrumbs"></div>
                    <div class="product__head_row media-max--tab">
                        <?php Template::printLabels(
                            $arResult,
                            '<div class="label label--#TYPE# product__label"><span>#TITLE#</span></div>'
                        )?>
                        <div class="product__vendor-code">
                            <?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?>
                        </div>
                    </div>
                    <div class="product__head_left">
                        <h1 class="product__title t-h5" itemprop="name">
                            <?=(!empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arResult['NAME'])?>
                        </h1>
                    </div>
                    <div class="product__price">
                        <div class="product__price_new" id="jsElementPrice" <?=($arSelectedItem['CATALOG_AVAILABLE'] !== 'Y' ? 'style="display:none"' : '')?>>
                            <?=$arPrice['PRINT_PRICE']?>
                        </div>
                        <div class="product__price_old" id="jsElementPriceOld" <?=($arSelectedItem['CATALOG_AVAILABLE'] !== 'Y' ? 'style="display:none"' : '')?>>
                            <?php
                            if ($arPrice['BASE_PRICE'] > $arPrice['PRICE']) {
                                echo $arPrice['PRINT_BASE_PRICE'];
                            }
                            ?>
                        </div>
                        <div class="product__status media-max--tab" id="jsElementAvailable">
                            <?php
                            if ($arSelectedItem['CATALOG_AVAILABLE'] === 'Y') {
                                echo Loc::getMessage('CATALOG_ELEMENT_AVAILABLE');
                            } else {
                                echo Loc::getMessage('CATALOG_ELEMENT_NOT_AVAILABLE');
                            }
                            ?>
                        </div>
                        <button class="product__reviews_button media-max--tab" data-scrollto="#reviews">
                            <svg class="i-reviews"><use xlink:href="#i-reviews"></use></svg>
                            <span class="link-underline" id="jsElementReviewsButton">
                                <?=Loc::getMessage('CATALOG_ELEMENT_REVIEWS')?>
                            </span>
                        </button>
                    </div>
                    <div class="product__status media-min--tab" id="jsElementAvailableMobile">
                        <?php
                        if ($arSelectedItem['CATALOG_AVAILABLE'] === 'Y') {
                            echo Loc::getMessage('CATALOG_ELEMENT_AVAILABLE');
                        } else {
                            echo Loc::getMessage('CATALOG_ELEMENT_NOT_AVAILABLE');
                        }
                        ?>
                    </div>
                </div>
                <meta itemprop="sku" content="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?>">
                <div itemprop="brand" itemscope itemtype="https://schema.org/Brand" style="display:none">
                    <meta itemprop="name" content="Le Journal intime">
                </div>
                <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" style="display:none">
                    <link itemprop="url" href="<?=$arResult['DETAIL_PAGE_URL']?>">
                    <meta itemprop="price" content="<?=$arPrice['RATIO_PRICE']?>">
                    <meta itemprop="priceCurrency" content="<?=$arPrice['CURRENCY']?>">
                    <?php
                    if ($arSelectedItem['CATALOG_AVAILABLE'] === 'Y') {
                        ?>
                        <link itemprop="availability" href="http://schema.org/InStock">
                        <?php
                    }
                    ?>
                </div>
                <?php
                if ($isHaveOffers && !empty($arResult['SKU_PROPERTIES'])) {
                    foreach ($arResult['SKU_PROPERTIES'] as $arSkuProperty) {
                        if (empty($arSkuProperty['VALUES'])) {
                            continue;
                        }
                        if ($arSkuProperty['CODE'] === 'RAZMER') {
                            $selectedSize = '&nbsp;';
                            foreach ($arSkuProperty['VALUES'] as $arValue) {
                                if ($arSelectedItem['TREE']["PROP_{$arSkuProperty['ID']}"] == $arValue['ID']) {
                                    $selectedSize = $arValue['NAME'];
                                    break;
                                }
                            }
                            ?>
                            <div class="product__head_footer">
                                <span class="js-product-size__outer" id="jsSelectedSize">
                                    <?=$selectedSize?>
                                </span>
                                <button class="button-bordered media-min--tab" data-modal-open="sizes">
                                    <?=Loc::getMessage('CATALOG_ELEMENT_SELECT_SIZE')?>
                                </button>
                            </div>
                            <?php
                        }
                    }
                }
                ?>
            </div>
            <?php
            if ($isHaveOffers && !empty($arResult['SKU_PROPERTIES'])) {
                foreach ($arResult['SKU_PROPERTIES'] as $arSkuProperty) {
                    if (empty($arSkuProperty['VALUES'])) {
                        continue;
                    }

                    if ($arSkuProperty['CODE'] === 'TSVET') {
                        ?>
                        <div class="product__color" data-property="<?=$arSkuProperty['ID']?>">
                            <div class="product__color_title">
                                <?=$arSkuProperty['NAME']?>
                            </div>
                            <ul class="colors">
                                <?php
                                foreach ($arSkuProperty['VALUES'] as $arValue) {
                                    $propertyUniqueId = "prop_{$arSkuProperty['ID']}_{$arValue['ID']}";
                                    $checked = ($arSelectedItem['TREE']["PROP_{$arSkuProperty['ID']}"] == $arValue['ID'] ? 'checked' : '');
                                    $resize = (new Resize(
                                        intval($arSkuProperty['PICTURES'][$arValue['NAME']]),
                                        [150, 150]
                                    ));
                                    $picture = $resize->isSuccess() ? $resize->getResult()['src'] : '/assets/img/no_photo_sku.svg';
                                    ?>
                                    <div class="colors__item" data-offer-item-wrap>
                                        <input type="checkbox"
                                            id="<?=$propertyUniqueId?>"
                                            value="<?=$arValue['NAME']?>"
                                            name="colors"
                                            data-value="<?=$arValue['ID']?>"
                                            <?=$checked?>
                                        />
                                        <label for="<?=$propertyUniqueId?>">
                                            <img class="colors__item_image" src="<?=$picture?>" alt="">
                                            <div class="colors__item_title">
                                                <?=$arValue['NAME']?>
                                            </div>
                                        </label>
                                    </div>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                        <?php
                    } elseif ($arSkuProperty['CODE'] === 'RAZMER') {
                        ?>
                        <div class="product__size" data-property="<?=$arSkuProperty['ID']?>">
                            <div class="product__size_title"><?=$arSkuProperty['NAME']?></div>
                            <ul class="sizes sizes--inline">
                                <?php
                                foreach ($arSkuProperty['VALUES'] as $arValue) {
                                    $propertyUniqueId = "prop_{$arSkuProperty['ID']}_{$arValue['ID']}";
                                    $checked = ($arSelectedItem['TREE']["PROP_{$arSkuProperty['ID']}"] == $arValue['ID'] ? 'checked' : '');
                                    ?>
                                    <div class="sizes__item" data-offer-item-wrap>
                                        <input type="radio"
                                            name="property_<?=$arSkuProperty['ID']?>"
                                            id="<?=$propertyUniqueId?>"
                                            data-value="<?=$arValue['ID']?>"
                                            data-size-name="<?=htmlspecialchars($arValue['NAME'])?>"
                                            <?=$checked?>
                                        />
                                        <label for="<?=$propertyUniqueId?>">
                                            <?=$arValue['NAME']?>
                                        </label>
                                    </div>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php
                            $isSizeInModel = \CIBlockElement::GetList(
                                ['ID', 'DESC'],
                                [
                                    'IBLOCK_ID' => Iblock::getInstance()->get('sizes'),
                                    'PROPERTY_ITEM' => intval($arResult['ID']),
                                ],
                                false,
                                ['nTopCount' => 1],
                                ['ID']
                            )->SelectedRowsCount() > 0;
                            $isSizeTable = \CIBlockElement::GetList(
                                ['ID', 'DESC'],
                                [
                                    'IBLOCK_ID' => Iblock::getInstance()->get('size_table'),
                                    'ID' => intval($arResult['PROPERTIES']['SIZE_TABLE']['VALUE']),
                                ],
                                false,
                                ['nTopCount' => 1],
                                ['ID']
                            )->SelectedRowsCount() > 0;

                            if ($isSizeInModel || $isSizeTable) {
                                ?>
                                <button class="product__button link__underline"
                                    data-modal-url="/local/ajax/element_size.php?siteId=<?=SITE_ID?>&id=<?=$arResult['ID']?>&table=<?=intval($arResult['PROPERTIES']['SIZE_TABLE']['VALUE'])?>"
                                    data-modal-tab="<?=($isSizeTable ? 'sizes' : 'stories')?>">
                                    <?=Loc::getMessage('CATALOG_ELEMENT_SIZE_TABLE')?>
                                </button>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                        $this->SetViewTarget('MODAL_ELEMENT_SIZES');
                        ?>
                        <section class="modal modal--sizes modal--aside modal--bordered" data-modal="sizes">
                            <button class="modal__overlay" type="button" data-modal-close="sizes">
                                <button class="modal__mobile-close"></button>
                            </button>
                            <div class="modal__container">
                                <div class="modal__content">
                                    <div class="sizes-modal">
                                        <div class="sizes-modal__body">
                                            <ul class="sizes sizes--mobile">
                                                <?php
                                                foreach ($arSkuProperty['VALUES'] as $arValue) {
                                                    $propertyUniqueId = "modal_prop_{$arSkuProperty['ID']}_{$arValue['ID']}";
                                                    $checked = ($arSelectedItem['TREE'][$arSkuProperty['ID']] == $arValue['ID'] ? 'checked' : '');
                                                    ?>
                                                    <div class="sizes__item" data-offer-item-wrap>
                                                        <input type="radio"
                                                               name="modal_property_<?=$arSkuProperty['ID']?>"
                                                               id="<?=$propertyUniqueId?>"
                                                               data-modal-property="<?=$arSkuProperty['ID']?>"
                                                               data-modal-value="<?=$arValue['ID']?>"
                                                        />
                                                        <label for="<?=$propertyUniqueId?>">
                                                            <?=$arValue['NAME']?>
                                                        </label>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <?php
                                        if ($isSizeInModel || $isSizeTable) {
                                            ?>
                                            <div class="sizes-modal__footer"
                                                data-modal-url="/local/ajax/element_size.php?siteId=<?=SITE_ID?>&id=<?=$arResult['ID']?>&table=<?=intval($arResult['PROPERTIES']['SIZE_TABLE']['VALUE'])?>"
                                                data-modal-tab="<?=($isSizeTable ? 'sizes' : 'stories')?>">
                                                <button><?=Loc::getMessage('CATALOG_ELEMENT_SIZE_TABLE')?></button>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <?php
                        $this->EndViewTarget();
                    }
                }
            }
            ?>
            <div class="product__form_footer">
                <div class="product__form_buttons">
                    <button id="jsElementBuyButton" class="button-black">
                        <span><?=Loc::getMessage('CATALOG_ELEMENT_ADD_TO_BASKET')?></span>
                    </button>
                    <button id="jsElementOneClickButton" class="button-bordered" data-modal-open="buy-in-click">
                        <span><?=Loc::getMessage('CATALOG_ELEMENT_ONE_CLICK')?></span>
                    </button>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.product.subscribe',
                        '',
                        [
                            'PRODUCT_ID' => $arResult['ID'],
                            'BUTTON_ID' => 'jsElementSubscribe',
                            'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
                        ],
                        $component,
                        ['HIDE_ICONS' => 'Y']
                    )?>
                </div>
                <?php
                if ($arSelectedItem['CATALOG_AVAILABLE'] === 'Y') {
                    ?>
                    <button class="button-iconic" data-favorite-item="<?=$arResult['ID']?>" data-favorite-item-name="<?=htmlspecialchars($arResult['NAME'])?>">
                        <svg class="i-flag"><use xlink:href="#i-flag"></use></svg>
                        <span data-default="В список желаний" data-active="Убрать из списка"></span>
                    </button>
                    <?php
                }
                ?>
            </div>
            <?php
            if ($arSelectedItem['CATALOG_AVAILABLE'] === 'Y') {
                $APPLICATION->IncludeComponent(
                    'its.maxma:product.info',
                    '',
                    [
                        'PRODUCT_ID' => $arResult['ID'],
                    ],
                    $component,
                    ['HIDE_ICONS' => 'Y']
                );
            }
            if (!empty($arResult['DISPLAY_PROPERTIES'])) {
                ?>
                <table class="product-props">
                    <?php
                    $arEnumIds = [];
                    $arEnumNotes = [];
                    foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty) {
                        if ($arProperty['PROPERTY_TYPE'] === 'L') {
                            if (!is_array($arProperty['VALUE_ENUM_ID'])) {
                                $arProperty['VALUE_ENUM_ID'] = [$arProperty['VALUE_ENUM_ID']];
                            }
                            $arEnumIds = array_merge($arEnumIds, $arProperty['VALUE_ENUM_ID']);
                        }
                    }
                    $arEnumIds = array_unique($arEnumIds);
                    $resNotes = \CIBlockElement::GetList(
                        ['ID' => 'DESC'],
                        [
                            'IBLOCK_ID' => Iblock::getInstance()->get('value_note'),
                            'PROPERTY_PROP_VALUE' => $arEnumIds ?: false,
                        ],
                        false,
                        false,
                        ['ID', 'IBLOCK_ID', 'NAME', 'PREVIEW_TEXT', 'PROPERTY_PROP_VALUE']
                    );
                    while ($arNote = $resNotes->Fetch()) {
                        $arEnumNotes[$arNote['PROPERTY_PROP_VALUE_VALUE']] = $arNote['PREVIEW_TEXT'];
                    }
                    foreach ($arResult['DISPLAY_PROPERTIES'] as $propertyCode => $arProperty) {
                        ?>
                        <tr>
                            <td><?=$arProperty['NAME']?></td>
                            <td>
                                <?php
                                $value = is_array($arProperty['DISPLAY_VALUE'])
                                    ? implode(' / ', $arProperty['DISPLAY_VALUE'])
                                    : $arProperty['DISPLAY_VALUE'];

                                if ($propertyCode === 'COLLECTIONS') {
                                    $resCollections = \CIBlockElement::GetList(
                                        ['ID' => 'DESC'],
                                        [
                                            'IBLOCK_ID' => Iblock::getInstance()->get('collections'),
                                            'ACTIVE' => 'Y',
                                            'PROPERTY_COLLECTION' => $value,
                                        ]
                                    );
                                    if (!empty($arResult['PROPERTIES']['COLLECTION_LINK']['VALUE'])) {
                                        $url = htmlspecialchars($arResult['PROPERTIES']['COLLECTION_LINK']['VALUE']);
                                        $value = "<a href={$url}>{$value}</a>";
                                    } elseif ($arCollection = $resCollections->GetNext()) {
                                        $value = "<a href={$arCollection['DETAIL_PAGE_URL']}>{$value}</a>";
                                    }
                                }

                                if (!is_array($arProperty['VALUE_ENUM_ID'])) {
                                    $arProperty['VALUE_ENUM_ID'] = [$arProperty['VALUE_ENUM_ID']];
                                }

                                $note = [];
                                foreach ($arProperty['VALUE_ENUM_ID'] as $enumId) {
                                    if ($arEnumNotes[$enumId]) {
                                        $note[] = $arEnumNotes[$enumId];
                                    }
                                }

                                if ($note) {
                                    ?>
                                    <div class="product-props__item">
                                        <span><?=$value?></span>
                                        <div class="product-props__hint js-toggle">
                                            <div class="product-props__hint_button js-toggle__btn">?</div>
                                            <div class="product-props__hint_popup js-hint">
                                                <div class="product-props__hint_content js-hint__content">
                                                    <?=implode('<br><br>', $note)?>
                                                </div>
                                                <div class="product-props__hint_close js-toggle__btn">
                                                    <svg class="i-close"><use xlink:href="#i-close"></use></svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    echo $value;
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <?php
            }
            ?>
            <div class="product__form_part">
                <div class="product__form_tabs">
                    <?php
                    if (!empty($arResult['~DETAIL_TEXT'])) {
                        ?>
                        <div class="product__form_tab link__underline">
                            <?=Loc::getMessage('CATALOG_ELEMENT_DESCRIPTION')?>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    if ($arSelectedItem['CATALOG_AVAILABLE'] === 'Y') {
                        ?>
                        <button class="product__form_tab link__underline" data-modal-url="/local/ajax/delivery_and_payment.php?siteId=<?=SITE_ID?>" data-modal-tab="delivery">
                            <span>
                                <?=Loc::getMessage('CATALOG_ELEMENT_DELIVERY')?>
                            </span>
                        </button>
                        <button class="product__form_tab link__underline" data-modal-url="/local/ajax/delivery_and_payment.php?siteId=<?=SITE_ID?>" data-modal-tab="payment">
                            <span>
                                <?=Loc::getMessage('CATALOG_ELEMENT_PAYMENT')?>
                            </span>
                        </button>
                        <?php
                    }
                    ?>
                </div>
                <div class="product-description js-toggle is-active">
                    <?php
                    if (!empty($arResult['~DETAIL_TEXT'])) {
                        ?>
                        <div class="product-description__content js-text-overflow" itemprop="description">
                            <?=$arResult['~DETAIL_TEXT']?>
                        </div>
                        <button class="product-description__button js-toggle__btn">
                            <span
                                data-default="<?=Loc::getMessage('CATALOG_ELEMENT_DETAIL_TEXT_MORE')?>"
                                data-active="<?=Loc::getMessage('CATALOG_ELEMENT_DETAIL_TEXT_HIDE')?>">
                            </span>
                        </button>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </form>
    </div>
</div>
<?php

$arJsParameters = [
    'id' => $arResult['ID'],
    'name' => $arResult['NAME'],
    'canBuy' => $arResult['CAN_BUY'],
    'available' => $arResult['CATALOG_AVAILABLE'] === 'Y',
    'messageAvailable' => Loc::getMessage('CATALOG_ELEMENT_AVAILABLE'),
    'messageNotAvailable' => Loc::getMessage('CATALOG_ELEMENT_NOT_AVAILABLE'),
    'canSubscribe' => false, //$arOffer['CATALOG_SUBSCRIBE'] == 'Y',
    'isHaveOffers' => $isHaveOffers,
    'actionVariableName' => htmlspecialchars($arParams['ACTION_VARIABLE']),
    'quantityVariableName' => htmlspecialchars($arParams['PRODUCT_QUANTITY_VARIABLE']),
    'idVariableName' => htmlspecialchars($arParams['PRODUCT_ID_VARIABLE']),
    'basketUrl' => $arParams['BASKET_URL'],
    'currentPageUrl' => $APPLICATION->GetCurPage(),
    'inBasketMessage' => Loc::getMessage('CATALOG_ELEMENT_IN_BASKET'),
    'addBasketMessage' => Loc::getMessage('CATALOG_ELEMENT_ADD_TO_BASKET'),
    'actionBuyHeadMessage' => Loc::getMessage('CATALOG_ELEMENT_ADD_ACTION_HEAD'),
    'actionBuyTextMessage' => Loc::getMessage('CATALOG_ELEMENT_ADD_ACTION_MESSAGE'),
    'actionBuyLinkMessage' => Loc::getMessage('CATALOG_ELEMENT_ADD_ACTION_LINK'),
    'errorMessage' => Loc::getMessage('CATALOG_ELEMENT_ERROR'),
    'notCanBuyMessage' => Loc::getMessage('CATALOG_ELEMENT_NOT_CAN_BUY'),
    'goToBasketMessage' => Loc::getMessage('CATALOG_ELEMENT_GO_TO_BASKET'),
    'siteId' => SITE_ID,
];

if ($isHaveOffers) {
    $arJsParameters['selectedOffer'] = $arResult['OFFERS_SELECTED'];
    $arJsParameters['offersCount'] = count($arResult['OFFERS']);
    $arJsParameters['offers'] = [];

    foreach ($arResult['OFFERS'] as $offerKey => $arOffer) {
        $arOfferPrice = $arOffer['ITEM_PRICES'][$arOffer['ITEM_PRICE_SELECTED']];

        $arOffer['SKU_TREE'] = [];
        foreach ($arOffer['TREE'] as $strProperty => $value) {
            $propertyId = str_replace('PROP_', '', $strProperty);
            $arOffer['SKU_TREE'][intval($propertyId)] = intval($value);
        }

        $discount = floatval($arOfferPrice['BASE_PRICE']) - floatval($arOfferPrice['PRICE']);

        $arOfferParameters = [
            'id' => $arOffer['ID'],
            'canBuy' => boolval($arOffer['CAN_BUY']),
            'available' => $arOffer['CATALOG_AVAILABLE'] === 'Y',
            'canSubscribe' => false, //$arOffer['CATALOG_SUBSCRIBE'] == 'Y',
            'priceOld' => $discount > 0 ? $arOfferPrice['PRINT_BASE_PRICE'] : '',
            'price' => $arOfferPrice['PRINT_PRICE'],
            'tree' => $arOffer['SKU_TREE'],
            'selectedTree' => [
                'value' => reset($arOffer['SKU_TREE']),
                'property' => key($arOffer['SKU_TREE'])
            ],
        ];

        $arJsParameters['offers'][$offerKey] = $arOfferParameters;
    }
}
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        new CatalogElement(
            <?=json_encode($arJsParameters)?>,
            {
                price: 'jsElementPrice',
                priceOld: 'jsElementPriceOld',
                selectedSize: 'jsSelectedSize',
                offers: 'jsElementSku',
                buyButton: 'jsElementBuyButton',
                oneClickButton: 'jsElementOneClickButton',
                subscribeButton: 'jsElementSubscribe',
                availableWrap: 'jsElementAvailable',
                availableMobileWrap: 'jsElementAvailableMobile',
                setWrap: 'jsElementSetWrap',
            }
        );
    });

    const galleryData = <?=json_encode($arGallery)?>;

    // //при выборе размера, не отрабатывает клик на input. Зыаускаю в ручную
    // let labelSizeCollection = document.querySelectorAll('div.sizes__item label');
    // for (var i = 0; i < labelSizeCollection.length; i++) {
    //     labelSizeCollection[i].onclick = function (event) {
    //         event.target.previousElementSibling.click();
    //     }
    // }

</script>
