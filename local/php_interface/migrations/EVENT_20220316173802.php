<?php

namespace Sprint\Migration;


class EVENT_20220316173802 extends Version
{
    protected $description = "Событие Новое сообщение из формы обратной связи";

    protected $moduleVersion = "3.30.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->Event()->saveEventType('CONTACTS_FORM_SEND_EVENT', array (
  'LID' => 'ru',
  'EVENT_TYPE' => 'email',
  'NAME' => 'Новое сообщение с формы обратной связи',
  'DESCRIPTION' => '#FIELD_IBLOCK_ID# - ИБ Формы
#FIELD_ID# - ИД заявки
#FIELD_NAME# - Имя
#FIELD_PROPERTY_EMAIL# - Email
#FIELD_PREVIEW_TEXT# - Сообщение',
  'SORT' => '150',
));
            $helper->Event()->saveEventType('CONTACTS_FORM_SEND_EVENT', array (
  'LID' => 'en',
  'EVENT_TYPE' => 'email',
  'NAME' => 'Новое сообщение с формы обратной связи',
  'DESCRIPTION' => '#FIELD_IBLOCK_ID# - ИБ Формы
#FIELD_ID# - ИД заявки
#FIELD_NAME# - Имя
#FIELD_PROPERTY_EMAIL# - Email
#FIELD_PREVIEW_TEXT# - Сообщение',
  'SORT' => '150',
));
            $helper->Event()->saveEventMessage('CONTACTS_FORM_SEND_EVENT', array (
  'LID' => 
  array (
    0 => 's1',
    1 => 's2',
  ),
  'ACTIVE' => 'Y',
  'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
  'EMAIL_TO' => '#DEFAULT_EMAIL_FROM#',
  'SUBJECT' => 'Новое сообщение с формы обратной связи',
  'MESSAGE' => '<b>Имя:</b> #FIELD_NAME#<br>
<b>Email:</b> #FIELD_PROPERTY_EMAIL#<br>
<b>Сообщение:</b> #FIELD_PREVIEW_TEXT#<br>
<b>Ссылка на обращение:</b> <a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=#FIELD_IBLOCK_ID#&type=service&lang=ru&ID=#FIELD_ID#">перейти</a><br>',
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
  'EVENT_TYPE' => '[ CONTACTS_FORM_SEND_EVENT ] Новое сообщение с формы обратной связи',
));
        }

    public function down()
    {
        //your code ...
    }
}
