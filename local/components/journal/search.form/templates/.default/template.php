<?php

use Bitrix\Main\Localization\Loc;

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
<div class="modal__content">
    <button class="modal__close" data-modal-close="search"></button>
    <div class="container section__wrapper">
        <section class="catalog-section">
            <div class="catalog-section__head catalog-section__head--search" data-scroll>
                <form class="search-form media-max--tab" action="<?=SITE_DIR?>catalog/">
                    <div class="input">
                        <input type="text" name="q" required id="jsSearchInput">
                        <label class="input__label">
                            <?=Loc::getMessage('INPUT_PLACEHOLDER')?>
                        </label>
                        <div class="input__bar"></div>
                    </div>
                    <button type="submit" class="search-form__submit">
                        <svg class="i-search"><use xlink:href="#i-search"></use></svg>
                    </button>
                </form>
            </div>
            <div class="catalog-section__search" id="jsSearchWrap"></div>
        </section>
    </div>
</div>

<script>
    function getSearchResult() {
        let wrap = document.getElementById('jsSearchWrap');
        showPreloader(wrap);
        axios({
            url: '<?=SITE_DIR?>catalog/',
            method: 'post',
            params: {
                q: document.getElementById('jsSearchInput').value,
                ajax_result: 'Y',
            },
            data: {},
            timeout: 0,
            responseType: 'text',
        }).then(function (response) {
            let parser, html, content;
            parser = new DOMParser();
            html = parser.parseFromString(response.data, 'text/html');
            content = html.querySelector('#jsSearchResultWrap');
            setTimeout(() => {
                wrap.innerHTML = content ? content.innerHTML : '';
            }, 700)
            setTimeout(() => {
                hidePreloader(wrap);
            }, 1200)
        });
    }

    const searchHandler = () => {
        clearTimeout(document.searchTimeout);
        document.searchTimeout = setTimeout(getSearchResult, 500);
    }

    document.getElementById('jsSearchInput').addEventListener('keydown', searchHandler);
    document.getElementById('jsSearchInput').addEventListener('paste', searchHandler);
</script>
