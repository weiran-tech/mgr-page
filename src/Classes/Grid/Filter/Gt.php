<?php

namespace Weiran\MgrPage\Classes\Grid\Filter;

use Illuminate\Support\Arr;

class Gt extends FilterItem
{
    /**
     * @inheritDoc
     */
    protected string $view = 'weiran-mgr-page::tpl.filter.gt';

    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     *
     * @return array|mixed|void
     */
    public function condition(array $inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if (is_null($value)) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, '>', $this->value);
    }
}
