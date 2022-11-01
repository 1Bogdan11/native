<?php

use Bitrix\Iblock\Component\Tools;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

class PersonalSectionComponent extends \CBitrixComponent
{
    public function __construct($component = null)
    {
        Loader::includeModule('iblock');
        Loc::loadMessages(__FILE__);
        parent::__construct($component);
    }

    public function executeComponent()
    {
        $this->arResult['TEMPLATE_NAME'] = $this->detect();

        if ($this->arParams['ONLY_MENU'] === 'Y') {
            $this->includeComponentTemplate('menu');
            return;
        }

        if ($this->initComponentTemplate($this->arResult['TEMPLATE_NAME'])) {
            $this->includeComponentTemplate($this->arResult['TEMPLATE_NAME']);
        } else {
            Tools::process404('', true, true, true);
        }
    }

    private function detect(): string
    {
        if ($this->arParams['SEF_MODE'] === 'Y') {
            $url = parse_url(Context::getCurrent()->getServer()->getRequestUri(), PHP_URL_PATH);
            $path = trim(str_replace($this->arParams['SEF_FOLDER'], null, $url), ' /');

            if (strlen($path) === 0) {
                $template = '';
                $this->arResult['ELEMENT_ID'] = 0;
                $this->arResult['ELEMENT_CODE'] = false;
            } elseif (strpos($path, '/') !== false) {
                $explode = explode('/', $path);
                $this->arResult['ELEMENT_CODE'] = trim(array_pop($explode));
                $this->arResult['ELEMENT_ID'] = intval($this->arResult['ELEMENT_CODE']);
                $template = strtolower(implode('-', $explode));

                if (strlen($this->arResult['ELEMENT_CODE']) === 0) {
                    $this->arResult['ELEMENT_CODE'] = false;
                }
            } else {
                $template = strtolower($path);
                $this->arResult['ELEMENT_ID'] = 0;
                $this->arResult['ELEMENT_CODE'] = false;
            }
        } else {
            $requestTemplate = htmlspecialchars(trim(strval($this->request['section'])));
            $template = strlen($requestTemplate) > 0 ? $requestTemplate : '';
            $this->arResult['ELEMENT_ID'] = intval($this->request['id']);
            $this->arResult['ELEMENT_CODE'] = false;
        }

        return $template;
    }
}

