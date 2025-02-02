<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Form\Field;

use Weiran\MgrPage\Classes\Form\Field;
use Weiran\MgrPage\Classes\Form\Traits\PlainInput;

class Date extends Field
{
    use PlainInput;

    protected $options = [
        'type' => 'date',
    ];

    protected $attributes = [
        'style' => 'width: 110px',
    ];

    protected string $view = 'py-mgr-page::tpl.form.date';

    public function render()
    {

        $this->addVariables([
            'options' => $this->options,
        ]);
        return parent::render();
    }
}
