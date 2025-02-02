<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\Request\Develop;

use Weiran\Core\Classes\Traits\CoreTrait;
use Weiran\Framework\Classes\Traits\ViewTrait;
use Weiran\MgrPage\Http\Request\Backend\BackendController;
use View;

/**
 * 开发平台初始化
 */
class DevelopController extends BackendController
{
    public static array $permission = [
        'global' => 'backend:py-system.develop.manage',
    ];

    use ViewTrait, CoreTrait;

    public function __construct()
    {
        parent::__construct();
        View::share('_menus', $this->coreModule()->menus()->where('type', 'develop')->toArray());
    }
}
