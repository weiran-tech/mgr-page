<?php

namespace Weiran\MgrPage\Classes\Grid\Tools;

use Weiran\MgrPage\Classes\Grid\Filter;
use Throwable;

/**
 * 筛选按钮
 */
class FilterButton extends AbstractTool
{
    /**
     * @var string
     */
    protected string $view = 'weiran-mgr-page::tpl.filter.button';

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function render()
    {
        $variables = [
            'url_no_scopes' => $this->filter()->urlWithoutScopes(),
            'filter_id'     => $this->filter()->getFilterId(),
        ];

        return view($this->view, $variables)->render();
    }

    /**
     * @return Filter
     */
    protected function filter(): Filter
    {
        return $this->grid->getFilter();
    }
}