<?php

namespace Sprint\Migration;


class IB_PROP_20220316170417 extends Version
{
    protected $description = "Свойство Таблица размеров ИБ Каталог";

    protected $moduleVersion = "3.30.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('catalog', '1c_catalog');
        $helper->Iblock()->saveProperty($iblockId, array (
  'NAME' => 'Таблица размеров',
  'ACTIVE' => 'Y',
  'SORT' => '20',
  'CODE' => 'SIZE_TABLE',
  'DEFAULT_VALUE' => '',
  'PROPERTY_TYPE' => 'E',
  'ROW_COUNT' => '1',
  'COL_COUNT' => '30',
  'LIST_TYPE' => 'L',
  'MULTIPLE' => 'N',
  'XML_ID' => '',
  'FILE_TYPE' => '',
  'MULTIPLE_CNT' => '5',
  'LINK_IBLOCK_ID' => '1c_catalog:size_table',
  'WITH_DESCRIPTION' => 'N',
  'SEARCHABLE' => 'N',
  'FILTRABLE' => 'N',
  'IS_REQUIRED' => 'N',
  'VERSION' => '1',
  'USER_TYPE' => NULL,
  'USER_TYPE_SETTINGS' => NULL,
  'HINT' => '',
  'FEATURES' => 
  array (
    0 => 
    array (
      'MODULE_ID' => 'catalog',
      'FEATURE_ID' => 'IN_BASKET',
      'IS_ENABLED' => 'N',
    ),
    1 => 
    array (
      'MODULE_ID' => 'iblock',
      'FEATURE_ID' => 'DETAIL_PAGE_SHOW',
      'IS_ENABLED' => 'N',
    ),
    2 => 
    array (
      'MODULE_ID' => 'iblock',
      'FEATURE_ID' => 'LIST_PAGE_SHOW',
      'IS_ENABLED' => 'N',
    ),
  ),
));
    
    }

    public function down()
    {
        //your code ...
    }
}
