<?php

namespace Journal;

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;

class Seo
{
    public static function epilogActions(): void
    {
        global $APPLICATION;

        if (defined('ADMIN_SECTION') && ADMIN_SECTION) {
            return;
        }

        $request = Context::getCurrent()->getRequest();
        for ($number = 1; $number <= 3; $number++) {
            $key = "PAGEN_$number";
            $page = intval($request->get($key));
            if (!$page) {
                continue;
            }
            if ($page === 1) {
                LocalRedirect(
                    $APPLICATION->GetCurPageParam('', [$key]),
                    false,
                    '301 Moved Permanently'
                );
                break;
            }
            $APPLICATION->SetPageProperty(
                'title',
                $APPLICATION->GetTitle('title')
                    . Loc::getMessage('SEO_PAGE_NAVIGATION_TITLE_ADD', ['#PAGE#' => $page])
            );
            $APPLICATION->SetPageProperty(
                'description',
                Loc::getMessage('SEO_PAGE_NAVIGATION_DESCRIPTION_ADD', ['#PAGE#' => $page])
                    . $APPLICATION->GetPageProperty('description')
            );
        }
    }
}
