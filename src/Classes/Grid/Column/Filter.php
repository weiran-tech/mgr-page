<?php

namespace Weiran\MgrPage\Classes\Grid\Column;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Weiran\MgrPage\Classes\Grid\Column;

class Filter implements Renderable
{
    /**
     * @var string|array
     */
    protected $class;

    /**
     * @var Column
     */
    protected $parent;

    /**
     * @param Column $column
     */
    public function setParent(Column $column)
    {
        $this->parent = $column;
    }

    /**
     * Get column name.
     *
     * @return string
     */
    public function getColumnName()
    {
        return $this->parent->name;
    }

    /**
     * Get filter value of this column.
     *
     * @param string $default
     *
     * @return array|\Illuminate\Http\Request|string
     */
    public function getFilterValue($default = '')
    {
        return request($this->getColumnName(), $default);
    }

    /**
     * Get form action url.
     *
     * @return string
     */
    public function getFormAction()
    {
        $request = request();

        $query = $request->query();
        Arr::forget($query, [$this->getColumnName(), '_pjax']);

        $question = $request->getBaseUrl() . $request->getPathInfo() == '/' ? '/?' : '?';

        return count($request->query()) > 0
            ? $request->url() . $question . http_build_query($query)
            : $request->fullUrl();
    }

    public function render()
    {
        return '';
    }

    /**
     * @param string $key
     *
     * @return array|null|string
     */
    protected function trans($key)
    {
        return __("admin.{$key}");
    }
}
