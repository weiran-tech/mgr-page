<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Form\Field;

class DateRange extends Date
{
    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->options([
            'layui-range' => true,
        ]);
        return parent::render();
    }
}
