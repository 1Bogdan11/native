<?php

use Bitrix\Main\Context;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;
use Bitrix\Main\PhoneNumber\PhoneNumber;
use Bitrix\Main\Result;
use Bitrix\Main\Sms\Event;
use Bitrix\Main\UserTable;

class PhoneAuthComponent extends \CBitrixComponent implements Controllerable
{
    public array $errorCollection = [];

    public const SESS_KEY = 'PHONE_AUTH_COMPONENT';
    public const SESS_KEY_ALL = 'PHONE_AUTH_COMPONENT_ALL';
    protected static array $phonesCache = [];

    public function configureActions()
    {
        return [
            'confirmCode' => [
                'prefilters' => [
                    new HttpMethod([HttpMethod::METHOD_POST]),
                    new Csrf(),
                ]
            ]
        ];
    }

    public function executeComponent(): void
    {
        global $APPLICATION;

        if (!Loader::includeModule('messageservice')) {
            return;
        }

        $this->setSessionParameters();

        if ($this->arParams['USE_BACK_URL'] === 'Y') {
            $this->arParams['BACK_URL'] = strval($this->arParams['BACK_URL'] ?? $this->request['back_url']);
        }

        if (strlen($this->arParams['BACK_URL']) === 0) {
            $this->arParams['BACK_URL'] = $APPLICATION->GetCurPageParam('', ['change_number', 'redirect_from_json']);
        }

        $this->arResult['ERRORS'] = [];
        $this->arResult['FIELDS'] = [];

        $this->processComponent();
        $this->includeComponentTemplate();
    }

    protected function setSessionParameters(): void
    {
        $_SESSION[self::SESS_KEY]['PARAMS'] = [
            'USER_PHONE_FIELD' => $this->arParams['USER_PHONE_FIELD'],
            'CONFIRM_CODE_LENGTH' => $this->arParams['CONFIRM_CODE_LENGTH'],
            'RESEND_LIMIT' => $this->arParams['RESEND_LIMIT'],
            'SMS_EVENT_CODE' => $this->arParams['SMS_EVENT_CODE'],
            'DEV_MODE' => $this->arParams['DEV_MODE'],
        ];
    }

    protected static function getSessionParameters(): array
    {
        if (is_array($_SESSION[self::SESS_KEY]['PARAMS'])) {
            return $_SESSION[self::SESS_KEY]['PARAMS'];
        }
        return [];
    }

    private function processComponent(): void
    {
        global $USER;
        if ($USER->IsAuthorized()) {
            $this->arResult['TEMPLATE_TYPE'] = 'SUCCESS';
            return;
        }

        if ($this->request['change_number'] === 'Y') {
            unset($_SESSION[self::SESS_KEY]['LAST_PHONE']);
        }

        $arLastPhone = $_SESSION[self::SESS_KEY]['LAST_PHONE'];
        $phoneNumber = strval($arLastPhone['NUMBER']);
        $country = strval($arLastPhone['COUNTRY']);
        $phoneHash = self::getPhoneHash($phoneNumber, $country);
        $number = self::getPhoneNumber($phoneNumber, $country);

        if ($_SESSION[self::SESS_KEY]['PHONES'][$phoneHash]['CONFIRM'] === 'Y') {
            if (!UserTable::getEntity()->hasField(strval($this->arParams['USER_PHONE_FIELD']))) {
                $this->arResult['ERRORS'][] = "Invalid field \"{$this->arParams['USER_PHONE_FIELD']}\" in User entity!";
                $this->arResult['TEMPLATE_TYPE'] = '';
                return;
            }

            $resUser = UserTable::getList([
                'filter' => [
                    strval($this->arParams['USER_PHONE_FIELD']) => $number->format(Format::E164)
                ]
            ]);

            if (!$arUser = $resUser->fetch()) {
                $arUser = [];
            }

            $arFields = $this->combineAdditionalFields($arUser);

            if (!$this->checkAdditionalFields($arFields)) {
                $this->arResult['TEMPLATE_TYPE'] = 'LAST_STEP';
                return;
            }

            $authId = 0;
            $user = new \CUser();

            if ($arUser && ($arUser['ACTIVE'] !== 'Y' || $arUser['BLOCKED'] === 'Y')) {
                $this->arResult['ERRORS'][] = Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_ERROR_USER_BANNED');
                $this->arResult['TEMPLATE_TYPE'] = '';
                return;
            } elseif ($arUser) {
                if (!$this->compareAdditionalFields($arUser, $arFields)) {
                    $update = $user->Update($arUser['ID'], $arFields);
                    if ($update === false) {
                        $this->arResult['ERRORS'][] = $user->LAST_ERROR;
                        $this->arResult['TEMPLATE_TYPE'] = '';
                        return;
                    }
                }
                $authId = intval($arUser['ID']);
            } else {
                $arFields = array_merge(
                    $arFields,
                    [
                        strval($this->arParams['USER_PHONE_FIELD']) => $number->format(Format::E164),
                        'LOGIN' => $arFields['EMAIL'],
                        'PASSWORD' => md5($arFields['EMAIL'] . self::generateConfirmCode(10)),
                    ]
                );
                $add = $user->Add($arFields);
                if (intval($add) === 0) {
                    $this->arResult['ERRORS'][] = $user->LAST_ERROR;
                    $this->arResult['TEMPLATE_TYPE'] = '';
                    return;
                }
                $authId = intval($add);
            }

            if ($authId > 0) {
                $USER->Authorize($authId, true);
                unset($_SESSION[self::SESS_KEY]['LAST_PHONE']);
                if ($this->request['redirect_from_json'] === 'Y') {
                    $this->breakJsonRedirect();
                } else {
                    LocalRedirect($this->arParams['BACK_URL']);
                    die();
                }
            }
        }

        $this->arResult['TEMPLATE_TYPE'] = '';
    }

