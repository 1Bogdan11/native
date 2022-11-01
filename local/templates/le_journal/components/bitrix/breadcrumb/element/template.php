<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */

if (empty($arResult)) {
    return '';
}

$items = '';
for ($i = 0; $i < count($arResult); $i++) {
    $title = htmlspecialchars($arResult[$i]['TITLE']);
    $link = htmlspecialchars($arResult[$i]['LINK']);
    $number = $i + 1;
    $meta = "<meta itemprop='name' content='{$title}'>";
    $meta .= "<meta itemprop='position' content='{$number}'>";
    if (!empty($link) && $i !== (count($arResult) - 1)) {
        $items .= "<li itemscope itemprop='itemListElement' itemtype='http://schema.org/ListItem'><a itemprop='item' class='link__underline' href='{$link}'>{$title}{$meta}</a></li>";
    } else {
        $items .= "<li><span>{$title}</span></li>";
    }
}

ob_start();

?>
<template id="jsBreadcrumbsDesktopTemplate" style="display:none;">
    <ul itemscope itemtype="http://schema.org/BreadcrumbList"><?=$items?></ul>
</template>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let template = document.getElementById('jsBreadcrumbsDesktopTemplate');
        let wrapDesktop = document.getElementById('jsBreadcrumbsDesktop');
        if (template && wrapDesktop) {
            wrapDesktop.innerHTML = template.innerHTML;
            template.parentNode.removeChild(template);
        }
    });
</script>
<?php

return ob_get_clean();
