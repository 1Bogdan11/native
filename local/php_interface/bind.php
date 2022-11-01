<?php

use Bitrix\Main\EventManager;
use Journal\OpenGraph;
use Journal\IblockEventHandler;
use Journal\CatalogEventHandler;
use Journal\User;
use Journal\PicturePinInput;
use Journal\PicturePinHandler;
use Journal\OrderEventHandler;
use Journal\Collection\DiscountAction;
use Journal\Collection\InCollectionCondition;
use Journal\Import1C;
use Journal\SmsEvent;
use Journal\SmsAero;
use Journal\Seo;
use Journal\CustomFilter;
use Journal\IblockCatalogPropertyValueInput;
use Journal\DiscountEvent;

$eventManager = EventManager::getInstance();

$eventManager->addEventHandler('main', 'OnEpilog', [OpenGraph::class, 'setDefaults']);

$eventManager->addEventHandler('main', 'OnBeforeUserAdd', [User::class, 'beforeUserSaveAction']);
$eventManager->addEventHandler('main', 'OnBeforeUserUpdate', [User::class, 'beforeUserSaveAction']);
$eventManager->addEventHandler('main', 'OnAfterUserLogout', [User::class, 'afterUserLogout']);

$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementAdd', [IblockEventHandler::class, 'onAfterIBlockElementAddHandler']);
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementUpdate', [IblockEventHandler::class, 'onAfterIBlockElementUpdateHandler']);
$eventManager->addEventHandler('catalog', '\Bitrix\Catalog\Subscribe::onAfterAdd', [CatalogEventHandler::class, 'SubscribeTableOnAfterAddHandler']);

$eventManager->addEventHandler('iblock', 'OnIBlockPropertyBuildList', [PicturePinInput::class, 'getUserTypeDescription']);
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementUpdate', [PicturePinHandler::class, 'afterElementUpdate']);

$eventManager->addEventHandler('sale', 'OnSaleOrderSaved', [OrderEventHandler::class, 'orderSaved']);

$eventManager->addEventHandler('sale', 'OnCondSaleActionsControlBuildList', [DiscountAction::class, 'getActionDescription']);
$eventManager->addEventHandler('sale', 'OnCondSaleActionsControlBuildList', [InCollectionCondition::class, 'getConditionDescription']);

$eventManager->addEventHandler('iblock', 'OnBeforeIBlockPropertyUpdate', [Import1C::class, 'beforePropertyUpdate']);
$eventManager->addEventHandler('iblock', 'OnBeforeIBlockElementUpdate', [Import1C::class, 'beforeElementUpdateAction']);

$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementAdd', [CustomFilter::class, 'afterElementAdd']);
$eventManager->addEventHandler('iblock', 'OnBeforeIBlockElementUpdate', [CustomFilter::class, 'beforeElementUpdate']);
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementUpdate', [CustomFilter::class, 'afterElementUpdate']);
$eventManager->addEventHandler('iblock', 'OnBeforeIBlockElementDelete', [CustomFilter::class, 'beforeElementDelete']);
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementDelete', [CustomFilter::class, 'afterElementDelete']);

$eventManager->addEventHandler('sale', 'OnOrderNewSendEmail', [SmsEvent::class, 'onOrderNewSendEmail']);
$eventManager->addEventHandler('sale', 'OnOrderPaySendEmail', [SmsEvent::class, 'onOrderPaySendEmail']);
$eventManager->addEventHandler('sale', 'OnOrderStatusSendEmail', [SmsEvent::class, 'onOrderStatusSendEmail']);

$eventManager->addEventHandler('messageservice', 'onGetSmsSenders', function () {
    return [new SmsAero()];
});

$eventManager->addEventHandler('main', 'OnEpilog', [Seo::class, 'epilogActions']);

$eventManager->addEventHandler('iblock', 'OnIBlockPropertyBuildList', [IblockCatalogPropertyValueInput::class, 'getUserTypeDescription']);

$eventManager->addEventHandler('sale', '\Bitrix\Sale\Internals\Discount::onAfterAdd', [DiscountEvent::class, 'afterAdd']);
$eventManager->addEventHandler('sale', '\Bitrix\Sale\Internals\Discount::onAfterUpdate', [DiscountEvent::class, 'afterUpdate']);
