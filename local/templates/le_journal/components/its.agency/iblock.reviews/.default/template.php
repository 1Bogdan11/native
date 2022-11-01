<?php

use Bitrix\Main\Localization\Loc;
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

$this->setFrameMode(false);

?>
<section class="product-reviews" id="reviews">
    <?php
    if ($arParams['RATING_PROPERTY'] && count($arResult['ITEMS']) > 0) {
        ?>
        <div itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
            <meta itemprop="ratingValue" content="<?=$arResult['REVIEWS_RATING']?>"/>
            <meta itemprop="reviewCount" content="<?=$arResult['REVIEWS_COUNT']?>"/>
        </div>
        <?php
    }
    ?>
    <div class="product-reviews__title" data-scroll data-observe="fade-y">
        <?=Loc::getMessage('ITS_AGENCY_REVIEWS_TITLE')?>
    </div>
    <div class="product-reviews__content" data-scroll data-observe="fade-y">
        <?php
        if (count($arResult['ITEMS']) > 0) {
            ?>
            <div class="product-reviews__left">
                <ul class="product-reviews__list">
                    <?php
                    foreach ($arResult['ITEMS'] as $arItem) {
                        ?>
                        <li class="product-reviews__item <?=($arItem['ACTIVE'] !== 'Y' ? 'moderated' : '')?>" itemprop="review" itemscope itemtype="http://schema.org/Review">
                            <div class="product-reviews__item_inner">
                                <div class="product-reviews__item_part">
                                    <div class="product-reviews__item_name" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                        <span itemprop="name"><?=$arItem['NAME']?></span>
                                        <?php
                                        if ($arItem['ACTIVE'] !== 'Y') {
                                            ?>
                                            <div class="review-item-moderated">
                                                <?=Loc::getMessage('ITS_AGENCY_REVIEWS_MODERATED')?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="product-reviews__item_date"><?=(new DateTime($arItem['DATE_ACTIVE_FROM']))->format('d.m.Y')?></div>
                                    <meta itemprop="datePublished" content="<?=(new DateTime($arItem['DATE_ACTIVE_FROM']))->format('Y-m-d')?>">
                                </div>
                                <div class="product-reviews__item_content" itemprop="description">
                                    <?=$arItem['PREVIEW_TEXT']?>
                                </div>
                                <?php
                                if ($arParams['RATING_PROPERTY']) {
                                    $rating = min(5, max(1, (intval($arItem['PROPERTIES'][$arParams['RATING_PROPERTY']]['VALUE']) ?: 5)));
                                    ?>
                                    <div itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                                        <meta itemprop="worstRating" content="1">
                                        <meta itemprop="bestRating" content="5"/>
                                        <meta itemprop="ratingValue" content="<?=$rating?>">
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            if (!empty($arItem['DETAIL_TEXT'])) {
                                ?>
                                <div class="product-reviews__item_answer">
                                    <div class="product-reviews__item_name"><?=(empty($arItem['PROPERTIES']['ANSWER_NAME']['VALUE']) ? $arItem['PROPERTIES']['ANSWER_NAME']['DEFAULT_VALUE'] : $arItem['PROPERTIES']['ANSWER_NAME']['VALUE']);?></div>
                                    <div class="product-reviews__item_content"><?=$arItem['DETAIL_TEXT']?></div>
                                    <div class="product-reviews__item_date"><?=$arItem['PROPERTIES']['ANSWER_DATE']['VALUE'];?></div>
                                </div>
                                <?php
                            }
                            ?>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <?=$arResult['NAV_STRING']?>
            </div>
            <?php
        }
        ?>
        <div class="product-reviews__right">
            <?php $APPLICATION->IncludeComponent(
                'its.agency:form.sample',
                'reviews',
                [
                    'FOR_ID' => $arParams['FOR_ID'],
                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                    'PROPERTIES' => $arParams['PROPERTIES'],
                    'PROPERTIES_REQUIRE' => $arParams['PROPERTIES_REQUIRE'],
                    'USE_CAPTCHA' => $arParams['USE_CAPTCHA'],
                    'CAPTCHA_PUBLIC' => $arParams['CAPTCHA_PUBLIC'],
                    'CAPTCHA_PRIVATE' => $arParams['CAPTCHA_PRIVATE'],
                    'MAIL_EVENT_NAME' => $arParams['MAIL_EVENT_NAME'],
                    'BUTTON_NAME' => 'reviews_form_send',
                ],
                false
            )?>
        </div>
    </div>
</section>
