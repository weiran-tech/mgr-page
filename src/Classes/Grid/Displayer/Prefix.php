<?php

namespace Weiran\MgrPage\Classes\Grid\Displayer;

class Prefix extends AbstractDisplayer
{
    public function display($prefix = null, $delimiter = '&nbsp;')
    {
        if ($prefix instanceof \Closure) {
            $prefix = $prefix->call($this->row, $this->getValue(), $this->getColumn()->getOriginal());
        }

        return <<<HTML
{$prefix}{$delimiter}{$this->getValue()}
HTML;
    }
}
