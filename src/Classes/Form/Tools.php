<?php

namespace Weiran\MgrPage\Classes\Form;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Weiran\Framework\Helper\ArrayHelper;

class Tools implements Renderable
{
    /**
     * @var Builder
     */
    protected $form;

    /**
     * Collection of tools.
     *
     * @var array
     */
    protected $tools = ['delete', 'view', 'list'];

    /**
     * Tools should be appends to default tools.
     *
     * @var Collection
     */
    protected $appends;

    /**
     * Tools should be prepends to default tools.
     *
     * @var Collection
     */
    protected $prepends;

    /**
     * Create a new Tools instance.
     *
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->form     = $builder;
        $this->appends  = new Collection();
        $this->prepends = new Collection();
    }

    /**
     * Append a tools.
     *
     * @param mixed $tool
     *
     * @return $this
     */
    public function append($tool)
    {
        $this->appends->push($tool);

        return $this;
    }

    /**
     * Prepend a tool.
     *
     * @param mixed $tool
     *
     * @return $this
     */
    public function prepend($tool)
    {
        $this->prepends->push($tool);

        return $this;
    }

    /**
     * Disable `list` tool.
     *
     * @param bool $disable
     * @return $this
     */
    public function disableList(bool $disable = true)
    {
        if ($disable) {
            ArrayHelper::delete($this->tools, 'list');
        }
        elseif (!in_array('list', $this->tools)) {
            array_push($this->tools, 'list');
        }

        return $this;
    }

    /**
     * Disable `delete` tool.
     *
     * @param bool $disable
     * @return $this
     */
    public function disableDelete(bool $disable = true)
    {
        if ($disable) {
            ArrayHelper::delete($this->tools, 'delete');
        }
        elseif (!in_array('delete', $this->tools)) {
            array_push($this->tools, 'delete');
        }

        return $this;
    }

    /**
     * Disable `edit` tool.
     *
     * @param bool $disable
     * @return $this
     */
    public function disableView(bool $disable = true)
    {
        if ($disable) {
            ArrayHelper::delete($this->tools, 'view');
        }
        elseif (!in_array('view', $this->tools)) {
            array_push($this->tools, 'view');
        }

        return $this;
    }

    /**
     * Get parent form of tool.
     *
     * @return Builder
     */
    public function form()
    {
        return $this->form;
    }

    /**
     * Render tools.
     *
     * @return string
     */
    public function render()
    {
        $output = $this->renderCustomTools($this->prepends);

        foreach ($this->tools as $tool) {
            $renderMethod = 'render' . ucfirst($tool);
            $output       .= $this->$renderMethod();
        }

        return $output . $this->renderCustomTools($this->appends);
    }

    /**
     * Get request path for resource list.
     *
     * @return string
     */
    protected function getListPath()
    {
        return $this->form->getResource();
    }

    /**
     * Get request path for edit.
     *
     * @return string
     */
    protected function getDeletePath()
    {
        return $this->getViewPath();
    }

    /**
     * Get request path for delete.
     *
     * @return string
     */
    protected function getViewPath()
    {
        $key = $this->form->getResourceId();

        if ($key) {
            return $this->getListPath() . '/' . $key;
        }
        else {
            return $this->getListPath();
        }
    }

    /**
     * Render list button.
     *
     * @return string
     */
    protected function renderList()
    {
        $text = trans('admin.list');

        return <<<EOT
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="{$this->getListPath()}" class="btn btn-sm btn-default" title="$text"><i class="fa fa-list"></i><span class="hidden-xs">&nbsp;$text</span></a>
</div>
EOT;
    }

    /**
     * Render list button.
     *
     * @return string
     */
    protected function renderView()
    {
        $view = trans('admin.view');

        return <<<HTML
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="{$this->getViewPath()}" class="btn btn-sm btn-primary" title="{$view}">
        <i class="fa fa-eye"></i><span class="hidden-xs"> {$view}</span>
    </a>
</div>
HTML;
    }

    /**
     * Render `delete` tool.
     *
     * @return string
     */
    protected function renderDelete()
    {
        $trans = [
            'delete_confirm' => trans('admin.delete_confirm'),
            'confirm'        => trans('admin.confirm'),
            'cancel'         => trans('admin.cancel'),
            'delete'         => trans('admin.delete'),
        ];

        $class = uniqid();

        return <<<HTML
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="javascript:void(0);" class="btn btn-sm btn-danger {$class}-delete" title="{$trans['delete']}">
        <i class="fa fa-trash"></i><span class="hidden-xs">  {$trans['delete']}</span>
    </a>
</div>
HTML;
    }

    /**
     * Render custom tools.
     *
     * @param Collection $tools
     *
     * @return mixed
     */
    protected function renderCustomTools($tools)
    {
        if ($this->form->isCreating()) {
            $this->disableView();
            $this->disableDelete();
        }

        if (!$tools->count()) {
            return '';
        }

        return $tools->map(function ($tool) {
            if ($tool instanceof Renderable) {
                return $tool->render();
            }

            if ($tool instanceof Htmlable) {
                return $tool->toHtml();
            }

            return (string) $tool;
        })->implode(' ');
    }
}
