<?php

namespace Its\Maxma\Api;

use Bitrix\Main\Config\Option;
use Its\Maxma\Module;
use Bitrix\Main\Application;

class Log
{
    private static ?Log $instance = null;

    private bool $enable;
    private string $path;

    final public static function getInstance(): self
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __construct()
    {
        $this->enable = Option::get(Module::getId(), 'its_maxma_enable_error_log') === 'Y';

        $path = Option::get(Module::getId(), 'its_maxma_error_log_path');
        $this->path = str_replace('#DATE#', (new \DateTime())->format('d.m.Y'), $path);
    }

    public function flush($data): void
    {
        if (!$this->enable) {
            return;
        }
        file_put_contents(
            Application::getDocumentRoot() . $this->path,
            var_export(
                [
                    'time' => (new \DateTime())->format('D.m.y H:i:s'),
                    'data' => $data,
                ],
                true
            ),
            FILE_APPEND
        );
    }
}
