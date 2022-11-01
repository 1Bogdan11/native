<?php

use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use ReCaptcha\ReCaptcha;

class FormSampleComponent extends \CBitrixComponent
{
    public function executeComponent()
    {
        global $APPLICATION;

        if (!Loader::includeModule('iblock')) {
            return;
        }

        $iblockId = intval($this->arParams['IBLOCK_ID']);
        $arFields = array_values($this->arParams['PROPERTIES']);
        $arRequire = array_values($this->arParams['PROPERTIES_REQUIRE']);

        if ($iblockId <= 0) {
            return;
        }

        $this->arResult['FIELDS'] = [];
        $arProperties = [];
        foreach ($arFields as $code) {
            $arField = [
                'PROPERTY' => 'N',
                'REQUIRE' => in_array($code, $arRequire) ? 'Y' : 'N',
                'FIELD_NAME' => mb_strtoupper($code),
                'VALUE' => htmlspecialchars(trim($this->request[mb_strtoupper($code)]))
            ];
            if (substr($code, 0, 9) === 'PROPERTY_') {
                $code = str_replace('PROPERTY_', '', $code);
                $arProperties[] = $code;
                $arField['PROPERTY'] = 'Y';
            }
            $this->arResult['FIELDS'][$code] = $arField;
        }

        $resProperty = PropertyTable::getList([
            'filter' => [
                'IBLOCK_ID' => $iblockId,
                'CODE' => $arProperties,
            ]
        ]);

        while ($arProperty = $resProperty->fetch()) {
            if ($arProperty['PROPERTY_TYPE'] === 'L') {
                $arProperty['VALUES'] = PropertyEnumerationTable::getList([
                    'filter' => ['=PROPERTY_ID' => $arProperty['ID']],
                    'order' => ['SORT' => 'ASC'],
                ])->fetchAll();
            }
            $this->arResult['FIELDS'][$arProperty['CODE']]['DETAIL'] = $arProperty;
        }

        if (empty($this->arParams['BUTTON_NAME'])) {
            $this->arParams['BUTTON_NAME'] = 'save_form';
        }

        if ($this->request[$this->arParams['BUTTON_NAME']] && check_bitrix_sessid()) {
            $this->save();

            if ($this->request['print_json']) {
                $APPLICATION->RestartBuffer();
                while (ob_get_level()) {
                    ob_end_clean();
                }

                ob_start();
                $this->includeComponentTemplate('json');
                $content = ob_get_clean();

                $response = Context::getCurrent()->getResponse();
                $response->getHeaders()->set('Content-Type', 'application/json');
                $response->setStatus('200 OK');
                $response->flush($content);
                die();
            }
        }

        $this->includeComponentTemplate();
    }

    private function save()
    {
        global $USER;

        $this->arResult['ERRORS'] = [];
        $arElementData = [
            'ACTIVE' => 'N',
        ];
        foreach ($this->arResult['FIELDS'] as $code => $arField) {
            if (is_uploaded_file($_FILES[($arField['PROPERTY'] === 'Y' ? 'PROPERTY_' : '') . $code]['tmp_name'])) {
                $arField['VALUE'] = $_FILES[($arField['PROPERTY'] === 'Y' ? 'PROPERTY_' : '') . $code];
            }

            if (empty($arField['VALUE']) && $arField['REQUIRE'] === 'Y') {
                $this->arResult['ERRORS'][] = [
                    'name' => $arField['FIELD_NAME'],
                    'message' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_CLASS_FIELD_ERROR'),
                ];
                continue;
            }

            if ($arField['PROPERTY'] === 'Y') {
                $arElementData['PROPERTY_VALUES'][$code] = $arField['VALUE'];
            } else {
                $arElementData[$code] = $arField['VALUE'];
            }
        }

        $arElementData['IBLOCK_ID'] = intval($this->arParams['IBLOCK_ID']);

        if ($this->arParams['USE_CAPTCHA'] === 'Y') {
            try {
                $recaptcha = new ReCaptcha(strval($this->arParams['CAPTCHA_PRIVATE']));
                $check = $recaptcha->verify($this->request['g-recaptcha-response'], $this->request->getRemoteAddress());

                if (!$check->isSuccess()) {
                    $this->arResult['ERRORS'][] = [
                        'name' => '',
                        'message' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_CLASS_RECAPTCHA_ERROR'),
                        'detail' => $USER->IsAdmin() ? $check->getErrorCodes() : '',
                    ];
                }
            } catch (\Throwable $e) {
                $this->arResult['ERRORS'][] = [
                    'name' => '',
                    'message' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_CLASS_RECAPTCHA_ERROR'),
                    'detail' => $USER->IsAdmin() ? $e->getMessage() : '',
                ];
            }
        }

        if (!empty($this->arResult['ERRORS'])) {
            return;
        }

        $element = new CIBlockElement();
        $add = $element->Add($arElementData);

        if (intval($add) <= 0) {
            $this->arResult['ERRORS'][] = [
                'name' => '',
                'message' => Loc::getMessage('ITS_AGENCY_COMPONENT_FORM_SAMPLE_CLASS_INTERNAL_ERROR'),
                'detail' => $USER->IsAdmin() ? $element->LAST_ERROR : '',
            ];
        } else {
            $this->arResult['SUCCESS'] = 'Y';

            if (!empty($this->arParams['MAIL_EVENT_NAME'])) {
                $eventFields = [
                    'FIELD_ID' => $add,
                ];

                foreach ($arElementData as $key => $value) {
                    if ($key === 'PROPERTY_VALUES') {
                        foreach ($value as $propertyKey => $propertyValue) {
                            $arProperty = $this->arResult['FIELDS'][$propertyKey]['DETAIL'];
                            if ($arProperty['PROPERTY_TYPE'] === PropertyTable::TYPE_ELEMENT) {
                                $arElement = ElementTable::getById(intval($propertyValue))->fetch();
                                if ($arElement) {
                                    $eventFields["FIELD_PROPERTY_{$propertyKey}"] = "[{$arElement['ID']}] {$arElement['NAME']}";
                                    $eventFields["FIELD_PROPERTY_{$propertyKey}_ID"] = intval($propertyValue);
                                    $eventFields["FIELD_PROPERTY_{$propertyKey}_NAME"] = $arElement['NAME'];
                                }
                            } elseif ($arProperty['PROPERTY_TYPE'] === PropertyTable::TYPE_LIST) {
                                $arProperty = $this->arResult['FIELDS'][$propertyKey]['DETAIL'];
                                $eventFields["FIELD_PROPERTY_{$propertyKey}"] = '';
                                $eventFields["FIELD_PROPERTY_{$propertyKey}_XML_ID"] = '';
                                foreach ($arProperty['VALUES'] as $arValue) {
                                    if ($arValue['ID'] == $propertyValue) {
                                        $eventFields["FIELD_PROPERTY_{$propertyKey}"] = $arValue['VALUE'];
                                        $eventFields["FIELD_PROPERTY_{$propertyKey}_XML_ID"] = $arValue['XML_ID'];
                                    }
                                }
                            } else {
                                $eventFields["FIELD_PROPERTY_{$propertyKey}"] = strval($propertyValue);
                            }
                        }
                    } else {
                        $eventFields["FIELD_$key"] = strval($value);
                    }
                }

                Event::send([
                    'EVENT_NAME' => htmlspecialchars($this->arParams['MAIL_EVENT_NAME']),
                    'LID' => $this->arParams['SITE_ID'] ?? SITE_ID,
                    'C_FIELDS' => $eventFields,
                ]);
            }
        }
    }
}
