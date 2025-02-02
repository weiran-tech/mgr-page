<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Form\Field;

use Weiran\Framework\Validation\Rule;

class Mobile extends Text
{

    public function __construct($column = '', $arguments = [])
    {
        parent::__construct($column, $arguments);
        $this->rules([Rule::mobile()], [
            'mobile' => '输入类型必须是手机号',
        ]);
    }


    public function render()
    {
        $this->defaultAttribute('style', 'width: 150px');

        return parent::render();
    }
}
