<?

namespace Journal;

use Bitrix\Main\Loader;

/**
 * @deprecated
 */
class GlobalStorage
{
    private static $storage = [
        'SKU_PROP_COLOR' => 'TSVET',
        'SKU_PROP_SIZE' => 'RAZMER',
    ];

    public static function get($name)
    {
        return self::$storage[$name];
    }
}
