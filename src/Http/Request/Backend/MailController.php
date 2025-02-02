<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\Request\Backend;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Weiran\Framework\Classes\Resp;
use Weiran\MgrPage\Classes\Layout\Content;
use Weiran\MgrPage\Http\MgrPage\FormMailStore;
use Weiran\MgrPage\Http\MgrPage\FormMailTest;
use Weiran\System\Classes\Traits\SystemTrait;

/**
 * 邮件控制器
 */
class MailController extends BackendController
{
    use SystemTrait;

    public function __construct()
    {
        parent::__construct();

        self::$permission = [
            'global' => 'backend:py-system.global.manage',
        ];
    }

    /**
     * 保存邮件配置
     * @return array|JsonResponse|RedirectResponse|Response|Redirector|Resp|Content|\Response
     */
    public function store()
    {
        return (new FormMailStore())->render();
    }

    /**
     * 测试邮件发送
     */
    public function test()
    {
        return (new FormMailTest())->render();
    }
}
