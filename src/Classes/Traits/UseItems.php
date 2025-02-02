<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Traits;

use Illuminate\Contracts\Support\Renderable;

trait UseItems
{

    /**
     * @var array
     */
    protected array $items = [];


    /**
     * Append an action.
     *
     * @param array|Renderable $action
     *
     * @return $this
     */
    public function add($action): self
    {
        if (is_array($action)) {
            $this->items = array_merge($this->items, $action);
        }
        else {
            $this->items[] = $action;
        }
        return $this;
    }
}
