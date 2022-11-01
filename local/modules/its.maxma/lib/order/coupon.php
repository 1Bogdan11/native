<?php

namespace Its\Maxma\Order;

class Coupon
{
    protected const SESSION_KEY = 'ITS_MAXMA_SESSION_PROMOCODE';

    public static function setToSession(string $coupon = ''): void
    {
        $_SESSION[static::SESSION_KEY] = $coupon;
    }

    public static function getFromSession(): string
    {
        return strval($_SESSION[static::SESSION_KEY]);
    }

    public static function clear(): void
    {
        unset($_SESSION[static::SESSION_KEY]);
    }
}
