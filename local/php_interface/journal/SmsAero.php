<?php

namespace Journal;

use Bitrix\Main\Error;
use Bitrix\MessageService\Sender\Base;
use Bitrix\MessageService\Sender\Result\SendMessage;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class SmsAero extends Base
{
    private string $login;
    private string $password;
    private SmsAeroSdkV2 $client;

    public function __construct()
    {
        $this->login = 'a.nikolaev@sebbin-lab.ru';
        $this->password = 'oQBB0w7OQ895FYqPNiL12DZskOX';

        $this->client = new SmsAeroSdkV2(
            $this->login,
            $this->password
        );
    }

    public function sendMessage(array $fields): SendMessage
    {
        if (!$this->canUse()) {
            $result = new SendMessage();
            $result->addError(new Error(Loc::getMessage('SMS_AERO_SEND_MESSAGE_USE_ERROR')));
            return $result;
        }

        $result = new SendMessage();
        $response = $this->client->send(
            $fields['MESSAGE_TO'],
            $fields['MESSAGE_BODY'],
            $fields['MESSAGE_FROM'] ?: false
        );

        if (!$response['success']) {
            $message = strval($response['message']);
            if ($response['data']) {
                $message .= ' (' . implode(', ', array_keys($response['data'])) . ')';
            }
            $result->addError(new Error($message));
            return $result;
        }

        return $result;
    }

    public function getShortName()
    {
        return 'smsaero.ru';
    }

    public function getId()
    {
        return 'smsaero';
    }

    public function getName()
    {
        return 'SMS Aero';
    }

    public function canUse()
    {
        return true;
    }

    public function getFromList()
    {
        $response = $this->client->sign_list();
        if ($response['success'] && intval($response['data']['totalCount'])) {
            $names = [];
            for ($i = 0; $i < intval($response['data']['totalCount']); $i++) {
                $names[] = [
                    'id' => $response['data'][$i]['name'],
                    'name' => $response['data'][$i]['name'],
                ];
            }
            return $names;
        }
        return [];
    }
}
