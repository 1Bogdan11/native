<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\PhoneNumber;

\CBitrixComponent::includeComponentClass('its.agency:phone.auth');

class PhoneCheckComponent extends \PhoneAuthComponent
{
    public function executeComponent(): void
    {
        if (!Loader::includeModule('messageservice')) {
            return;
        }

        if (!is_array($this->arParams['USER_PHONES'])) {
            $this->arParams['USER_PHONES'] = [];
        }

        $this->arParams['FIELD_VALUE'] = strval($this->arParams['FIELD_VALUE']);
        $this->arParams['FIELD_VALUE_COUNTRY'] = strval($this->arParams['FIELD_VALUE_COUNTRY']);

        $this->arResult['FIELD_VALUE'] = '';
        $this->arResult['FIELD_VALUE_COUNTRY'] = '';

        $number = static::getPhoneNumber(strval($this->arParams['FIELD_VALUE']));
        if ($number->isValid()) {
            $this->arResult['FIELD_VALUE'] = $number->format(Format::E164);
            $this->arResult['FIELD_VALUE_COUNTRY'] = $number->getCountry();
        }

        if (!empty($this->arParams['FIELD_VALUE_COUNTRY'])) {
            $this->arResult['FIELD_VALUE_COUNTRY'] = $this->arParams['FIELD_VALUE_COUNTRY'];
        }

        $this->setSessionParameters();

        $confirmedPhone = self::checkAcceptedPhones(
            $this->arParams['USER_PHONES'],
            $this->arParams['FIELD_VALUE'],
            $this->arParams['FIELD_VALUE_COUNTRY']
        );
        $confirmedPhone = $confirmedPhone || static::checkConfirmPhone(
            $this->arParams['FIELD_VALUE'],
            $this->arParams['FIELD_VALUE_COUNTRY']
        );
        $this->arResult['CONFIRM_PHONE'] = $confirmedPhone ? 'Y' : 'N';

        $phoneHash = static::getPhoneHash($this->arParams['FIELD_VALUE'], $this->arParams['FIELD_VALUE_COUNTRY']);
        $this->arResult['SEND_CODE'] = !empty($_SESSION[self::SESS_KEY]['PHONES'][$phoneHash]['LAST_CODE']) && !$confirmedPhone ? 'Y' : 'N';

        $this->includeComponentTemplate();
    }

    public static function checkConfirmPhoneForParentComponent(PhoneNumber $phoneNumber, string $country): bool
    {
        $params = static::getSessionParameters();

        $confirmedPhone = self::checkAcceptedPhones(
            $params['USER_PHONES'],
            $phoneNumber->format(Format::E164),
            $country
        );
        return $confirmedPhone || static::checkConfirmPhone(
            $phoneNumber->format(Format::E164),
            $country
        );
    }

    public static function checkAcceptedPhones(array $userNumbers, string $phoneNumber, string $country = ''): bool
    {
        $requestPhone = static::getPhoneNumber($phoneNumber, $country);
        foreach ($userNumbers as $userCountry => $userNumber) {
            if (empty($userNumber)) {
                continue;
            }
            $userPhone = static::getPhoneNumber(strval($userNumber), strval($userCountry));
            if ($requestPhone->format(Format::E164) === $userPhone->format(Format::E164)) {
                return true;
            }
        }

        return false;
    }

    protected function setSessionParameters(): void
    {
        $_SESSION[self::SESS_KEY]['PARAMS'] = [
            'USER_PHONES' => $this->arParams['USER_PHONES'],
            'CONFIRM_CODE_LENGTH' => $this->arParams['CONFIRM_CODE_LENGTH'],
            'RESEND_LIMIT' => $this->arParams['RESEND_LIMIT'],
            'SMS_EVENT_CODE' => $this->arParams['SMS_EVENT_CODE'],
            'DEV_MODE' => $this->arParams['DEV_MODE'],
        ];
    }

    public function confirmCodeAction(): array
    {
        $result = [];

        $params = $this->getSessionParameters();

        $limit = min(600, max(10, intval($params['RESEND_LIMIT'])));

        $phoneNumber = strval($this->request['phone']);
        $country = strval($this->request['country']);
        $code = trim(strval($this->request['code']));

        $number = static::getPhoneNumber($phoneNumber);
        $timeout = static::getSendTimeout($phoneNumber, $country, $limit);

        $result['timeout'] = $timeout;

        if (!$number->isValid()) {
            $result['state'] = 'error';
            $result['message'] = Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_CHECK_ERROR_NOT_VALID');
            return $result;
        }

        $_SESSION[static::SESS_KEY]['LAST_PHONE'] = [
            'NUMBER' => $phoneNumber,
            'COUNTRY' => $country,
        ];

        if (
            self::checkAcceptedPhones($params['USER_PHONES'], $phoneNumber, $country)
            || static::checkConfirmPhone($phoneNumber, $country, $code)
        ) {
            $result['state'] = 'confirm';
            return $result;
        } elseif (isset($this->request['code']) > 0) {
            $result['state'] = 'error';
            $result['message'] = Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_CHECK_ERROR_CODE_ERROR');
            return $result;
        }

        if ($timeout > 0) {
            $result['state'] = 'error';
            $result['message'] = Loc::getMessage('ITS_AGENCY_COMPONENT_PHONE_CHECK_ERROR_TIMEOUT', [
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
}
