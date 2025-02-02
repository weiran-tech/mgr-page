<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Grid\Displayer;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Weiran\MgrPage\Classes\Traits\UseInteraction;
use Weiran\MgrPage\Classes\Traits\UseItems;

class Actions extends AbstractDisplayer
{

    use UseItems, UseInteraction;

    /**
     * Append an action.
     *
     * @param array|string $action
     * @return $this
     * @see        add()
     * @deprecated 4.2
     */
    public function append($action): self
    {
        return $this->add($action);
    }

    public function edit($url): void
    {
        $this->iframe('编辑', $url)->icon('pen')->primary();
    }

    public function delete($url, $title): void
    {
        $this->request('删除', $url)->icon('trash')->danger()
            ->confirm("确认删除 [{$title}]?");
    }


    public function disable($url, $title, $status = '已启用'): void
    {
        $this->request($status, $url)->icon('check-circle')
            ->confirm("确定要禁用 [{$title}]")->tooltip("当前启用, 点击禁用 [{$title}]");
    }


    public function enable($url, $title, $status = '已禁用'): void
    {
        $this->request($status, $url)->icon('slash-circle')
            ->confirm("确定启用 [{$title}]")->tooltip("当前禁用, 点击启用 [{$title}]")->danger();
    }

    /**
     * @inheritDoc
     */
    public function display($callback = null): string
    {
        if ($callback instanceof Closure) {
            $callback->call($this, $this);
        }

        $actions = [];
        foreach ($this->items as $append) {
            if ($append instanceof Renderable) {
                $actions[] = $append->render();
            }
        }

        return implode('', $actions);
    }
}
