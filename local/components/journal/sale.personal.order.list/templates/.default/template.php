<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Delivery\Services\Manager as DeliveryManager;
use Journal\Tool\Template;
use Its\Library\Image\Resize;
use Bitrix\Main\Type\DateTime;

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

if (!empty($arResult['ERRORS']['FATAL']))
{
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showMessage({
                title: <?=json_encode(Loc::getMessage('ORDER_LIST_ERROR'))?>,
                text: <?=json_encode(implode('<br>', $arResult['ERRORS']['FATAL']))?>,
                time: 10000,
            });
        });
    </script>
    <?php
    return;
}

if (!empty($arResult['ERRORS']['NONFATAL']))
{
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showMessage({
                title: <?=json_encode(Loc::getMessage('ORDER_LIST_ERROR'))?>,
                text: <?=json_encode(implode('<br>', $arResult['ERRORS']['NONFATAL']))?>,
                time: 10000,
            });
        });
    </script>
    <?php
    return;
}

if (!count($arResult['ORDERS'])) {
    ?>
    <p>
        <?=Loc::getMessage($arParams['SHOW_TYPE'] === 'HISTORY' ? 'ORDER_LIST_EMPTY_HISTORY' : 'ORDER_LIST_EMPTY_ACTIVE')?>
    </p>
    <?php
    return;
}


foreach ($arResult['ORDERS'] as $key => $arOrderInfo) {
    /** @var DateTime $orderDate */
    $arOrder = $arOrderInfo['ORDER'];
    ?>
    <div class="order-preview-a">
        <div class="order-preview-a__left">
            <div class="order-preview-a__title">
                <?=Loc::getMessage('ORDER_LIST_NAME', [
                    '#DATE#' => FormatDate($arParams['ACTIVE_DATE_FORMAT'], $arOrder['DATE_INSERT']),
                    '#NUMBER#' => $arOrder['ACCOUNT_NUMBER'],
                ])?>
            </div>
            <div class="order-preview-a__subtitle">
                <?php
                foreach ($arOrderInfo['SHIPMENT'] as $arShipment) {
                    if (empty($arShipment)) {
                        continue;
                    }
                    $arDelivery = DeliveryManager::getById($arShipment['DELIVERY_ID']);

                    echo Loc::getMessage('ORDER_LIST_DELIVERY') . ' ';
                    echo $arDelivery['NAME'];
                }
                ?>
            </div>
            <button class="order-preview-a__button" data-modal-url="<?=$arOrder['URL_TO_DETAIL']?>">
                <?=Loc::getMessage('ORDER_LIST_MORE')?>
            </button>
        </div>
        <div class="order-preview-a__images">
            <?php
            foreach (array_values($arOrderInfo['BASKET_ITEMS']) as $i => $arItem) {
                if ($i >= 5) {
                    break;
                }
                $pictures = Template::selectPictures(
                    $arItem,
                    false,
                    count($arItem['OFFERS']) ? $arItem['OFFERS'][$arItem['OFFER_SELECTED_ID']] : []
                );
                $resize = new Resize(
                    intval(reset($pictures)),
                    [300, 300],
                    BX_RESIZE_IMAGE_EXACT
                );
                ?>
                <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="order-preview-a__image">
                    <?=$resize->getPictureTag([
                        'alt' => htmlspecialchars($arItem['NAME']),
                        'no_photo' => '/assets/img/no_photo.svg',
                    ])?>
                </a>
                <?php
            }
            ?>
        </div>
        <div class="order-preview-a__price">
            <?=$arOrder['FORMATED_PRICE']?>
        </div>
    </div>
    <?php
}

echo $arResult['NAV_STRING'];
