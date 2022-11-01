<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;

class ReviewsComponent extends \CBitrixComponent
{
    public function executeComponent(): int
    {
        global $USER;

        if (!Loader::includeModule('iblock')) {
            return 0;
        }

        $this->arParams['PAGE_ELEMENT_COUNT'] = abs(intval($this->arParams['PAGE_ELEMENT_COUNT']));
        $navParams = ['nPageSize' => $this->arParams['PAGE_ELEMENT_COUNT']];
        $filter = ['IBLOCK_ID' => intval($this->arParams['IBLOCK_ID'])];

        if ($USER->IsAuthorized() && $this->arParams['HIDE_MODERATED'] !== 'Y') {
            $filter[] = [
                'LOGIC' => 'OR',
                [
                    'CREATED_BY' => $USER->GetID(),
                    'ACTIVE' => 'N',
                ],
                [
                    'ACTIVE' => 'Y',
                ],
            ];
        } else {
            $filter['ACTIVE'] = 'Y';
        }

        if ($this->arParams['SHOW_ALL'] !== 'Y') {
            if (intval($this->arParams['FOR_ID']) > 0) {
                $filter["PROPERTY_{$this->arParams['FOR_PROPERTY']}"] = intval($this->arParams['FOR_ID']);
            } else {
                $filter["PROPERTY_{$this->arParams['FOR_PROPERTY']}"] = false;
            }
        }

        $resource = \CIBlockElement::GetList(
            ['CREATED' => 'DESC', 'ID' => 'DESC'],
            $filter,
            false,
            $navParams
        );

        $this->arResult['ITEMS'] = [];

        while ($object = $resource->GetNextElement()) {
            $reviewData = $object->GetFields();
            $reviewData['PROPERTIES'] = $object->GetProperties();
            $this->arResult['ITEMS'][] = $reviewData;
        }

        $this->arResult['NAV_STRING'] = $resource->GetPageNavStringEx(
            $navComponentObject,
            '',
            $this->arParams['PAGER_TEMPLATE'] ?? '',
            $this->arParams['PAGER_SHOW_ALWAYS'] === 'Y',
            $this
        );

        $this->arResult['REVIEWS_COUNT'] = $resource->SelectedRowsCount();

        if ($this->arParams['RATING_PROPERTY']) {
            $ratingSum = 0;
            $ratingResource = \CIBlockElement::GetList(
                ['CREATED' => 'DESC', 'ID' => 'DESC'],
                $filter,
                false,
                false,
                ["PROPERTY_{$this->arParams['RATING_PROPERTY']}"]
            );
            while ($ratingData = $ratingResource->Fetch()) {
                $rating = intval($ratingData["PROPERTY_{$this->arParams['RATING_PROPERTY']}_VALUE"]) ?: 5;
                $ratingSum += min(5, max(1, $rating));
            }

            $this->arResult['REVIEWS_RATING'] = intval($this->arResult['REVIEWS_COUNT'])
                ? $ratingSum / intval($this->arResult['REVIEWS_COUNT'])
                : 0;
        }

        $this->includeComponentTemplate();
        return intval($this->arResult['REVIEWS_COUNT']);
    }
}
