<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
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
?>
<?
if (0 < $arResult["SECTIONS_COUNT"]) {
    ?>
    <ul class="tags js-tags">
        <?
        foreach ($arResult['SECTIONS'] as &$arSection) {
            $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
            $this->AddDeleteAction(
                $arSection['ID'],
                $arSection['DELETE_LINK'],
                $strSectionDelete,
                $arSectionDeleteParams
            );
            $activeClass = ($arSection['CODE'] == $arParams['ACTIVE_SECTION_CODE']) ? 'is-active' : '';
            ?>
            <li class="tags__item js-tag <?= $activeClass ?>" id="<?
            echo $this->GetEditAreaId($arSection['ID']); ?>">
                <a href="<?= $arSection['SECTION_PAGE_URL']; ?>">
                    <?= $arSection['NAME']; ?>
                </a>
            </li>
            <?
        }
        ?>
        <li class="tags__item tags__item--more">
            <button class="js-tags-more">...</button>
        </li>
    </ul>
    <?
}
?>