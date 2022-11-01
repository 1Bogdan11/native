<?php

namespace Journal\Favorite;

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\UserTable;
use Bitrix\Main\Web\Cookie;

class Favorite
{
    private const SESS_KEY = 'FAVORITE_SAVED';

    private int $userId = 0;
    private int $catalogId = 0;
    private bool $useSession = false;
    private array $items;

    public static function installFavoriteTable(): bool
    {
        $connection = Application::getInstance()->getConnection();
        if (!$connection->isTableExists(FavoriteTable::getTableName())) {
            FavoriteTable::getEntity()->createDbTable();
        }

        return $connection->isTableExists(FavoriteTable::getTableName());
    }

    public function __construct(int $catalogId, int $userId = 0)
    {
        global $USER;
        $this->userId = $userId;
        $this->catalogId = $catalogId;

        if ($USER->IsAuthorized() && ($this->userId <= 0 || $this->userId === intval($USER->GetID()))) {
            $this->userId = intval($USER->GetID());
        }

        if ($this->userId < 1) {
            $this->useSession = true;
        }

        $this->items = $this->getSavedItems();
    }

    public function getItems(): array
    {
        return array_values($this->items);
    }

    public function add(int $productId): bool
    {
        if (in_array($productId, $this->items)) {
            return true;
        }

        $resProduct = ElementTable::getList([
            'filter' => ['=ID' => $productId, '=IBLOCK_ID' => $this->catalogId],
            'select' => ['ID']
        ]);

        if ($resProduct->getSelectedRowsCount() > 0) {
            $this->items[] = $productId;
            return true;
        }

        return false;
    }

    public function remove(int $productId): bool
    {
        $key = array_search($productId, $this->items);
        if ($key !== false) {
            unset($this->items[$key]);
        }

        return !in_array($productId, $this->items);
    }

    private function saveToSession(): void
    {
        $_SESSION[self::SESS_KEY] = json_encode($this->items);

        $cookie = new Cookie(static::SESS_KEY, json_encode($this->items), time() + 86400 * 20);
        $cookie->setSpread(Cookie::SPREAD_DOMAIN);
        $cookie->setDomain(Context::getCurrent()->getServer()->getHttpHost());
        $cookie->setSecure(true);
        $cookie->setHttpOnly(false);

        Context::getCurrent()->getResponse()->addCookie($cookie);
    }

    private function loadFromSession(): array
    {
        $json = $_SESSION[static::SESS_KEY];
        if (empty($json)) {
            $json = Context::getCurrent()->getRequest()->getCookie(static::SESS_KEY);
        }

        $favorite = json_decode($json, true);

        return is_array($favorite) ? $favorite : [];
    }

    public function save(): bool
    {
        if ($this->useSession) {
            $this->saveToSession();
        }

        $resFavoriteUser = FavoriteTable::getList([
            'filter' => ['=USER_ID' => $this->userId]
        ]);

        if ($resFavoriteUser->getSelectedRowsCount() < 1) {
            if ($this->userId > 0) {
                $addFavorite = FavoriteTable::add([
                    'USER_ID' => $this->userId,
                    'FAVORITES' => $this->items
                ]);
                if ($addFavorite->isSuccess()) {
                    return true;
                }
            }

            return $this->useSession;
        }

        $arFavoriteUser = $resFavoriteUser->fetch();
        $updateFavoriteUser = FavoriteTable::update(
            $arFavoriteUser['ID'],
            ['FAVORITES' => $this->items]
        );

        if ($updateFavoriteUser->isSuccess()) {
            return true;
        }

        return false;
    }

    private function getSavedItems(): array
    {
        $arItems = [];

        if ($this->useSession) {
            $arItems = $this->loadFromSession();
        }

        $resUser = UserTable::getList([
            'filter' => ['=ID' => $this->userId],
            'select' => ['ID'],
        ]);
        $arUser = $resUser->fetch();

        if ($arUser) {
            $resFavoriteUser = FavoriteTable::getList([
                'filter' => ['=USER_ID' => $this->userId]
            ]);
            $arFavoriteUser = $resFavoriteUser->fetch();

            if ($arFavoriteUser && count($arFavoriteUser['FAVORITES']) > 0) {
                $arItems = array_unique(array_merge($arItems, $arFavoriteUser['FAVORITES']));
            }
        }

        return $this->actualizeItems($arItems);
    }

    private function actualizeItems(array $arItems): array
    {
        $resElement = ElementTable::getList([
            'filter' => [
                '=ID' => $arItems,
                '=IBLOCK_ID' => $this->catalogId
            ],
            'select' => ['ID']
        ]);

        if ($resElement->getSelectedRowsCount() < 1) {
            return [];
        }

        $items = array_column($resElement->fetchAll(), 'ID');
        foreach ($items as &$item) {
            $item = intval($item);
        }

        return $items;
    }
}
