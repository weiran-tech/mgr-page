<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Hooks\MgrPage;

use Weiran\Core\Services\Contracts\ServiceArray;
use Weiran\MgrPage\Http\MgrPage\FormSettingPam;
use Weiran\MgrPage\Http\MgrPage\FormSettingSite;

class SettingSystem implements ServiceArray
{

    public function key(): string
    {
        return 'weiran.mgr-page';
    }

    public function data(): array
    {
        return [
            'title' => '系统',
            'forms' => [
                FormSettingSite::class,
                FormSettingPam::class,
            ],
        ];
    }
}