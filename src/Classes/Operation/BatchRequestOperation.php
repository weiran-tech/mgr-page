<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Operation;

use Illuminate\Support\Str;

/**
 * 批量请求
 */
class BatchRequestOperation extends Operation
{

    protected string $renderType = 'button';

    public function render(): string
    {
        $this->attributes['data-url']  = $this->url;
        $this->attributes['lay-event'] = Str::random(4);
        $this->classes[]               = 'J_request';
        return parent::render();
    }
}
