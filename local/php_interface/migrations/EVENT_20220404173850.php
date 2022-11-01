<?php

namespace Sprint\Migration;


class EVENT_20220404173850 extends Version
{
    protected $description = "Кастомные SMS события заказа";

    protected $moduleVersion = "3.30.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->Event()->saveEventType('SMS_EVENT_SALE_NEW_ORDER', array (
  'LID' => 'ru',
  'EVENT_TYPE' => 'sms',
  'NAME' => 'Новый заказ',
  'DESCRIPTION' => '#USER_PHONE# - Телефон пользователя (из заказа)
#USER_NAME# - Имя пользователя (из заказа)
#ORDER_ID# - ID заказа
#ORDER_NUMBER# - Номер заказа',
  'SORT' => '150',
));
            $helper->Event()->saveEventType('SMS_EVENT_SALE_NEW_ORDER', array (
  'LID' => 'en',
  'EVENT_TYPE' => 'sms',
  'NAME' => 'Новый заказ',
  'DESCRIPTION' => '#USER_PHONE# - Телефон пользователя (из заказа)
#USER_NAME# - Имя пользователя (из заказа)
#ORDER_ID# - ID заказа
#ORDER_NUMBER# - Номер заказа',
  'SORT' => '150',
));
            $helper->Event()->saveEventType('SMS_EVENT_SALE_ORDER_PAY', array (
  'LID' => 'ru',
  'EVENT_TYPE' => 'sms',
  'NAME' => 'Заказ оплачен',
  'DESCRIPTION' => '#USER_PHONE# - Телефон пользователя (из заказа)
#USER_NAME# - Имя пользователя (из заказа)
#ORDER_ID# - ID заказа
#ORDER_NUMBER# - Номер заказа',
  'SORT' => '150',
));
            $helper->Event()->saveEventType('SMS_EVENT_SALE_ORDER_PAY', array (
  'LID' => 'en',
  'EVENT_TYPE' => 'sms',
  'NAME' => 'Заказ оплачен',
  'DESCRIPTION' => '#USER_PHONE# - Телефон пользователя (из заказа)
#USER_NAME# - Имя пользователя (из заказа)
#ORDER_ID# - ID заказа
#ORDER_NUMBER# - Номер заказа',
  'SORT' => '150',
));
            $helper->Event()->saveEventType('SMS_EVENT_SALE_STATUS_D', array (
  'LID' => 'ru',
  'EVENT_TYPE' => 'sms',
  'NAME' => 'Статус: Заказ передан в доставку (D)',
  'DESCRIPTION' => '#USER_PHONE# - Телефон пользователя (из заказа)
#USER_NAME# - Имя пользователя (из заказа)
#ORDER_ID# - ID заказа
#ORDER_NUMBER# - Номер заказа',
  'SORT' => '150',
));
            $helper->Event()->saveEventType('SMS_EVENT_SALE_STATUS_D', array (
  'LID' => 'en',
  'EVENT_TYPE' => 'sms',
  'NAME' => 'Статус: Заказ передан в доставку (D)',
  'DESCRIPTION' => '#USER_PHONE# - Телефон пользователя (из заказа)
#USER_NAME# - Имя пользователя (из заказа)
#ORDER_ID# - ID заказа
#ORDER_NUMBER# - Номер заказа',
  'SORT' => '150',
));
        }

    public function down()
    {
        //your code ...
    }
}
