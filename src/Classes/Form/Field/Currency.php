<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Form\Field;

class Currency extends Text
{

    /**
     * @inheritDoc
     */
    public function prepare($value)
    {
        return (float) $value;
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->defaultAttribute('style', 'width: 120px');
        $this->addVariables([
            'type' => 'number',
        ]);
        return parent::render();
    }
}
