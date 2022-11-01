<?php

namespace Its\Sendpulse;


use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Error;

class Sendpulse
{
    private $API_USER_ID;
    private $API_SECRET;
    private $BOOK_ID;


    public function __construct()
    {
        $this->API_USER_ID = Option::get('its.sendpulse', 'API_USER_ID', '');
        $this->API_SECRET = Option::get('its.sendpulse', 'API_SECRET', '');
        $this->BOOK_ID = Option::get('its.sendpulse', 'BOOK_ID', '');
    }

    /**
     * @param array $emails
     * @return \Bitrix\Main\Result
     */
    public function addEmails(array $emails): \Bitrix\Main\Result
    {
        //Example  emails
//        $emails = array(
//            array(
//                'email' => 'subscriberOsman@example.ru',
//                'variables' => array(
//                    'phone' => '+12345678911',
//                    'name' => 'User',
//                )
//            )
//        );

        $result = new \Bitrix\Main\Result();
        //проверим чтобы все email были заполнены и не пустые.
        // Sendpulse и на пустой email возвращает succes
        foreach ($emails as $item) {
            if (!check_email($item['email'])) {
                $result->addError(new Error("Неверный или пустой email"));
                return $result;
            }
        }

        $spFileStorage = new \Sendpulse\RestApi\Storage\FileStorage("/upload/sendpulse/");
        $spApiClient = new \Sendpulse\RestApi\ApiClient($this->API_USER_ID, $this->API_SECRET, $spFileStorage);
        $spResult = $spApiClient->addEmails($this->BOOK_ID, $emails);

        if ($spResult->result) {
        } elseif ($spResult->is_error) {
            $result->addError(new Error($spResult->message));
        }
        return $result;
    }

    /**
     * проверка email в адресной книге
     * @param $email
     * @return bool
     */
    public function chekEmailInBook($email): bool
    {
        $result = false;
        if (!check_email($email)) {
            return $result;
        }
        $spFileStorage = new \Sendpulse\RestApi\Storage\FileStorage("/upload/sendpulse/");
        $spApiClient = new \Sendpulse\RestApi\ApiClient($this->API_USER_ID, $this->API_SECRET, $spFileStorage);
        $spResult = $spApiClient->getEmailInfo($this->BOOK_ID, $email);

        if ($spResult->is_error) {
            $result = false;
        } else {
            $result = true;
        }
        unset ($spApiClient);
        unset ($spResult);

        return $result;
    }

    /**
     * удалить email из адресной книги
     * @param $email
     * @return \Bitrix\Main\Result
     */
    public function removeEmail($email): \Bitrix\Main\Result
    {
        $result = new \Bitrix\Main\Result();
        //проверим чтобы все email были заполнены и не пустые.
        // Sendpulse и на пустой email возвращает succes
        if (!check_email($email)) {
            $result->addError(new Error("Неверный или пустой email"));
            return $result;
        }
        $spFileStorage = new \Sendpulse\RestApi\Storage\FileStorage("/upload/sendpulse/");
        $spApiClient = new \Sendpulse\RestApi\ApiClient($this->API_USER_ID, $this->API_SECRET, $spFileStorage);
        $spResult = $spApiClient->removeEmails($this->BOOK_ID, [$email]);
        if ($spResult->result) {
            //успешный запрос
        } elseif ($spResult->is_error) {
            $result->addError(new Error($spResult->message));
        }

        unset ($spApiClient);
        unset ($spResult);

        return $result;
    }
}
