<?php

namespace Journal;

use Bitrix\Main\Result;
use Its\Sendpulse\Sendpulse;
use Bitrix\Main\Loader;

class SubscribeManager
{
    protected Sendpulse $service;

    public function __construct()
    {
        Loader::includeModule('its.sendpulse');
        $this->service = new Sendpulse();
    }

    public function add(string $email): Result
    {
        return $this->service->addEmails([['email' => $email]]);
    }

    public function remove(string $email): Result
    {

        return $this->service->removeEmail($email);
    }

    public function check(string $email): bool
    {
        if (empty($email)) {
            return false;
        }
        return $this->service->chekEmailInBook($email);
    }
}
