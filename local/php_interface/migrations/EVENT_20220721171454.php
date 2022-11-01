<?php

namespace Sprint\Migration;


class EVENT_20220721171454 extends Version
{
    protected $description = "Событие заявки в 1 клик";

    protected $moduleVersion = "4.0.2";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->Event()->saveEventType('ONE_CLICK_FORM_SEND_EVENT', array (
  'LID' => 'ru',
  'EVENT_TYPE' => 'email',
  'NAME' => 'Новая заявка в 1 клик',
  'DESCRIPTION' => '#FIELD_ID# - ИД записи
#FIELD_IBLOCK_ID# - ИД инфоблока
#FIELD_NAME# - Имя
#FIELD_PROPERTY_PHONE# - Телефон
#FIELD_PROPERTY_ITEM# - Товар ([ИД] Название)
#FIELD_PROPERTY_ITEM_ID# - ИД товара
#FIELD_PROPERTY_ITEM_NAME# - Название товара',
  'SORT' => '150',
));
            $helper->Event()->saveEventType('ONE_CLICK_FORM_SEND_EVENT', array (
  'LID' => 'en',
  'EVENT_TYPE' => 'email',
  'NAME' => 'Новая заявка в 1 клик',
  'DESCRIPTION' => '#FIELD_ID# - ИД записи
#FIELD_IBLOCK_ID# - ИД инфоблока
#FIELD_NAME# - Имя
#FIELD_PROPERTY_PHONE# - Телефон
#FIELD_PROPERTY_ITEM# - Товар ([ИД] Название)
#FIELD_PROPERTY_ITEM_ID# - ИД товара
#FIELD_PROPERTY_ITEM_NAME# - Название товара',
  'SORT' => '150',
));
            $helper->Event()->saveEventMessage('ONE_CLICK_FORM_SEND_EVENT', array (
  'LID' => 
  array (
    0 => 's1',
    1 => 's2',
  ),
  'ACTIVE' => 'Y',
  'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
  'EMAIL_TO' => '#DEFAULT_EMAIL_FROM#',
  'SUBJECT' => 'Новая заявка в 1 клик',
  'MESSAGE' => '<p><b>Имя:</b> #FIELD_NAME#</p>
<p><b>Телефон:</b> #FIELD_PROPERTY_PHONE#</p>
<p><b>Товар:</b> #FIELD_PROPERTY_ITEM#</p>
<p><b>Посмотреть товар:</b> <a href="/bitrix/admin/iblock_element_edit.php?type=1c_catalog&IBLOCK_ID=1&lang=ru&ID=#FIELD_PROPERTY_ITEM_ID#">перейти</a></p>
<p><b>Посмотреть заявку:</b> <a href="/bitrix/admin/iblock_element_edit.php?type=service&IBLOCK_ID=#FIELD_IBLOCK_ID#&lang=ru&ID=#FIELD_ID#">перейти</a></p>',
  'BODY_TYPE' => 'html',
  'BCC' => '',
  'REPLY_TO' => '',
  'CC' => '',
  'IN_REPLY_TO' => '',
  'PRIORITY' => '',
  'FIELD1_NAME' => '',
  'FIELD1_VALUE' => '',
  'FIELD2_NAME' => '',
  'FIELD2_VALUE' => '',
  'SITE_TEMPLATE_ID' => '',
  'ADDITIONAL_FIELD' => 
  array (
  ),
  'LANGUAGE_ID' => 'ru',
  'EVENT_TYPE' => '[ ONE_CLICK_FORM_SEND_EVENT ] Новая заявка в 1 клик',
));
        }

    public function down()
    {
        //your code ...
    }
}
