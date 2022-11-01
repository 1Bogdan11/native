<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $templateData
 */
//не будет переключения размеров/цветов. просто отобразим существующие варианты

$minPrice = [];
$haveOffers = !empty($arResult['OFFERS']);
$skuProps = [ //св-ва по которым формируется SKU
    \Journal\GlobalStorage::get('SKU_PROP_COLOR'),
    \Journal\GlobalStorage::get('SKU_PROP_SIZE')
];
$skuPropsVariant = [];
if ($haveOffers) {
    $minPrice = $arResult['OFFERS'][0]['ITEM_PRICES'][0]; //цена первого торгового предложения

    foreach ($arResult['OFFERS'] as $offer) {
        $offerCanBuy = $offer['CAN_BUY'];
        foreach ($skuProps as $skuProp) {
            if ($offer['PROPERTIES'][$skuProp]['VALUE']) {
                $currentPropValue = $offer['PROPERTIES'][$skuProp]['VALUE'];
                $propDisplay = CIBlockFormatProperties::GetDisplayValue($offer, $offer['PROPERTIES'][$skuProp]);
                if ($propDisplay['DISPLAY_VALUE'] != '') {
                    $propDisplayValue = $propDisplay['DISPLAY_VALUE'];
                }
                if ($skuPropsVariant[$skuProp][$currentPropValue]) { //если уже было такое св-во
                    if ($offerCanBuy) { //перезапищем, только если  CAN_BUY == true
                        $skuPropsVariant[$skuProp][$currentPropValue]['can_buy'] = $offerCanBuy;
                    }
                } else {
                    $skuPropsVariant[$skuProp][$currentPropValue] = [
                        'value' => $currentPropValue,
                        'display_value' => $propDisplayValue,
                        'can_buy' => $offerCanBuy,
                    ];
                }


                $propDisplayValue = '';
                unset($propDisplay);
            }
        }

        if ($minPrice['PRICE'] > $offer['ITEM_PRICES'][0]['PRICE']) { //gecnjq
            $minPrice = $offer['ITEM_PRICES'][0];
        }
    }
}
$arResult['ADD_DATA']['SKU_PROPS_VARIANT'] = $skuPropsVariant;
$arResult['ADD_DATA']['PRICE'] = $minPrice;
