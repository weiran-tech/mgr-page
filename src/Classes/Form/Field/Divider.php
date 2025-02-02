<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Form\Field;

use Weiran\MgrPage\Classes\Form\Field;

class Divider extends Field
{
    protected $title;

    public function __construct($title = '')
    {
        parent::__construct($title);
        $this->title = $title;
    }

    public function render(): string
    {
        if (empty($this->title)) {
            return '<hr>';
        }

        return <<<HTML
<div style="height: 20px; border-bottom: 1px solid #eee; text-align: center;margin-top: 10px;margin-bottom: 20px;">
	<span style="font-size: 14px; background-color: #ffffff; padding: 0 10px;position: relative;top: 7px;">
		{$this->title}
	</span>
</div>
HTML;
    }
}
