<?php

namespace Sprint\Migration;


class EVENT_20220729135609 extends Version
{
    protected $description = "Почтовое событие нового отзыва";

    protected $moduleVersion = "4.0.2";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->Event()->saveEventType('REVIEW_FORM_SEND_EVENT', array (
  'LID' => 'ru',
  'EVENT_TYPE' => 'email',
  'NAME' => 'Новый отзыв',
  'DESCRIPTION' => '#FIELD_ID# - ИД отзыва
#FIELD_IBLOCK_ID# - ИД инфоблока
#FIELD_PREVIEW_TEXT# - Текст отзыва
#FIELD_NAME# - Имя
#FIELD_PROPERTY_EMAIL# - Email 
#FIELD_PROPERTY_ITEM# - Товар
#FIELD_PROPERTY_ITEM_ID# - ИД товара
#FIELD_PROPERTY_ITEM_NAME# - Название товара
#FIELD_PROPERTY_RATING# - Рейтинг',
  'SORT' => '150',
));
            $helper->Event()->saveEventType('REVIEW_FORM_SEND_EVENT', array (
  'LID' => 'en',
  'EVENT_TYPE' => 'email',
  'NAME' => 'Новый отзыв',
  'DESCRIPTION' => '#FIELD_ID# - ИД отзыва
#FIELD_IBLOCK_ID# - ИД инфоблока
#FIELD_PREVIEW_TEXT# - Текст отзыва
#FIELD_NAME# - Имя
#FIELD_PROPERTY_EMAIL# - Email 
#FIELD_PROPERTY_ITEM# - Товар
#FIELD_PROPERTY_ITEM_ID# - ИД товара
#FIELD_PROPERTY_ITEM_NAME# - Название товара
#FIELD_PROPERTY_RATING# - Рейтинг',
  'SORT' => '150',
));
            $helper->Event()->saveEventMessage('REVIEW_FORM_SEND_EVENT', array (
  'LID' => 
  array (
    0 => 's1',
    1 => 's2',
  ),
  'ACTIVE' => 'Y',
  'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
  'EMAIL_TO' => '#DEFAULT_EMAIL_FROM#',
  'SUBJECT' => 'Новый отзыв (требуется модерация)',
  'MESSAGE' => '<p><b>Имя:</b> #FIELD_NAME#</p>
<p><b>Email:</b> #FIELD_PROPERTY_EMAIL#</p>
<p><b>Текст отзыва:</b> #FIELD_PREVIEW_TEXT#</p>
<p><b>Товар:</b> #FIELD_PROPERTY_ITEM#</p>
<p><b>Посмотреть товар:</b> <a href="/bitrix/admin/iblock_element_edit.php?type=1c_catalog&IBLOCK_ID=1&lang=ru&ID=#FIELD_PROPERTY_ITEM_ID#">перейти</a></p>
<p><b>Посмотреть отзыв:</b> <a href="/bitrix/admin/iblock_element_edit.php?type=service&IBLOCK_ID=#FIELD_IBLOCK_ID#&lang=ru&ID=#FIELD_ID#">перейти</a></p>',
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
  'EVENT_TYPE' => '[ REVIEW_FORM_SEND_EVENT ] Новый отзыв',
));
        }

    public function down()
    {
        //your code ...
    }
}
