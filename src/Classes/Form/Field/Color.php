<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Form\Field;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class Color extends Text
{

    protected string $view = 'weiran-mgr-page::tpl.form.color';

    /**
     * Render this filed.
     *
     * @return Factory|View
     */
    public function render()
    {
        $this->defaultAttribute('style', 'width: 140px');

        return parent::render();
    }
}
