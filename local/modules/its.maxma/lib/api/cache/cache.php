<?php

namespace Its\Maxma\Api\Cache;

class Cache
{
    protected static array $cache = [];
    protected string $id = '';
    protected int $time = 0;

    public static function makeInstance(array $id, int $time = 0): self
    {
        if ($time > 0) {
            return new LongCache($id, $time);
        }
        return new self($id, 0);
    }

    protected function __construct(array $id, int $time = 0)
    {
        if (empty($id)) {
            throw new \Exception('Cache id is empty!');
        }

        $this->time = $time;
        $this->id = serialize($id);
    }

    public function isSuccess(): bool
    {
        return isset(static::$cache[$this->id]);
    }

    public function destroy(): void
    {
        unset(static::$cache[$this->id]);
    }

    public function getVars()
    {
        return static::$cache[$this->id];
    }

    public function setVars($vars): void
    {
        static::$cache[$this->id] = $vars;
    }
}
