<?

namespace Journal;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Main\Loader;

/**
 * @deprecated
 */
class Utils
{
    public static $propertyEnumValues = [];

    public static function getEnumValues(int $iblockId, string $propertyCode): ?array
    {
        if (!array_key_exists($propertyCode, self::$propertyEnumValues[$iblockId])) {
            self::$propertyEnumValues[$iblockId][$propertyCode] = self::loadEnumValues($iblockId, $propertyCode);
        }

        return self::$propertyEnumValues[$iblockId][$propertyCode];
    }

    public static function loadEnumValues(int $iblockId, string $propertyCode): ?array
    {
        Loader::includeModule('iblock');

        $result = PropertyEnumerationTable::query()->setSelect(
            ['ID', 'XML_ID', 'VALUE', 'PROP_ID' => 'PROPERTY.ID',]
        )->where('PROPERTY.IBLOCK_ID', $iblockId)->where('PROPERTY.CODE', $propertyCode)->fetchAll();

        return $result ?: null;
    }

    /**
     * Убирает из номера лишние символы и теги, которые могут мешать в href-конструкции "tel:"
     * @param $phone
     * @return string
     */
    static function preparePhoneNumber($phone)
    {
        return str_replace([' ', '(', ')', '-'], '', strip_tags($phone));
    }

    /**
     * возвращает кол-во элементов в каталоге
     *
     */
    static function getProductsCount()
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $count = 0;

        $dbItems = \Bitrix\Iblock\ElementTable::getList(
            [
                'order' => array('SORT' => 'ASC'),
                'select' => array('ID', 'NAME', 'IBLOCK_ID', 'SORT', 'ACTIVE'),
                'filter' => array('IBLOCK_ID' => 4),
                'count_total' => 1,
                // дает возможность получить кол-во элементов через метод getCount()
                'cache' => array( // Кеш запроса, почему-то в офф. документации об этом умалчивают
                    'ttl' => 3600,
                    'cache_joins' => true
                ),
            ]
        );
        $count = $dbItems->getCount();
        return $count;
    }

}
