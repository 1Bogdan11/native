<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (empty($arResult)) {
    return;
}

?>
<ul class="footer__menu">
    <?php
    foreach (array_values($arResult) as $i => $arItem) {
        if ($arItem['PERMISSION'] <= 'D') {
            continue;
        }
        if (empty($arItem['LINK']) && $i === 0) {
            ?>
            <li>
                <span class="footer__text--b">
                    <?=$arItem['TEXT']?>
                </span>
            </li>
            <?php
            continue;
        }
        ?>
        <li>
            <a class="t-link--m link__underline--hover" href="<?=$arItem['LINK']?>">
                <?=$arItem['TEXT']?>
            </a>
        </li>
        <?php
    }
    ?>
</ul>
