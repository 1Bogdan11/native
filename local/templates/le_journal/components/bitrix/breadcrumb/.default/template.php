<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */

if (empty($arResult)) {
    return '';
}

$result = '<ul itemscope itemtype="http://schema.org/BreadcrumbList">';
for ($i = 0; $i < count($arResult); $i++) {
    $title = htmlspecialchars($arResult[$i]['TITLE']);
    $link = htmlspecialchars($arResult[$i]['LINK']);
    $number = $i + 1;
    $meta = "<meta itemprop='name' content='{$title}'>";
    $meta .= "<meta itemprop='position' content='{$number}'>";
    if (!empty($link) && $i !== (count($arResult) - 1)) {
        $result .= "<li itemscope itemprop='itemListElement' itemtype='http://schema.org/ListItem'><a itemprop='item' class='link__underline' href='{$link}'>{$title}{$meta}</a></li>";
    } else {
        $result .= "<li><span>{$title}</span></li>";
    }
}
$result .= '</ul>';

return $result;
