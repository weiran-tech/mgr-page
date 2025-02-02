<?php

namespace Weiran\MgrPage\Classes\Grid\Filter\Layout;

use Closure;
use Illuminate\Support\Collection;
use Weiran\MgrPage\Classes\Grid\Filter;
use Weiran\MgrPage\Classes\Grid\Filter\FilterItem;

/**
 * 布局
 */
class Layout
{
    /**
     * @var Collection|Column[]
     */
    protected Collection $columns;

    /**
     * @var Column
     */
    protected Column $current;

    /**
     * @var Filter
     */
    protected Filter $parent;

    /**
     * Layout constructor.
     *
     * @param Filter $filter
     */
    public function __construct(Filter $filter)
    {
        $this->parent  = $filter;
        $this->current = new Column();
        $this->columns = new Collection();
    }

    /**
     * Add a filter to layout column.
     *
     * @param FilterItem $filter
     */
    public function addFilter(FilterItem $filter)
    {
        $this->current->addFilter($filter);
    }

    /**
     * Add a new column in layout.
     *
     * @param int|float $width
     * @param Closure   $closure
     */
    public function column($width, Closure $closure)
    {
        if ($this->columns->isEmpty()) {
            $column = $this->current;
            $column->setWidth($width);
        }
        else {
            $column        = new Column($width);
            $this->current = $column;
        }

        $this->columns->push($column);
        $closure($this->parent);
    }

    /**
     * Get all columns in filter layout.
     *
     * @return Collection
     */
    public function columns(): Collection
    {
        if ($this->columns->isEmpty()) {
            $this->columns->push($this->current);
        }
        return $this->columns;
    }

    /**
     * 过滤条件数量
     * @return int
     */
    public function filterCount(): int
    {
        $count = 0;
        foreach ($this->columns as $column) {
            $count += $column->filterCount();
        }
        return $count;
    }
}
