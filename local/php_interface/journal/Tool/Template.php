<?php

namespace Journal\Tool;

use Bitrix\Highloadblock\HighloadBlockTable;
use Its\Library\Image\Resize;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Template
{
    private static $colors = [];

    public static function selectPictures(array $item, bool $detail = false, array $offer = []): array
    {
        $pictures = count($offer) ? static::selectPictures($offer, $detail) : [];
        $order = !$detail ? ['preview','more','detail'] : ['detail','more'];
        $isUseDetailPicture = true;

        foreach ($order as $type) {
            switch ($type) {
                case 'preview':
                    if (!is_array($item['PREVIEW_PICTURE']) && intval($item['PREVIEW_PICTURE']) > 0) {
                        $pictures[] = intval($item['PREVIEW_PICTURE']);
                        $isUseDetailPicture = false;
                    } elseif (intval($item['~PREVIEW_PICTURE']) > 0) {
                        $pictures[] = intval($item['~PREVIEW_PICTURE']);
                        $isUseDetailPicture = false;
                    }
                    break;
                case 'more':
                    if (is_array($item['PROPERTIES']['MORE_PHOTO']['VALUE'])) {
                        $pictures = array_merge($pictures, $item['PROPERTIES']['MORE_PHOTO']['VALUE']);
                    }
                    break;
                case 'detail':
                    if (!$isUseDetailPicture) {
                        break;
                    }
                    if (!is_array($item['DETAIL_PICTURE']) && intval($item['DETAIL_PICTURE']) > 0) {
                        $pictures[] = intval($item['DETAIL_PICTURE']);
                    } elseif (intval($item['~DETAIL_PICTURE']) > 0) {
                        $pictures[] = intval($item['~DETAIL_PICTURE']);
                    }
                    break;
            }
        }
        return $pictures;
    }

    public static function printLabels(array $item, string $template, array $types = ['not-avialable', 'sale', 'new']): void
    {
        foreach ($types as $type) {
            if (defined('DISABLE_LABEL') && DISABLE_LABEL === $type) {
                continue;
            }
            switch ($type) {
                case 'new':
                    if ($item['PROPERTIES']['LABEL_NEW']['VALUE_XML_ID'] === 'Y') {
                        echo str_replace(
                            ['#TYPE#', '#TITLE#'],
                            ['new', 'NEW'],
                            $template
                        );
                    }
                    break;

                case 'sale':
                    if ($item['PROPERTIES']['LABEL_SALE']['VALUE_XML_ID'] === 'Y') {
                        echo str_replace(
                            ['#TYPE#', '#TITLE#'],
                            ['sale', 'SALE'],
                            $template
                        );
                    }
                    break;

                case 'not-avialable':
                    $isAvailable = false;
                    if (!empty($item['OFFERS'])) {
                        foreach ($item['OFFERS'] as $offer) {
                            if (empty($offer['CATALOG_AVAILABLE'])) {
                                $offer['CATALOG_AVAILABLE'] = $offer['CAN_BUY'] ? 'Y' : 'N';
                            }
                            if ($offer['CATALOG_AVAILABLE'] === 'Y') {
                                $isAvailable = true;
                                break;
                            }
                        }
                    } else {
                        if (empty($item['CATALOG_AVAILABLE'])) {
                            $item['CATALOG_AVAILABLE'] = $item['CAN_BUY'] ? 'Y' : 'N';
                        }
                        if ($item['CATALOG_AVAILABLE'] === 'Y') {
                            $isAvailable = true;
                        }
                    }

                    if (!$isAvailable) {
                        echo str_replace(
                            ['#TYPE#', '#TITLE#'],
                            ['not-avialable', Loc::getMessage('JOURNAL_TEMPLATE_LABELS_NOT_AVAILABLE')],
                            $template
                        );
                        return;
                    }
                    break;
            }
        }
    }

    public static function getOffersColorsHtml(array $item): string
    {
        $entity = [];
        $colors = [];
        $list = [];
        $pictures = [];

        $type = $item['PROPERTIES']['PREVIEW_ITEM_TYPE']['VALUE_XML_ID'] == 'FULL' ? 'full' : 'item';

        if (!empty($item['OFFERS'])) {
            foreach ($item['OFFERS'] as $offer) {
                $xmlIds = $offer['PROPERTIES']['COLOR']['VALUE'];
                $tableName = $offer['PROPERTIES']['COLOR']['USER_TYPE_SETTINGS']['TABLE_NAME'];

                if (empty($xmlIds)) {
                    continue;
                }

                if (!is_array($xmlIds)) {
                    $xmlIds = [$xmlIds];
                }

                $offerPictures = Template::selectPictures($offer);
                $offerPicture = intval(reset($offerPictures));

                foreach ($xmlIds as $xmlId) {
                    if (empty(static::$colors[$xmlId])) {
                        if (!$entity[$tableName]) {
                            $table = HighloadBlockTable::getList([
                                'filter' => ['=TABLE_NAME' => $tableName],
                            ])->fetch();
                            $entity[$tableName] = HighloadBlockTable::compileEntity($table)->getDataClass();
                        }

                        $row = $entity[$tableName]::getList([
                            'filter' => ['=UF_XML_ID' => $xmlId],
                        ])->fetch();

                        static::$colors[$xmlId] = [
                            'XML_ID' => $xmlId,
                            'NAME' => $row['UF_NAME'],
                            'FILE' => \CFile::ResizeImageGet(
                                $row['UF_FILE'],
                                ['width' => 50, 'height' => 50],
                                BX_RESIZE_IMAGE_EXACT
                            )['src'],
                        ];
                    }

                    if (!empty(static::$colors[$xmlId])) {
                        $pictures[$xmlId] = $offerPicture;
                        $colors[$xmlId] = static::$colors[$xmlId];
                    }
                }
            }

            if (count($colors) > 1) {
                foreach ($colors as $color) {
                    $attribute = [];
                    $attribute[] = "data-type='{$type}'";
                    $pictureId = intval($pictures[$color['XML_ID']]);
                    if ($pictureId) {
                        $resizeData = (new Resize($pictureId, [1000, 1000]))->getResult();
                        if ($resizeData['types']) {
                            $attribute[] = "data-src='{$resizeData['types']['sample']}'";
                            $attribute[] = "data-srcset='{$resizeData['types']['webp']}'";
                        }
                    }
                    $attr = implode(' ', $attribute);
                    $list[] = "<li class='card-colors__item js-card-color' {$attr}><div class='card-colors__item-point' style='background-image:url({$color['FILE']})'></div><div class='card-colors__item-popup'>{$color['NAME']}</div></li>";
                }
            }
        }

        return '<ul class="card-colors">' . implode('', $list) . '</ul>';
    }
}
