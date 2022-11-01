<?php

namespace Sprint\Migration;


class IBOffersAPICODE20220130221650 extends Version
{
    protected $description = "символьный код и API CODE для ИБ торговых предложениий";

    protected $moduleVersion = "3.30.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->saveIblock(array (
  'IBLOCK_TYPE_ID' => '1c_catalog',
  'LID' => 
  array (
    0 => 's1',
  ),
  'CODE' => 'offers',
  'API_CODE' => 'offers',
  'REST_ON' => 'N',
  'NAME' => 'Пакет предложений (Основной каталог товаров)',
  'ACTIVE' => 'Y',
  'SORT' => '500',
  'LIST_PAGE_URL' => '',
  'DETAIL_PAGE_URL' => '',
  'SECTION_PAGE_URL' => '',
  'CANONICAL_PAGE_URL' => '',
  'PICTURE' => NULL,
  'DESCRIPTION' => '',
  'DESCRIPTION_TYPE' => 'text',
  'RSS_TTL' => '24',
  'RSS_ACTIVE' => 'Y',
  'RSS_FILE_ACTIVE' => 'N',
  'RSS_FILE_LIMIT' => NULL,
  'RSS_FILE_DAYS' => NULL,
  'RSS_YANDEX_ACTIVE' => 'N',
  'XML_ID' => '65becde3-7aa4-43a5-a9f6-ddda69e1909e#',
  'INDEX_ELEMENT' => 'Y',
  'INDEX_SECTION' => 'N',
  'WORKFLOW' => 'N',
  'BIZPROC' => 'N',
  'SECTION_CHOOSER' => 'L',
  'LIST_MODE' => '',
  'RIGHTS_MODE' => 'S',
  'SECTION_PROPERTY' => 'Y',
  'PROPERTY_INDEX' => 'I',
  'VERSION' => '1',
  'LAST_CONV_ELEMENT' => '0',
  'SOCNET_GROUP_ID' => NULL,
  'EDIT_FILE_BEFORE' => '',
  'EDIT_FILE_AFTER' => '',
  'SECTIONS_NAME' => 'Группа',
  'SECTION_NAME' => 'Раздел',
  'ELEMENTS_NAME' => 'Товар',
  'ELEMENT_NAME' => 'Элемент',
  'EXTERNAL_ID' => '65becde3-7aa4-43a5-a9f6-ddda69e1909e#',
  'LANG_DIR' => '/',
  'SERVER_NAME' => 'lj.local',
  'IPROPERTY_TEMPLATES' => 
  array (
  ),
  'ELEMENT_ADD' => 'Добавить элемент',
  'ELEMENT_EDIT' => 'Изменить элемент',
  'ELEMENT_DELETE' => 'Удалить элемент',
  'SECTION_ADD' => 'Добавить раздел',
  'SECTION_EDIT' => 'Изменить раздел',
  'SECTION_DELETE' => 'Удалить раздел',
));

    }

    public function down()
    {
        //your code ...
    }
}
