<?php

namespace Journal;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;

class OpenGraph
{
    protected static string $site = '';

    public static function getSite(): string
    {
        if (strlen(static::$site) === 0) {
            $context = Context::getCurrent();
            $server = $context->getServer();
            $request = $context->getRequest();
            $method = $request->isHttps() ? 'https' : 'http';
            static::$site = "{$method}://{$server->getServerName()}";
        }
        return static::$site;
    }

    public static function setProperty(string $code, string $value): void
    {
        if (empty($code) || empty($value)) {
            return;
        }

        global $APPLICATION;
        $APPLICATION->SetPageProperty("og:$code", $value);
    }

    public static function setInnerLink(string $code, string $value): void
    {
        if (empty($code) || empty($value)) {
            return;
        }

        static::setProperty($code, static::getSite() . $value);
    }

    public static function setDefaults(): void
    {
        global $APPLICATION;
        $APPLICATION->SetPageProperty(
            'og:title',
            $APPLICATION->GetPageProperty('og:title', $APPLICATION->GetTitle(false))
        );
        $APPLICATION->SetPageProperty(
            'og:description',
            $APPLICATION->GetPageProperty(
                'og:description',
                $APPLICATION->GetPageProperty('description', $APPLICATION->GetDirProperty('description'))
            )
        );
        $APPLICATION->SetPageProperty(
            'og:url',
            $APPLICATION->GetPageProperty('og:url', static::getSite() . $APPLICATION->GetCurPageParam())
        );
        $APPLICATION->SetPageProperty(
            'og:image',
            $APPLICATION->GetPageProperty('og:image', static::getSite() . '/og_image.jpg')
        );
        $APPLICATION->SetPageProperty('og:type', 'website');
        $APPLICATION->SetPageProperty('og:locale', 'ru_RU');
        $APPLICATION->SetPageProperty('og:site_name', Option::get('main', 'site_name'));
    }

    public static function showMeta(): void
    {
        global $APPLICATION;
        $APPLICATION->ShowMeta('og:title');
        $APPLICATION->ShowMeta('og:description');
        $APPLICATION->ShowMeta('og:url');

        $APPLICATION->ShowMeta('og:image');
        $APPLICATION->ShowMeta('og:image:secure_url');
        $APPLICATION->ShowMeta('og:image:alt');
        $APPLICATION->ShowMeta('og:image:type');
        $APPLICATION->ShowMeta('og:image:width');
        $APPLICATION->ShowMeta('og:image:height');

        $APPLICATION->ShowMeta('og:type');
        $APPLICATION->ShowMeta('og:site_name');
    }
}
