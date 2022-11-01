<?php

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Context;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;
use Bitrix\Main\Request;
use Bitrix\Sale\Basket;
use Bitrix\Sale\BasketBase;
use Bitrix\Sale\BasketItem;
use Bitrix\Sale\Discount;
use Bitrix\Sale\DiscountBase;
use Bitrix\Sale\Fuser;
use Bitrix\Sale\Discount\Context\Fuser as FuserDiscount;
use Journal\Favorite\Favorite;
use Its\Library\Iblock\Iblock;

class SiteDataAjaxComponent extends Controller
{
    private Favorite $favorite;

    public function __construct(Request $request = null)
    {
        global $USER;

        $this->favorite = new Favorite(
            Iblock::getInstance()->get('catalog'),
            $USER->IsAuthorized() ? intval($USER->GetID()) : 0
        );

        parent::__construct($request);
    }

    public function executeComponent()
    {
        return;
    }

    public function configureActions()
    {
        return [
            'summary' => [
                'prefilters' => [
                    new HttpMethod([HttpMethod::METHOD_POST]),
                    new Csrf(),
                ]
            ],
            'addToFavorite' => [
                'prefilters' => [
                    new HttpMethod([HttpMethod::METHOD_POST]),
                    new Csrf(),
                ]
            ],
            'removeFromFavorite' => [
                'prefilters' => [
                    new HttpMethod([HttpMethod::METHOD_POST]),
                    new Csrf(),
                ]
            ],
        ];
    }

    public function summaryAction()
    {
        return [
            'basket' => $this->getBasket(),
            'favorite' => $this->getFavorite(),
        ];
    }

    public function addToFavoriteAction(int $productId = null)
    {
        $this->favorite->add(intval($productId));
        $this->favorite->save();
        return $this->summaryAction();
    }

    public function removeFromFavoriteAction(int $productId = null)
    {
        $this->favorite->remove(intval($productId));
        $this->favorite->save();
        return $this->summaryAction();
    }

    private function getFavorite(): array
    {
        return [
            'items' => $this->favorite->getItems(),
        ];
    }

    private function getBasket(): array
    {
        global $USER;

        $basketData = [
            'total' => 0,
            'totalDiscount' => 0,
            'totalFormatted' => '',
            'totalDiscountFormatted' => '',
            'quantity' => 0,
            'unique' => 0,
            'items' => [],
        ];

        try {
            if (!Loader::includeModule('sale') || !Loader::includeModule('currency')) {
                throw new \Exception();
            }

            $basket = Basket::loadItemsForFUser(Fuser::getId(), Context::getCurrent()->getSite());

            if ($basket instanceof BasketBase) {
                $context = new FuserDiscount($basket->getFUserId());
                $discounts = Discount::buildFromBasket($basket, $context);
                if ($discounts instanceof DiscountBase) {
                    $result = $discounts->calculate();
                    if ($result->isSuccess()) {
                        $data = $result->getData();
                        $basket->applyDiscount($data['BASKET_ITEMS']);
                    }
                }

                $basketData['total'] = $basket->getBasePrice();
                $basketData['totalDiscount'] = $basket->getPrice();
                $basketData['totalFormatted'] = \CCurrencyLang::CurrencyFormat($basket->getBasePrice(), CurrencyManager::getBaseCurrency());
                $basketData['totalDiscountFormatted'] = \CCurrencyLang::CurrencyFormat($basket->getPrice(), CurrencyManager::getBaseCurrency());
                $basketData['unique'] = count($basket);

                foreach ($basket as $item) {
                    /** @var $item BasketItem */
                    $basketData['quantity'] += $item->getQuantity();
                    $basketData['items'][] = intval($item->getProductId());
                }
            }
        } catch (\Throwable $e) {
            if ($USER->IsAdmin()) {
                $basketData['error'] = [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace(),
                ];
            }
        }

        return $basketData;
    }
}
