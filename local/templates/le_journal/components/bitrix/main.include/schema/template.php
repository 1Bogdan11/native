<?php

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

$content = preg_replace('/[\n\r]+/', PHP_EOL, file_get_contents($arResult['FILE']));
$lines = explode(PHP_EOL, $content);
$imageId = intval(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $arParams['IMAGE']));
$imageUrl = \CFile::GetPath($imageId);
?>
<div itemscope itemtype="http://schema.org/Organization" style="display:none">
    <meta itemprop="name" content="<?=htmlspecialchars($lines[0])?>">
    <meta itemprop="description" content="<?=htmlspecialchars($lines[1])?>">
    <a itemprop="url" href="<?=htmlspecialchars($lines[2])?>"></a>
    <meta itemprop="telephone" content="<?=htmlspecialchars($lines[3])?>">
    <meta itemprop="email" content="<?=htmlspecialchars($lines[4])?>">
    <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
        <meta itemprop="postalCode" content="<?=htmlspecialchars($lines[5])?>">
        <meta itemprop="addressCountry" content="<?=htmlspecialchars($lines[6])?>">
        <meta itemprop="addressRegion" content="<?=htmlspecialchars($lines[7])?>">
        <meta itemprop="addressLocality" content="<?=htmlspecialchars($lines[8])?>">
        <meta itemprop="streetAddress" content="<?=htmlspecialchars($lines[9])?>">
    </div>
    <?php
    if ($imageUrl) {
        ?>
        <img itemprop="image" src="<?=$imageUrl?>" alt="">
        <?php
    }
    ?>
</div>
