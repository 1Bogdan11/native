<?php

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Its\Area\Asset;
use Its\Area\Menu\Menu;
use Its\Area\Right;

class its_area extends \CModule
{
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

        EventManager::getInstance()->registerEventHandler(
            'main',
            'OnBuildGlobalMenu',
            $this->MODULE_ID,
            Menu::class,
            'getModuleMenu',
            100
        );

        EventManager::getInstance()->registerEventHandler(
            'main',
            'OnPageStart',
            $this->MODULE_ID,
            Asset::class,
            'setAssets',
            100
        );

        ModuleManager::registerModule($this->MODULE_ID);
        $this->copyFiles('admin');
        $this->copyFiles('components', true);
        $this->InstallTasks();

        return true;
    }

    /**
     * @throws \Exception
     */
    public function doUninstall()
    {
        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnBuildGlobalMenu',
            $this->MODULE_ID,
            Menu::class,
            'getModuleMenu'
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnPageStart',
            $this->MODULE_ID,
            Asset::class,
            'setAssets'
        );

        $this->UnInstallTasks();
        $this->deleteFiles('admin');
        $this->deleteFiles('components', true);

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
