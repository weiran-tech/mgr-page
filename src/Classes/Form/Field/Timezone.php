<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Form\Field;

use DateTimeZone;

class Timezone extends Select
{
    protected string $view = 'py-mgr-page::tpl.form.select';

    public function render()
    {
        $this->options = collect(DateTimeZone::listIdentifiers())->mapWithKeys(function ($timezone) {
            return [$timezone => $timezone];
        })->toArray();

        return parent::render();
    }
}
