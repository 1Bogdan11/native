<?php

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

$this->setFrameMode(true);

$img = [];
if (is_array($arResult['DETAIL_PICTURE'])) {
    $img = $arResult['DETAIL_PICTURE'];
} elseif (is_array($arResult['PREVIEW_PICTURE'])) {
    $img = $arResult['PREVIEW_PICTURE'];
}

$datePublic = new DateTime($arResult['ACTIVE_FROM']);
$dateModify = new DateTime($arResult['TIMESTAMP_X']);

?>
<div class="article__inner" itemscope itemtype="<?=($arResult['ADD_DATA']['SECTION'][$arResult['IBLOCK_SECTION_ID']]['UF_IS_NEWS'] ? 'https://schema.org/NewsArticle' : 'https://schema.org/Article')?>">
    <link itemprop="mainEntityOfPage" href="<?=$arResult['DETAIL_PAGE_URL']?>">
    <link itemprop="image" href="<?=$img['SRC']?>">
    <meta itemprop="headline name" content="<?=$arResult['NAME']?>">
    <meta itemprop="description" content="<?=TruncateText($arResult['PREVIEW_TEXT'], 150)?>">
    <meta itemprop="author" content="Le Journal Intime">
    <meta itemprop="datePublished" datetime="<?=$datePublic->format('Y-m-d')?>" content="<?=$datePublic->format('Y-m-d')?>">
    <meta itemprop="dateModified" datetime="<?=$dateModify->format('Y-m-d')?>" content="<?=$dateModify->format('Y-m-d')?>">

    <div class="article__head">
        <h1 class="star-title" data-observe="fade-y" data-scroll>
            <svg class="i-subtract"><use xlink:href="#i-subtract"></use></svg>
            <span>
                <?=$arResult['NAME']?>
            </span>
        </h1>
        <div class="article__head-date" data-observe="fade-y" data-scroll>
            <?php
            if ($arResult['IBLOCK_SECTION_ID'] && $arResult['ADD_DATA']['SECTION'][$arResult['IBLOCK_SECTION_ID']]) {
                ?>
                <div class="article__head-tag">
                    <?=$arResult['ADD_DATA']['SECTION'][$arResult['IBLOCK_SECTION_ID']]['NAME'];?>
                </div>
                <?php
            }
            ?>
            <div class="diary-card__date">
                <?php
                $date = new DateTime($arResult['ACTIVE_FROM']);
                echo FormatDate($date->format('Y') === (new DateTime())->format('Y') ? 'j F' : 'j F Y', $date);
                ?>
            </div>
        </div>
    </div>
    <div class="article__content">
        <div class="article__image article__image--full" data-observe="fade-y" data-scroll>
            <?php
            if ($img) {
                ?>
                <img src="<?=$img['SRC']?>" alt="<?=htmlspecialchars($arResult['NAME'])?>">
                <?php
            }
            ?>
        </div>
        <div class="article__authors" data-observe="fade-y" data-scroll>
            <div class="article__authors-head">
                <div class="article__authors-head_inner">
                    <?php
                    if ($arResult['DISPLAY_PROPERTIES']['AUTHOR_TEXT']['VALUE'] != '') { ?>
                        <div class="article__authors-head_part">
                            <div class="article__authors-head_title"><?= $arResult['DISPLAY_PROPERTIES']['AUTHOR_TEXT']['NAME'] ?></div>
                            <div class="article__authors-head_author"><?= $arResult['DISPLAY_PROPERTIES']['AUTHOR_TEXT']['VALUE'] ?></div>
                        </div>
                        <?php
                    }
                    if ($arResult['DISPLAY_PROPERTIES']['AUTHOR_PHOTO']['VALUE'] != '') { ?>
                        <div class="article__authors-head_part">
                            <div class="article__authors-head_title"><?= $arResult['DISPLAY_PROPERTIES']['AUTHOR_PHOTO']['NAME'] ?></div>
                            <div class="article__authors-head_author"><?= $arResult['DISPLAY_PROPERTIES']['AUTHOR_PHOTO']['VALUE'] ?></div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
                if (array_key_exists("USE_SHARE", $arParams) && $arParams["USE_SHARE"] == "Y") {
                    ?>
                    <div class="news-detail-share">
                        <noindex>
                            <?php
                            $APPLICATION->IncludeComponent(
                                'bitrix:main.share',
                                'lejournal',
                                [
                                    'HANDLERS' => $arParams['SHARE_HANDLERS'],
                                    'PAGE_URL' => $arResult['~DETAIL_PAGE_URL'],
                                    'PAGE_TITLE' => $arResult['~NAME'],
                                    'SHORTEN_URL_LOGIN' => $arParams['SHARE_SHORTEN_URL_LOGIN'],
                                    'SHORTEN_URL_KEY' => $arParams['SHARE_SHORTEN_URL_KEY'],
                                    'HIDE' => $arParams['SHARE_HIDE'],
                                ],
                                $component,
                                ['HIDE_ICONS' => 'Y']
                            );
                            ?>
                        </noindex>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="article__authors-content">
                <?php
                if ($arParams['DISPLAY_PREVIEW_TEXT'] != 'N' && $arResult['FIELDS']['PREVIEW_TEXT']) {
                    echo $arResult['FIELDS']['PREVIEW_TEXT'];
                    unset($arResult['FIELDS']['PREVIEW_TEXT']);
                }
                ?>
            </div>
        </div>
        <div class="article__centered-block">
            <?=$arResult["DETAIL_TEXT"]?>
        </div>
    </div>
</div>

<?php
$templateData['og:title'] = $arResult['NAME'];
$templateData['og:description'] = $arResult['PREVIEW_TEXT'];
$templateData['og:url'] = $arResult['DETAIL_PAGE_URL'];
$templateData['og:image'] = $img['SRC'];
