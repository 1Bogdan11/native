<?php

namespace Its\Maxma\Ajax;

use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Controller;
use Its\Maxma\Order\Coupon as MaxmaCoupon;

class Coupon extends Controller
{
    public function configureActions()
    {
        return [
            'set' => [
                'prefilters' => [
                    new HttpMethod([HttpMethod::METHOD_POST]),
                    new Csrf(),
                ]
            ],
            'remove' => [
                'prefilters' => [
                    new HttpMethod([HttpMethod::METHOD_POST]),
                    new Csrf(),
                ]
            ],
        ];
    }

    public function setAction(string $coupon = '')
    {
        MaxmaCoupon::setToSession($coupon);
    }

    public function removeAction()
    {
        MaxmaCoupon::clear();
    }
}

