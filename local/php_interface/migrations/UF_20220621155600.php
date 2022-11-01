<?php

namespace Sprint\Migration;


class UF_20220621155600 extends Version
{
    protected $description = "Св-во скрытия шильдика для раздела ИБ каталог";

    protected $moduleVersion = "3.30.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
  'ENTITY_ID' => 'IBLOCK_1c_catalog:catalog_SECTION',
  'FIELD_NAME' => 'UF_HIDE_LABEL',
  'USER_TYPE_ID' => 'enumeration',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'DISPLAY' => 'LIST',
    'LIST_HEIGHT' => 1,
    'CAPTION_NO_VALUE' => '',
    'SHOW_NO_VALUE' => 'Y',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Скрыть шильдик',
    'ru' => 'Скрыть шильдик',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'ENUM_VALUES' => 
  array (
    0 => 
    array (
      'VALUE' => 'New',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => 'new',
    ),
    1 => 
    array (
      'VALUE' => 'Sale',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => 'sale',
    ),
  ),
));
    }

    public function down()
    {
        //your code ...
    }
}
