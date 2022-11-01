<?php

namespace Journal\Collection;

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Discount\Actions;

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
        return 'CollectionDiscountAction';
    }

    /**
     * @inernal
     * @return string
     */
    public static function getControlId(): string
    {
        return static::getActionId();
    }

    public static function getVisual(): array
    {
        return [
            'controls' => [
                'All',
                'True',
            ],
            'values' => [
                [
                    'All' => 'AND',
                    'True' => 'True',
                ],
                [
                    'All' => 'AND',
                    'True' => 'False',
                ],
                [
                    'All' => 'OR',
                    'True' => 'True',
                ],
                [
                    'All' => 'OR',
                    'True' => 'False',
                ],
            ],
            'logic' => [
                [
                    'style' => 'condition-logic-and',
                    'message' => Loc::getMessage('DISCOUNT_ACTION_LOGIC_AND'),
                ],
                [
                    'style' => 'condition-logic-and',
                    'message' => Loc::getMessage('DISCOUNT_ACTION_LOGIC_NOT_AND'),
                ],
                [
                    'style' => 'condition-logic-or',
                    'message' => Loc::getMessage('DISCOUNT_ACTION_LOGIC_OR'),
                ],
                [
                    'style' => 'condition-logic-or',
                    'message' => Loc::getMessage('DISCOUNT_ACTION_LOGIC_NOT_OR'),
                ],
            ],
        ];
    }

    public static function getActionData($params)
    {
        if (!isset($params['DATA']['True'])) {
            $params['DATA']['True'] = 'True';
        }
        return parent::getConditionShow($params);
    }

    public static function getAtoms(): array
    {
        return static::getAtomsEx(false, false);
    }

    public static function getAtomsEx($controlId = false, $ex = false): array
    {
        $atoms = [
            'Value' => array(
                'JS' => array(
                    'id' => 'Value',
                    'name' => 'extra_size',
                    'type' => 'input'
                ),
                'ATOM' => array(
                    'ID' => 'Value',
                    'FIELD_TYPE' => 'double',
                    'MULTIPLE' => 'N',
                    'VALIDATE' => ''
                )
            ),
            'All' => array(
                'JS' => array(
                    'id' => 'All',
                    'name' => 'aggregator',
                    'type' => 'select',
                    'values' => array(
                        'AND' => Loc::getMessage('DISCOUNT_ACTION_LOGIC_VISUAL_ALL'),
                        'OR' => Loc::getMessage('DISCOUNT_ACTION_LOGIC_VISUAL_ANY')
                    ),
                    'defaultValue' => 'AND',
                    'first_option' => '...'
                ),
                'ATOM' => array(
                    'ID' => 'All',
                    'FIELD_TYPE' => 'string',
                    'FIELD_LENGTH' => 255,
                    'MULTIPLE' => 'N',
                    'VALIDATE' => 'list'
                )
            ),
            'True' => array(
                'JS' => array(
                    'id' => 'True',
                    'name' => 'value',
                    'type' => 'select',
                    'values' => array(
                        'True' => Loc::getMessage('DISCOUNT_ACTION_LOGIC_VISUAL_TRUE'),
                        'False' => Loc::getMessage('DISCOUNT_ACTION_LOGIC_VISUAL_FALSE')
                    ),
                    'defaultValue' => 'True',
                    'first_option' => '...'
                ),
                'ATOM' => array(
                    'ID' => 'True',
                    'FIELD_TYPE' => 'string',
                    'FIELD_LENGTH' => 255,
                    'MULTIPLE' => 'N',
                    'VALIDATE' => 'list'
                )
            )
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
            'group' => true,
            'label' => Loc::getMessage('DISCOUNT_ACTION_NAME'),
            'showIn' => [\CSaleActionCtrlGroup::getControlId()],
            'visual' => static::getVisual(),
            'control' => [
                Loc::getMessage('DISCOUNT_ACTION_GET_DISCOUNT'),
                $atoms['Value'],
                Loc::getMessage('DISCOUNT_ACTION_PERCENT'),
                Loc::getMessage('DISCOUNT_ACTION_DESTINATION'),
                $atoms['All'],
                $atoms['True'],
            ]
        ];
    }

    public static function getAction($condition, $params, $control, $child = [])
    {
        if (!is_array($child)) {
            return false;
        }

        foreach (static::getAtomsEx() as $atom) {
            if (!isset($condition[$atom['id']])) {
                return false;
            }
        }

        $condition['Value'] = max(min(floatval($condition['Value']), 100), 0);
        $discountParams = [
            'VALUE' => -$condition['Value'],
            'UNIT' => Actions::VALUE_TYPE_PERCENT,
            'LIMIT_VALUE' => 0,
        ];

        if (!empty($child)) {
            $filter = "\$saleact{$params['FUNC_ID']}";
            if ($condition['All'] == 'AND') {
                $invert = '';
                $logic = ' && ';
                $prefix = $condition['True'] == 'True' ? '' : '!';
            } else {
                $prefix = '';
                if ($condition['True'] == 'True') {
                    $invert = '';
                    $logic = ' || ';
                } else {
                    $invert = '!';
                    $logic = ' && ';
                }
            }

            $command = $prefix . implode($logic . $prefix, $child);
            if ($prefix != '')
                $command = $invert . '(' . $command . ')';

            $code = "{$filter} = function(\$row) use ({$params['ORDER']}, {$params['BASKET']}) { return ({$command}); };";
            $code .= '\Bitrix\Sale\Discount\Actions::applyToBasket(' . $params['ORDER'] . ', ' . var_export($discountParams, true) . ', ' . $filter . ');';
            unset($filter);
        } else {
            $code = '\Bitrix\Sale\Discount\Actions::applyToBasket(' . $params['ORDER'] . ', ' . var_export($discountParams, true) . ', "");';
        }

        return [
            'COND' => $code,
        ];
    }

}
