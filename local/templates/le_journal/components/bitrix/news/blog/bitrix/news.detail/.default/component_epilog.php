<?php

use Journal\OpenGraph;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponent $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
/** @var array $templateData */

OpenGraph::setInnerLink('url', strval($templateData['og:url']));
OpenGraph::setInnerLink('image', strval($templateData['og:image']));
OpenGraph::setProperty('title', strval($templateData['og:title']));
OpenGraph::setProperty('description', strval($templateData['og:description']));
