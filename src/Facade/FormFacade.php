<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Facade;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

/**
 * 前台框架
 */
class FormFacade extends IlluminateFacade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'weiran.mgr-page.form';
    }
}