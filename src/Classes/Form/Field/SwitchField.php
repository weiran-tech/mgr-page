<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Form\Field;

use Weiran\MgrPage\Classes\Form\Field;
use Weiran\System\Models\SysConfig;

class SwitchField extends Field
{

    protected $default = 0;

    public function render()
    {
        $this->options = [
            SysConfig::NO  => '关闭',
            SysConfig::YES => '开启',
        ];
        return parent::render();
    }
}
