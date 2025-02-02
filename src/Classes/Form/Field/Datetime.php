<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Form\Field;

class Datetime extends Date
{
    protected $options = [
        'layui-type' => 'datetime',
    ];

    protected $attributes = [
        'style' => 'width: 180px',
    ];
}
