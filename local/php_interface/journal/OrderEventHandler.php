<?php

namespace Journal;

use Bitrix\Main\Event;
use Bitrix\Sale\Order;

class OrderEventHandler
{
    public static function orderSaved(Event $event)
    {
        /** @var Order $order */
        $order = $event->getParameter('ENTITY');
        $propertyCollection = $order->getPropertyCollection();
        $city = $propertyCollection->getDeliveryLocation();
        $address = $propertyCollection->getAddress();

        \CBitrixComponent::includeComponentClass('journal:user.address');

        $userFields = \UserAddressComponent::getUserFields();

        $updateUserFields = [];
        if (empty($userFields['UF_CITY'])) {
            $updateUserFields['UF_CITY'] = $city ? $city->getValue() : '';
        }
        if (empty($userFields['UF_ADDRESS'])) {
            $updateUserFields['UF_ADDRESS'] = $address ? $address->getValue() : '';
        }

        \UserAddressComponent::saveFields($updateUserFields);
    }
}
