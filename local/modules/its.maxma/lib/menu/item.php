<?php

namespace Its\Maxma\Menu;

class Item
{
    protected const PARENT_PREFIX = 'global_menu_';
    private string $code = '';
    private bool $global = false;
    private string $name = '';
    private string $icon = '';
    private string $parent = '';
    private int $sort = 100;
    private string $link = '';
    private array $links = [];
    private bool $show = true;

    /** @var self[] */
    private array $items = [];

    public function __construct(string $code = '', bool $global = false)
    {
        $this->code = $code;
        $this->global = $global;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }


    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;
        return $this;
    }

    public function setOperation(string $code): self
    {
        global $USER;
        $this->show = $USER->CanDoOperation($code);
        return $this;
    }

    public function setMoreLinks(array $links): self
    {
        $this->links = $links;
        return $this;
    }

    public function addMoreLinks(string $link): self
    {
        $this->links[] = $link;
        return $this;
    }

    public function from(string $name): self
    {
        $this->link = $name . '-list.php?lang=' . LANGUAGE_ID;
        $this->links = [];
        $this->links[] = $name . '-list.php';
        $this->links[] = $name . '-edit.php';
        return $this;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    public function setParent(self &$item): self
    {
        $item->addItem($this);
        return $this;
    }

    public function setParentId(string $id): self
    {
        $this->parent = $id;
        return $this;
    }

    public function addItem(self $item): self
    {
        if ($this->global) {
            $item->setParentId($this->getCode());
        }

        $this->items[] = $item;
        return $this;
    }

    public function combineGlobal(): array
    {
        if (!$this->global || !$this->show) {
            return [];
        }

        return [
            'menu_id' => $this->code,
            'text' => $this->name,
            'title' => $this->name,
            'sort' => $this->sort,
            'icon' => $this->icon,
            'items_id' => $this->getItemsId(),
            'help_section' => $this->code,
        ];
    }

    public function getCode(): string
    {
        return ($this->global ? static::PARENT_PREFIX : null) . $this->code;
    }

    public function getItemsId(): string
    {
        return $this->getCode() . '_item_id';
    }

    public function combine(): array
    {
        if (!$this->show) {
            return [];
        }

        if ($this->global) {
            $return = [];

            foreach ($this->items as $item) {
                $return[] = $item->combine();
            }

            return $return;
        }

        $return = [
            'text' => $this->name,
            'title' => $this->name,
            'sort' => $this->sort,
            'icon' => $this->icon,
            'items_id' => $this->getItemsId(),
        ];

        if ($this->link) {
            $return['url'] = $this->link;
        }

        if (count($this->links) > 0) {
            $return['more_url'] = $this->links;
        }

        if ($this->parent) {
            $return['parent_menu'] = $this->parent;
        }

        if (count($this->items) > 0) {
            $return['items'] = [];

            foreach ($this->items as $item) {
                $return['items'][] = $item->combine();
            }
        }

        return $return;
    }
}
