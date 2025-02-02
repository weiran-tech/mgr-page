<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Grid\Filter;

class EndsWith extends Like
{
    protected string $exprFormat = '%{value}';
}
