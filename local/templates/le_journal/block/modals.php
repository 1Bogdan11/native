<?php

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @global CMain $APPLICATION */
/** @global CUser $USER */

?>
<script>
    <?php /* Выполнение скриптов в модалках, загруженных аяксом */?>
    document.addEventListener('afterModalLoad', event => {
        if (Tool.inArray(event.detail.modalName, ['ordering-modal','basket-modal', 'ordering-success'])) {
            Tool.evalScripts(document.querySelector(`[data-modal="${event.detail.modalName}"]`));
        }
    });
</script>

<div class="modals js-modals"></div>

<?php $APPLICATION->ShowViewContent('MODAL_SITE_SELECTOR')?>
<?php $APPLICATION->ShowViewContent('MODAL_CATALOG_FILER')?>
<?php $APPLICATION->ShowViewContent('MODAL_CATALOG_CATEGORIES')?>
<?php $APPLICATION->ShowViewContent('MODAL_CATALOG_COLLECTIONS')?>
<?php $APPLICATION->ShowViewContent('MODAL_ELEMENT_SIZES')?>
<?php $APPLICATION->ShowViewContent('MODAL_ONE_CLICK')?>
<?php $APPLICATION->ShowViewContent('SHOPS_MAP_MODAL')?>

<section class="modal modal--mobile-menu modal--aside" data-modal="mobile-menu" data-scroll-disable-delay>
    <button class="modal__overlay" type="button" data-modal-close="mobile-menu">
        <button class="modal__mobile-close"></button>
    </button>
    <div class="modal__container">
        <div class="modal__content">
            <div class="mobile-menu__content">
                <div class="mobile-menu__part is-active" data-mobile-part="menu">
                    <ul class="mobile-menu__list">
                        <li class="mobile-menu__list_item">
                            <a href="<?=SITE_DIR?>catalog/">
                                <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_CATALOG')?>
                            </a>
                        </li>
                        <li class="mobile-menu__list_item">
                            <a href="<?=SITE_DIR?>catalog/collections/">
                                <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_COLLECTIONS')?>
                            </a>
                        </li>
                        <li class="mobile-menu__list_item">
                            <a href="<?=SITE_DIR?>about/">
                                <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_ABOUT')?>
                            </a>
                        </li>
                        <li class="mobile-menu__list_item">
                            <a href="<?=SITE_DIR?>blog/">
                                <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_BLOG')?>
                            </a>
                        </li>
                        <li class="mobile-menu__list_item">
                            <a href="<?=SITE_DIR?>shops/">
                                <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_STORES')?>
                            </a>
                        </li>
                        <li class="mobile-menu__list_item">
                            <a href="<?=SITE_DIR?>contacts/">
                                <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_CONTACTS')?>
                            </a>
                        </li>

                    </ul>
                    <div class="mobile-menu__bottom">
                        <?php $APPLICATION->IncludeComponent(
                            'bitrix:main.include',
                            'header_phone',
                            [
                                'AREA_FILE_SHOW' => 'file',
                                'PATH' => SITE_TEMPLATE_PATH . '/include_area/' . LANGUAGE_ID . '/template_phone.php',
                            ],
                            true
                        )?>
                        <div class="mobile-menu__bottom_part">
                            <?php
                            if (!$USER->IsAuthorized()) {
                                ?>
                                <button class="t-link" data-modal-open="profile-modal">
                                    <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_PROFILE')?>
                                </button>
                                <?php
                            } else {
                                ?>
                                <a class="t-link" href="<?=SITE_DIR?>personal/">
                                    <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_PROFILE')?>
                                </a>
                                <?php
                            }
                            ?>
                            <button class="t-link" data-modal-open="search">
                                <?=Loc::getMessage('HEADER_MAIN_MENU_ITEM_SEARCH')?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="modal modal--search modal--full modal--bordered" data-modal="search">
    <button class="modal__overlay" type="button" data-modal-close="search">
        <button class="modal__mobile-close"></button>
    </button>
    <div class="modal__container">
        <?php $APPLICATION->IncludeComponent(
            'journal:search.form',
            ''
        )?>
    </div>
</section>

<?php
if (!$USER->IsAuthorized()) {
    ?>
    <section class="modal modal--profile-modal modal--right" data-modal="profile-modal" data-tabs-modal>
        <button class="modal__overlay" type="button" data-modal-close="profile-modal">
            <button class="modal__mobile-close" data-modal-close="profile-modal"></button>
        </button>
        <div class="modal__container">
            <div class="modal__content">
                <?php $APPLICATION->IncludeComponent(
                    'its.agency:phone.auth',
                    '',
                    [
                        'DEV_MODE' => 'N',
                        'USER_PHONE_FIELD' => 'PERSONAL_PHONE',
                        'USER_PHONE_ADDITIONAL_FIELDS' => [
                            'LAST_NAME',
                            'NAME',
                            'SECOND_NAME',
                            'EMAIL',
                        ],
                        'USER_PHONE_ADDITIONAL_FIELDS_REQUIRED' => [
                            'LAST_NAME',
                            'NAME',
                            'EMAIL',
                        ],
                        'USE_BACK_URL' => 'Y',
                        'CONFIRM_CODE_LENGTH' => 4,
                        'RESEND_LIMIT' => 30,
                        'SMS_EVENT_CODE' => 'SMS_USER_CONFIRM_NUMBER',
                    ],
                    false,
                    ['HIDE_ICONS' => 'Y']
                )?>
            </div>
        </div>
    </section>
    <?php
}
?>

<section class="modal modal--aside-modal modal--aside" data-modal="aside-modal" data-tabs-modal>
    <button class="modal__overlay" type="button" data-modal-close="aside-modal">
        <button class="modal__mobile-close"></button>
    </button>
    <div class="modal__container">
        <?php include __DIR__ . '/modals/aside-modal.php'?>
    </div>
</section>

<section class="modal modal--want-to-buy modal--right" data-modal="want-to-buy">
    <button class="modal__overlay" type="button" data-modal-close="want-to-buy">
        <button class="modal__mobile-close"></button>
    </button>
    <div class="modal__container">
        <div class="modal__content">
            <button class="modal__close media-min--tab" data-modal-close="want-to-buy"></button>
            <?php $APPLICATION->ShowViewContent('element_subscribe_form')?>
        </div>
    </div>
</section>
