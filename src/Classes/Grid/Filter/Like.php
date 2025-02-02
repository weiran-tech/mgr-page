<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Grid\Filter;

use Illuminate\Support\Arr;

class Like extends FilterItem
{
    /**
     * @var string
     */
    protected string $exprFormat = '%{value}%';

    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     *
     * @return array|void
     */
    public function condition(array $inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if (is_array($value)) {
            $value = array_filter($value);
        }

        if (empty($value)) {
            return;
        }

        $this->value = $value;

        $expr = str_replace('{value}', $this->value, $this->exprFormat);

        return $this->buildCondition($this->column, 'like', $expr);
    }
}
