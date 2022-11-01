<?php

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Bitrix\Main\Context;

class UserAddressComponent extends \CBitrixComponent
{
    protected const ALLOW_FIELDS = [
        'UF_CITY',
        'UF_ADDRESS',
    ];

    public function executeComponent()
    {
        global $USER;

        if (!$USER->IsAuthorized() || !Loader::includeModule('sale')) {
            return;
        }

        $this->arResult['USER'] = static::getUserFields();

        if ($this->request->get('save_address') && check_bitrix_sessid()) {
            $data = [];
            foreach (static::ALLOW_FIELDS as $key) {
                $data[$key] = htmlspecialchars(strval($this->request->get($key)));
            }

            if (empty($data)) {
                $this->arResult['SAVED'] = 'Y';
            } else {
                $isSave = static::saveFields($data);
                $this->arResult['SAVED'] = $isSave ? 'Y' : 'N';
                $this->arResult['ERROR'] = !$isSave ? 'Y' : 'N';
            }

            if ($this->request->get('return_json')) {
                while (ob_get_level()) {
                    ob_end_clean();
                }
                $response = Context::getCurrent()->getResponse();
                $response->getHeaders()->set('Content-Type', 'application/json');
                $response->setStatus('200 OK');
                $response->flush(json_encode([
                    'success' => $this->arResult['SAVED'] === 'Y'
                ]));
                die();
            }
        }

        $this->includeComponentTemplate();
    }

    public static function getUserFields(): array
    {
        global $USER;

        if (!$USER->IsAuthorized()) {
            return [];
        }

        $data = UserTable::getList([
            'filter' => ['=ID' => $USER->GetID()],
            'select' => ['*', ...static::ALLOW_FIELDS],
        ])->fetch();

        return is_array($data) ? $data : [];
    }

    public static function saveFields(array $data): bool
    {
        global $USER;

        if (!$USER->IsAuthorized()) {
            return false;
        }

        $data = array_intersect_key($data, array_flip(static::ALLOW_FIELDS));
        if (empty($data)) {
            return false;
        }

        $user = new \CUser();
        return $user->Update($USER->GetID(), $data) === true;
    }
}
