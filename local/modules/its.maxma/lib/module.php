<?php

namespace Its\Maxma;

class Module
{
    public static function getData(): array
    {
        $data = include dirname(__DIR__) . '/module.php';
        return is_array($data) ? $data : [];
    }

    public static function getId(): string
    {
        $data = static::getData();
        return strval($data['MODULE_ID']);
    }
}
