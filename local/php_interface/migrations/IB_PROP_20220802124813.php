<?php

namespace Sprint\Migration;


class IB_PROP_20220802124813 extends Version
{
    protected $description = "Настройки формы редактирования для ИБ Отзывов";

    protected $moduleVersion = "4.0.2";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('reviews', 'service');
        $helper->UserOptions()->saveElementForm($iblockId, array (
  'Элемент|edit1' => 
  array (
    'ACTIVE' => 'Активность',
    'NAME' => 'Имя',
    'PROPERTY_EMAIL' => 'Email',
    'PREVIEW_TEXT' => 'Отзыв',
    'PROPERTY_ITEM' => 'Товар',
  ),
  'Сервисные|cedit1' => 
  array (
    'ID' => 'ID',
    'DATE_CREATE' => 'Создан',
    'TIMESTAMP_X' => 'Изменен',
    'XML_ID' => 'Внешний код',
    'PROPERTY_RATING' => 'Рейтинг',
  ),
));
    $helper->UserOptions()->saveElementGrid($iblockId, array (
  'views' => 
  array (
    'default' => 
    array (
      'columns' => 
      array (
        0 => '',
      ),
      'columns_sizes' => 
      array (
        'expand' => 1,
        'columns' => 
        array (
        ),
      ),
      'sticked_columns' => 
      array (
      ),
    ),
  ),
  'filters' => 
  array (
  ),
  'current_view' => 'default',
));
    $helper->UserOptions()->saveSectionGrid($iblockId, array (
  'views' => 
  array (
    'default' => 
    array (
      'columns' => 
      array (
        0 => '',
      ),
      'columns_sizes' => 
      array (
        'expand' => 1,
        'columns' => 
        array (
        ),
      ),
      'sticked_columns' => 
      array (
      ),
    ),
  ),
  'filters' => 
  array (
  ),
  'current_view' => 'default',
));

    }

    public function down()
    {
        //your code ...
    }
}
