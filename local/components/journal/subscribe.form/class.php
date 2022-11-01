<?php

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Journal\SubscribeManager;


class SubscribeFormComponent extends \CBitrixComponent
{
    protected string $email = '';
    protected SubscribeManager $subscribe;

    public function __construct($component = null)
    {
        $this->subscribe = new SubscribeManager();
        parent::__construct($component);
    }

    public function executeComponent()
    {
        $this->arResult['ERRORS'] = [];
        $this->arResult['EMAIL'] = htmlspecialchars($this->arParams['EMAIL'] ?? $this->request['EMAIL']);

        if ($this->request[$this->arParams['FORM_NAME'] ?? 'subscribe_form'] && check_bitrix_sessid()) {
            $this->toggleSubscribe($this->request['SUBSCRIBE'] !== 'N');

            if ($this->request['print_json']) {
                while (ob_get_level()) {
                    ob_end_clean();
                }
                $response = Context::getCurrent()->getResponse();
                $response->getHeaders()->set('Content-Type', 'application/json');
                $response->setStatus('200 OK');
                $response->flush(json_encode([
                    'status' => $this->arResult['SUCCESS'] === 'Y',
                    'action' => $this->arResult['ACTION'],
                    'message' => implode('<br>', $this->arResult['ERRORS']),
                ]));
                die();
            }
        }

        $this->arResult['SUBSCRIBED'] = $this->subscribe->check($this->arResult['EMAIL']) ? 'Y' : 'N';

        $this->includeComponentTemplate();
    }

    protected function toggleSubscribe(bool $subscribe = true)
    {
        global $USER;

        if ($subscribe) {
            $result = $this->subscribe->add($this->arResult['EMAIL']);
        } else {
            $result = $this->subscribe->remove($this->arResult['EMAIL']);
        }

        if (!$result->isSuccess()) {
            if (!$USER->IsAdmin()) {
                $this->arResult['ERRORS'][] = Loc::getMessage('ITS_AGENCY_COMPONENT_SUBSCRIBE_FORM_CLASS_INTERNAL_ERROR');
            } else {
                $this->arResult['ERRORS'] = array_merge($this->arResult['ERRORS'], $result->getErrorMessages());
            }
        }

        $this->arResult['ACTION'] = $subscribe ? 'SUBSCRIBE' : 'UNSUBSCRIBE';
        $this->arResult['SUCCESS'] = $result->isSuccess() ? 'Y' : 'N';
    }
}
