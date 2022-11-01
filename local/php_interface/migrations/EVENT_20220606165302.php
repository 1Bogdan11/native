<?php

namespace Sprint\Migration;


class EVENT_20220606165302 extends Version
{
    protected $description = "Событие формы обратной связи EN";

    protected $moduleVersion = "3.30.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->Event()->saveEventType('EN_FORM_SEND_EVENT', array (
  'LID' => 'ru',
  'EVENT_TYPE' => 'email',
  'NAME' => 'Форма обратной связи EN заглушки',
  'DESCRIPTION' => '#FIELD_ID# - ИД записи
#FIELD_NAME# - Email
#FIELD_PREVIEW_TEXT# - Комментарий 
#FIELD_IBLOCK_ID# - ИД инфоблока',
  'SORT' => '150',
));
            $helper->Event()->saveEventType('EN_FORM_SEND_EVENT', array (
  'LID' => 'en',
  'EVENT_TYPE' => 'email',
  'NAME' => 'Форма обратной связи EN заглушки',
  'DESCRIPTION' => '#FIELD_ID# - ИД записи
#FIELD_NAME# - Email
#FIELD_PREVIEW_TEXT# - Комментарий 
#FIELD_IBLOCK_ID# - ИД инфоблока',
  'SORT' => '150',
));
            $helper->Event()->saveEventMessage('EN_FORM_SEND_EVENT', array (
  'LID' => 
  array (
    0 => 's1',
    1 => 's2',
  ),
  'ACTIVE' => 'Y',
  'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
  'EMAIL_TO' => '#DEFAULT_EMAIL_FROM#',
  'SUBJECT' => 'Новое сообщение с формы обратной связи EN',
  'MESSAGE' => '<b>Email:</b> #FIELD_NAME#<br>
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
  'EVENT_TYPE' => '[ EN_FORM_SEND_EVENT ] Форма обратной связи EN заглушки',
));
        }

    public function down()
    {
        //your code ...
    }
}