    private function breakJsonRedirect(): void
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        $response = Context::getCurrent()->getResponse();
        $response->getHeaders()->set('Content-Type', 'application/json');
        $response->setStatus('200 OK');
        $response->flush(json_encode(['redirect' => $this->arParams['BACK_URL']]));
    }

    private function combineAdditionalFields(array $arUser): array
    {
        $arFields = [];
        if (empty($this->arParams['USER_PHONE_ADDITIONAL_FIELDS'])) {
            return $arFields;
        }

        foreach ($this->arParams['USER_PHONE_ADDITIONAL_FIELDS'] as $fieldCode) {
            $arFields[$fieldCode] = trim($this->request[$fieldCode] ?? $arUser[$fieldCode]);
        }

        return $arFields;
    }

    private function compareAdditionalFields(array $arUser, array $arFields): bool
    {
        foreach ($arFields as $code => $value) {
            if (!isset($arUser[$code]) || $arUser[$code] !== $value) {
                return false;
            }
        }

        return true;
    }

    private function checkAdditionalFields(array $arFields = null): bool
    {
        if (empty($this->arParams['USER_PHONE_ADDITIONAL_FIELDS'])) {
            return true;
        }

        $result = true;
        foreach ($arFields as $code => $value) {
            $this->arResult['FIELDS'][$code] = [
                'CODE' => $code,
                'FIELD_NAME' => $code,
                'REQUIRED' => 'N',
                'VALUE' => $value,
            ];
            if (in_array($code, $this->arParams['USER_PHONE_ADDITIONAL_FIELDS_REQUIRED'])) {
                $this->arResult['FIELDS'][$code]['REQUIRED'] = 'Y';
                if (empty($value)) {
                    if ($this->request['SAVE_ADDITIONAL_FIELDS']) {
                        $this->arResult['ERRORS'][$code] = Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_ERROR_REQUIRED');
                    }
                    $result = false;
                }
            }
        }

        return $result;
    }

    public function confirmCodeAction(): array
    {
        $result = [];

        $params = $this->getSessionParameters();

        $limit = min(600, max(10, intval($params['RESEND_LIMIT'])));

        $phoneNumber = strval($this->request['phone']);
        $country = strval($this->request['country']);
        $code = trim(strval($this->request['code']));

        $number = static::getPhoneNumber($phoneNumber, $country);
        $timeout = static::getSendTimeout($phoneNumber, $country, $limit);

        $result['timeout'] = $timeout;

        if (!$number->isValid()) {
            $result['state'] = 'error';
            $result['message'] = Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_ERROR_NOT_VALID');
            return $result;
        }

        $_SESSION[self::SESS_KEY]['LAST_PHONE'] = [
            'NUMBER' => $phoneNumber,
            'COUNTRY' => $country,
        ];

        if (self::checkConfirmPhone($phoneNumber, $country, $code)) {
            $result['state'] = 'confirm';
            return $result;
        } elseif (isset($this->request['code']) > 0) {
            $result['state'] = 'error';
            $result['message'] = Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_ERROR_CODE_ERROR');
            return $result;
        }

        if ($timeout > 0) {
            $result['state'] = 'error';
            $result['message'] = Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_AUTH_ERROR_TIMEOUT', [
                '#PHONE#' => $phoneNumber,
                '#PHONE_FORMATTED#' => $number->format(Format::E164),
                '#TIME#' => $timeout,
            ]);
            return $result;
        }

        $generatedCode = static::generateConfirmCode(intval($params['CONFIRM_CODE_LENGTH']));
        $send = static::sendConfirmCode(
            $phoneNumber,
            $country,
            $generatedCode,
            strval($params['SMS_EVENT_CODE']),
            $params['DEV_MODE'] === 'Y'
        );

        if ($params['DEV_MODE'] === 'Y') {
            $result['code'] = $generatedCode;
        }

        if ($send->isSuccess()) {
            $result['state'] = 'code';
            $result['timeout'] = static::getSendTimeout($phoneNumber, $country, $limit);
            return $result;
        }

        $result['state'] = 'error';
        $result['message'] = implode('<br>', $send->getErrorMessages());
        return $result;
    }

    public static function getPhoneHash(string $phoneNumber, string $country = ''): string
    {
        $number = self::getPhoneNumber($phoneNumber, $country);
        return md5($number->format(Format::E164) . 'E164');
    }

    public static function getPhoneNumber(string $phoneNumber, string $country = ''): PhoneNumber
    {
        $cacheKey = md5($phoneNumber . $country . 'phone');

        if (!isset(self::$phonesCache[$cacheKey]) && !(self::$phonesCache[$cacheKey] instanceof PhoneNumber)) {
            self::$phonesCache[$cacheKey] = Parser::getInstance()->parse($phoneNumber, $country);
        }

        return self::$phonesCache[$cacheKey];
    }

    public static function getSendTimeout(string $phoneNumber, string $country, int $limit): int
    {
        $phoneHash = self::getPhoneHash($phoneNumber, $country);
        $lastTimestamp = intval($_SESSION[self::SESS_KEY]['PHONES'][$phoneHash]['LAST_TIME']);
        $currentTimestamp = (new \DateTime())->getTimestamp();
        $time = $limit - ($currentTimestamp - $lastTimestamp);

        if ($time > $limit || $time <= 0) {
            $lastGlobalTimestamp = intval($_SESSION[self::SESS_KEY_ALL]);
            $globalTime = 20 - ($currentTimestamp - $lastGlobalTimestamp);
            return ($globalTime > 20 || $globalTime <= 0) ? 0 : $globalTime;
        }

        return $time;
    }

    public static function checkConfirmPhone(string $phoneNumber, string $country = '', string $code = null): bool
    {
        $phoneHash = self::getPhoneHash($phoneNumber, $country);

        if ($_SESSION[self::SESS_KEY]['PHONES'][$phoneHash]['CONFIRM'] === 'Y') {
            return true;
        }

        $lastCode = strval($_SESSION[self::SESS_KEY]['PHONES'][$phoneHash]['LAST_CODE']);

        if (strlen($lastCode) > 0 && $lastCode === strval($code)) {
            $_SESSION[self::SESS_KEY]['PHONES'][$phoneHash]['CONFIRM'] = 'Y';
            return true;
        }

        return false;
    }

    public static function generateConfirmCode(int $length = 5): string
    {
        $length = min(10, max(4, $length));

        try {
            $rand = [];
            for ($i = 0; $i < $length; $i++) {
                $rand[] = random_int(0, 9);
            }
        } catch (\Throwable $e) {
            $rand = [];
            for ($i = 0; $i < $length; $i++) {
                $rand[rand(1, 999) * $i] = rand(0, 9);
            }
            ksort($rand);
        }

        return implode('', $rand);
    }

    public static function sendConfirmCode(string $phoneNumber, string $country, string $code, string $template = '', bool $devMode = false): Result
    {
        $number = self::getPhoneNumber($phoneNumber, $country);

        $sms = new Event($template, [
            'USER_PHONE' => $number->format(Format::E164),
            'CODE' => $code,
        ]);

        if (!$devMode) {
            $result = $sms->send(true);
        } else {
            $result = new Result();
        }

        if ($result->isSuccess()) {
            $phoneHash = self::getPhoneHash($phoneNumber, $country);
            $timestamp = (new \DateTime())->getTimestamp();
            $_SESSION[self::SESS_KEY_ALL] = $timestamp;
            $_SESSION[self::SESS_KEY]['PHONES'][$phoneHash]['LAST_TIME'] = $timestamp;
            $_SESSION[self::SESS_KEY]['PHONES'][$phoneHash]['LAST_CODE'] = $code;
        }

        return $result;
    }

    public static function onAfterUserLogoutActions(): void
    {
        unset(
            $_SESSION[self::SESS_KEY],
            $_SESSION[self::SESS_KEY_ALL],
        );
    }
}
