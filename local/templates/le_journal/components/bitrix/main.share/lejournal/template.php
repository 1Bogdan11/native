<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<?
$mailto = "&body=" . $arResult['PAGE_URL'] . "?subject=" . $_SERVER['SERVER_NAME'] . "-" . $arResult['PAGE_TITLE'];
if ($arResult["PAGE_URL"]) {
?>
    <ul class="socials">
        <li class="socials__item"><a href="#"  target="_blank">
                <svg class="i-link">
                    <use xlink:href="#i-link"></use>
                </svg>
            </a></li>
        <li class="socials__item is-stroke"><a href="mailto:<?= $mailto ?>">
                <svg class="i-mail">
                    <use xlink:href="#i-mail"></use>
                </svg>
            </a></li>
        <?
        if (is_array($arResult["BOOKMARKS"]) && count($arResult["BOOKMARKS"]) > 0) {
            foreach (array_reverse($arResult["BOOKMARKS"]) as $name => $arBookmark) {
                ?>
                <li class="socials__item media-max--tab"><?= $arBookmark["ICON"] ?></li><?
            }
        }
        ?>
    </ul>
    <?
} else {
    ?><?//=GetMessage("SHARE_ERROR_EMPTY_SERVER")
    ?><?
}
?>