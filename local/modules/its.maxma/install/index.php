<?php

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Its\Maxma\Asset;
use Its\Maxma\Right;
use Its\Maxma\Event\UserEvent;
use Its\Maxma\Order\DiscountAction;
use Its\Maxma\Event\OrderEvent;
use Its\Maxma\Entity\CertificateTable;
use Its\Maxma\Entity\OrderTable;
use Its\Maxma\Entity\HistoryTable;
use Its\Maxma\Entity\ReturnTable;
use Its\Maxma\Menu\Menu;
use Its\Maxma\Agent\OrderSender;
use Its\Maxma\Event\AdminEvent;

class its_maxma extends \CModule
{
    protected const ORDER_MAIL_EVENTS = [
        'OnOrderNewSendEmail',
        'OnOrderDeliverSendEmail',
        'OnOrderPaySendEmail',
        'OnOrderCancelSendEmail',
        'OnOrderStatusSendEmail',
        'OnOrderRemindSendEmail',
        'OnOrderRecurringSendEmail',
        'OnOrderRecurringCancelSendEmail',
    ];

    public function __construct()
    {
        $arModule = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'module.php';

        $this->MODULE_ID = $arModule['MODULE_ID'];
        $this->MODULE_VERSION = $arModule['MODULE_VERSION'];
        $this->MODULE_VERSION_DATE = $arModule['MODULE_VERSION_DATE'];
        $this->MODULE_NAME = $arModule['MODULE_NAME'];
        $this->MODULE_DESCRIPTION = $arModule['MODULE_DESCRIPTION'];
        $this->PARTNER_NAME = $arModule['PARTNER_NAME'];
        $this->PARTNER_URI = $arModule['PARTNER_URI'];
        $this->MODULE_SORT = $arModule['MODULE_SORT'];
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = $arModule['SHOW_SUPER_ADMIN_GROUP_RIGHTS'];
        $this->MODULE_GROUP_RIGHTS = $arModule['MODULE_GROUP_RIGHTS'];
    }

    /**
     * @throws \Exception
     */
    public function doInstall()
    {
        if (!$this->isBitrixVersion()) {
            return false;
        }

        $event = EventManager::getInstance();

        $event->registerEventHandler(
            'main',
            'OnBuildGlobalMenu',
            $this->MODULE_ID,
            Menu::class,
            'getModuleMenu',
            100
        );

        $event->registerEventHandler(
            'main',
            'OnPageStart',
            $this->MODULE_ID,
            Asset::class,
            'setAssets',
            100
        );

        $event->registerEventHandler(
            'main',
            'OnAfterUserAdd',
            $this->MODULE_ID,
            UserEvent::class,
            'afterAdd',
            100
        );

        $event->registerEventHandler(
            'main',
            'OnAfterUserUpdate',
            $this->MODULE_ID,
            UserEvent::class,
            'afterUpdate',
            100
        );

        $event->registerEventHandler(
            'sale',
            'OnCondSaleActionsControlBuildList',
            $this->MODULE_ID,
            DiscountAction::class,
            'getActionDescription',
            100
        );

        $event->registerEventHandler(
            'sale',
            'OnBeforeSaleOrderFinalAction',
            $this->MODULE_ID,
            OrderEvent::class,
            'beforeOrderFinalAction',
            100
        );

        $event->registerEventHandler(
            'sale',
            'OnAfterSaleOrderFinalAction',
            $this->MODULE_ID,
            OrderEvent::class,
            'afterOrderFinalAction',
            100
        );

        $event->registerEventHandler(
            'sale',
            'OnSaleComponentOrderCreated',
            $this->MODULE_ID,
            OrderEvent::class,
            'componentOrderCreated',
            100
        );

        $event->registerEventHandler(
            'sale',
            'OnSaleOrderSaved',
            $this->MODULE_ID,
            OrderEvent::class,
            'orderSaved',
            100
        );

        $event->registerEventHandler(
            'sale',
            'OnOrderNewSendEmail',
            $this->MODULE_ID,
            OrderEvent::class,
            'orderNewSendEmail',
            1000
        );

        $event->registerEventHandler(
            'main',
            'OnAdminListDisplay',
            $this->MODULE_ID,
            AdminEvent::class,
            'onBuildList',
            1000
        );

        foreach (self::ORDER_MAIL_EVENTS as $eventName) {
            $event->registerEventHandler(
                'sale',
                $eventName,
                $this->MODULE_ID,
                OrderEvent::class,
                'orderSendEmail',
                2000
            );
        }

        ModuleManager::registerModule($this->MODULE_ID);
        $this->copyFiles('admin');
        $this->copyFiles('components', true);
        $this->InstallTasks();
        $this->createTable(CertificateTable::class);
        $this->createTable(OrderTable::class);
        $this->createTable(HistoryTable::class);
        $this->createTable(ReturnTable::class);

        OrderSender::register();

        return true;
    }

