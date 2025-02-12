<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\Request\Backend;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Weiran\Framework\Classes\Resp;
use Weiran\MgrPage\Classes\Layout\Content;
use Weiran\MgrPage\Http\MgrPage\FormSettingUpload;
use Weiran\System\Classes\Traits\SystemTrait;

/**
 * 上传设置
 */
class UploadController extends BackendController
{
    use SystemTrait;

    public function __construct()
    {
        parent::__construct();

        self::$permission = [
            'global' => 'backend:weiran-system.global.manage',
        ];
    }

    /**
     * 保存邮件配置
     * @return array|JsonResponse|RedirectResponse|Response|Redirector|Resp|Content|\Response
     */
    public function store()
    {
        return (new FormSettingUpload())->render();
    }
}
