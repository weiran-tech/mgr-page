<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Form\Field;

class TimeRange extends Time
{
    public function render()
    {
        $this->options([
            'layui-range' => true,
        ]);
        return parent::render();
    }
}