    /**
     * @throws \Exception
     */
    public function doUninstall()
    {
        $event = EventManager::getInstance();

        $event->unRegisterEventHandler(
            'main',
            'OnBuildGlobalMenu',
            $this->MODULE_ID,
            Menu::class,
            'getModuleMenu'
        );

        $event->unRegisterEventHandler(
            'main',
            'OnPageStart',
            $this->MODULE_ID,
            Asset::class,
            'setAssets'
        );

        $event->unRegisterEventHandler(
            'main',
            'OnAfterUserAdd',
            $this->MODULE_ID,
            UserEvent::class,
            'afterAdd'
        );

        $event->unRegisterEventHandler(
            'main',
            'OnAfterUserUpdate',
            $this->MODULE_ID,
            UserEvent::class,
            'afterUpdate'
        );

        $event->unRegisterEventHandler(
            'sale',
            'OnCondSaleActionsControlBuildList',
            $this->MODULE_ID,
            DiscountAction::class,
            'getActionDescription'
        );

        $event->unRegisterEventHandler(
            'sale',
            'OnBeforeSaleOrderFinalAction',
            $this->MODULE_ID,
            OrderEvent::class,
            'beforeOrderFinalAction'
        );

        $event->unRegisterEventHandler(
            'sale',
            'OnAfterSaleOrderFinalAction',
            $this->MODULE_ID,
            OrderEvent::class,
            'afterOrderFinalAction'
        );

        $event->unRegisterEventHandler(
            'sale',
            'OnSaleComponentOrderCreated',
            $this->MODULE_ID,
            OrderEvent::class,
            'componentOrderCreated'
        );

        $event->unRegisterEventHandler(
            'sale',
            'OnSaleOrderSaved',
            $this->MODULE_ID,
            OrderEvent::class,
            'orderSaved'
        );

        $event->unRegisterEventHandler(
            'sale',
            'OnOrderNewSendEmail',
            $this->MODULE_ID,
            OrderEvent::class,
            'orderNewSendEmail',
        );

        $event->unRegisterEventHandler(
            'main',
            'OnAdminListDisplay',
            $this->MODULE_ID,
            AdminEvent::class,
            'onBuildList',
        );

        foreach (self::ORDER_MAIL_EVENTS as $eventName) {
            $event->unRegisterEventHandler(
                'sale',
                $eventName,
                $this->MODULE_ID,
                OrderEvent::class,
                'orderSendEmail',
            );
        }

        $this->UnInstallTasks();
        $this->deleteFiles('admin');
        $this->deleteFiles('components', true);
        //$this->dropTable(CertificateTable::class);
        //$this->dropTable(OrderTable::class);
        //$this->dropTable(HistoryTable::class);
        //$this->dropTable(ReturnTable::class);

        OrderSender::unregister();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @param $class
     * @param null $query
     * @return bool
     * @throws \Exception
     */
    private function createTable($class, $query = null)
    {
        /** @var $class DataManager */
        Loader::includeModule($this->MODULE_ID);
        $connection = Application::getInstance()->getConnection();

        if (!$connection->isTableExists($class::getTableName())) {
            $class::getEntity()->createDbTable();

            if (strlen($query) > 0) {
                $connection->query($query);
            }

            return true;
        }

        return false;
    }

    /**
     * @param $class
     * @param null $query
     * @return bool
     * @throws \Exception
     */
    private function dropTable($class, $query = null)
    {
        /** @var $class DataManager */
        Loader::includeModule($this->MODULE_ID);
        $connection = Application::getInstance()->getConnection();

        if ($connection->isTableExists($class::getTableName())) {
            $connection->dropTable($class::getTableName());

            if (strlen($query) > 0) {
                $connection->query($query);
            }

            return true;
        }

        return false;
    }

    private function copyFiles(string $directory, bool $local = false)
    {
        $target = $local ? 'local' : BX_ROOT;
        $of = __DIR__ . DIRECTORY_SEPARATOR . $directory;
        $in = Context::getCurrent()->getServer()->getDocumentRoot() . $target . DIRECTORY_SEPARATOR . $directory;

        if (Directory::isDirectoryExists($of)) {
            CopyDirFiles($of, $in, true, true);
        }
    }

    private function deleteFiles(string $directory, bool $local = false)
    {
        $target = $local ? 'local' : BX_ROOT;
        $of = __DIR__ . DIRECTORY_SEPARATOR . $directory;
        $in = Context::getCurrent()->getServer()->getDocumentRoot() . $target . DIRECTORY_SEPARATOR . $directory;

        if (Directory::isDirectoryExists($of)) {
            DeleteDirFiles($of, $in);
        }
    }

    private function isBitrixVersion()
    {
        return CheckVersion(
            ModuleManager::getVersion('main'),
            '20.00.00'
        );
    }

    public function getModuleRightList()
    {
        Loader::includeModule($this->MODULE_ID);
        return (new Right())->getModuleRights();
    }

    /**
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function getModuleTasks()
    {
        Loader::includeModule($this->MODULE_ID);
        return (new Right())->getModuleTasks();
    }
}
