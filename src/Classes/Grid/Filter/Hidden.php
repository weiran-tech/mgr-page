<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Grid\Filter;

class Hidden extends FilterItem
{

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected string $view = 'weiran-mgr-page::tpl.filter.hidden';


    public function value($value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function condition(array $inputs)
    {

    }
}
