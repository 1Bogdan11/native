<?php

namespace Its\Maxma\Api\Cache;

use Bitrix\Main\Application;
use Bitrix\Main\Data\ManagedCache;

class LongCache extends Cache
{
    protected ManagedCache $manager;
    protected bool $success = false;

    protected function __construct(array $id, int $time = 0)
    {
        parent::__construct($id, $time);
        $this->manager = Application::getInstance()->getManagedCache();
        $this->success = boolval($this->manager->read($this->time, $this->id, 'its_maxma_api'));
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function destroy(): void
    {
        $this->manager->clean($this->id);
    }

    public function getVars()
    {
        if (!$this->success) {
            return null;
        }
        return $this->manager->get($this->id);
    }

    public function setVars($vars): void
    {
        $this->manager->set($this->id, $vars);
    }
}
