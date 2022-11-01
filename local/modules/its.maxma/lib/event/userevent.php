<?php

namespace Its\Maxma\Event;

use Its\Maxma\Api\Maxma;
use Bitrix\Main\Context;

class UserEvent
{
    public static function afterAdd(&$fields): void
    {
        $userId = intval($fields['ID']);
        if ($userId <= 0) {
            return;
        }
        $maxma = Maxma::getInstance();
        $maxmaUserData = $maxma->getUser($userId)->getData();
        if (empty($maxmaUserData)) {
            $maxma->addUser($userId, $fields);
        }
    }

    public static function afterUpdate(&$fields): void
    {
        $userId = intval($fields['ID']);
        if ($userId <= 0) {
            return;
        }
        $maxma = Maxma::getInstance();
        $maxmaUserData = $maxma->getUser($userId)->getData();
        if (!empty($maxmaUserData)) {
            $maxma->updateUser($userId, $fields);
        } else {
            $maxma->addUser($userId, $fields);
        }
    }
}
