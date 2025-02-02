<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Form\Field;

class Email extends Text
{
    protected $rules = [
        'nullable', 'email',
    ];

    public function render()
    {
        $this->defaultAttribute('type', 'email');

        return parent::render();
    }
}
