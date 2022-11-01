<?php

namespace Its\Maxma\Order;

use Bitrix\Main\Localization\Loc;

class DiscountAction extends \CSaleActionCtrlAction
{
    public static function getActionDescription(): array
    {
        return [
            'ID' => static::getActionId(),
            'SORT' => 100,
            'GROUP' => 'Y',
            'Parse' => [__CLASS__, 'parse'],
            'IsGroup' => [__CLASS__, 'isGroup'],
            'Generate' => [__CLASS__, 'getAction'],
            'ApplyValues' => [__CLASS__, 'applyValues'],
            'GetControlShow' => [__CLASS__, 'getActionParams'],
            'GetConditionShow' => [__CLASS__, 'getActionData'],
        ];
    }

    public static function getActionId(): string
    {
        return 'MaxmaDiscountAction';
    }

    public static function getControlId(): string
    {
        return static::getActionId();
    }

    public static function getVisual(): array
    {
        return [];
    }

    public static function getActionData($params)
    {
        return parent::getConditionShow($params);
    }

    public static function getAtoms(): array
    {
        return static::getAtomsEx(false, false);
    }

    public static function getAtomsEx($controlId = false, $ex = false): array
    {
        $atoms = [
            'Where' => [
                'JS' => [
                    'id' => 'Where',
                    'name' => 'where',
                    'type' => 'select',
                    'values' => array(
                        'ORDER' => Loc::getMessage('ITS_MAXMA_DISCOUNT_ACTION_WHERE_ORDER'),
                    ),
                    'defaultValue' => 'ORDER',
                    'first_option' => '...'
                ],
                'ATOM' => [
                    'ID' => 'Where',
                    'FIELD_TYPE' => 'string',
                    'FIELD_LENGTH' => 255,
                    'MULTIPLE' => 'N',
                    'VALIDATE' => 'list'
                ],
            ],
        ];

        if ($ex !== true) {
            foreach ($atoms as &$atom) {
                $atom = $atom['JS'];
            }
        }

        return $atoms;
    }

    public static function getActionParams(array $params): array
    {
        $atoms = static::getAtomsEx();
        return [
            'controlId' => static::getActionId(),
            'group' => false,
            'label' => Loc::getMessage('ITS_MAXMA_DISCOUNT_ACTION_NAME'),
            'showIn' => [\CSaleActionCtrlGroup::getControlId()],
            'visual' => static::getVisual(),
            'control' => [
                Loc::getMessage('ITS_MAXMA_DISCOUNT_ACTION_DESCRIPTION'),
                $atoms['Where'],
            ]
        ];
    }

    public static function getAction($condition, $params, $control, $child = [])
    {
        return [
            'COND' => "if (\Bitrix\Main\loader::includeModule('its.maxma')) {(new \Its\Maxma\Order\Discount({$params['ORDER']}))->apply();}",
        ];
    }

}
