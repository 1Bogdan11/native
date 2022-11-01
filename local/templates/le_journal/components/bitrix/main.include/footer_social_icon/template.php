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

if (empty($arResult['FILE'])) {
    return;
}

$text = file_get_contents($arResult['FILE']);
$link = preg_replace('~[^a-zA-Z0-9?=&%:/#_.-]~', '', $text);

if (empty($link)) {
    return;
}

?>
<li class="socials__item">
    <a href="<?=$link?>"  target="_blank">
        <?php
        switch ($arParams['SOCIAL_TYPE']) {
            case 'INSTAGRAM':
                ?><svg class="i-instagram"><use xlink:href="#i-instagram"></use></svg><?php
                break;

            case 'FACEBOOK':
                ?><svg class="i-facebook"><use xlink:href="#i-facebook"></use></svg><?php
                break;

            case 'YOUTUBE':
                ?><svg class="i-youtube"><use xlink:href="#i-youtube"></use></svg><?php
                break;

            case 'TELEGRAM':
                ?><svg class="i-telegram"><use xlink:href="#i-telegram"></use></svg><?php
                break;

            case 'WHATSAPP':
                ?><svg class="i-whatsapp"><use xlink:href="#i-whatsapp"></use></svg><?php
                break;

            case 'VK':
                ?><svg class="i-vk"><use xlink:href="#i-vk"></use></svg><?php
                break;
        }
        ?>
    </a>
</li>




