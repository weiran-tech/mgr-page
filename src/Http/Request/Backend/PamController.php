<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\Request\Backend;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Weiran\Framework\Classes\Resp;
use Weiran\Framework\Exceptions\ApplicationException;
use Weiran\MgrPage\Classes\Grid;
use Weiran\MgrPage\Http\MgrPage\FormPamDisable;
use Weiran\MgrPage\Http\MgrPage\FormPamEnable;
use Weiran\MgrPage\Http\MgrPage\FormPamEstablish;
use Weiran\MgrPage\Http\MgrPage\FormPamMobile;
use Weiran\MgrPage\Http\MgrPage\FormPamNote;
use Weiran\MgrPage\Http\MgrPage\FormPamPassword;
use Weiran\MgrPage\Http\MgrPage\FormPamSetting;
use Weiran\MgrPage\Http\MgrPage\FormSettingLog;
use Weiran\MgrPage\Http\MgrPage\ListPamAccount;
use Weiran\MgrPage\Http\MgrPage\ListPamLog;
use Weiran\MgrPage\Http\MgrPage\ListPamToken;
use Weiran\System\Action\Ban;
use Weiran\System\Action\Pam;
use Weiran\System\Action\Sso;
use Weiran\System\Events\PamTokenBanEvent;
use Weiran\System\Models\PamAccount;
use Weiran\System\Models\PamLog;
use Weiran\System\Models\PamToken;
use Throwable;

/**
 * 账户管理
 */
class PamController extends BackendController
{
    public function __construct()
    {
        parent::__construct();

        self::$permission = [
            'global'   => 'backend:weiran-system.pam.manage',
            'password' => 'backend:weiran-system.pam.password',
            'log'      => 'backend:weiran-system.pam.log',
            'disable'  => 'backend:weiran-system.pam.disable',
            'enable'   => 'backend:weiran-system.pam.enable',
        ];
    }

    /**
     * Display a listing of the resource.
     * @throws ApplicationException|Throwable
     */
    public function index()
    {
        return (new Grid(new PamAccount()))
            ->setLists(ListPamAccount::class)->render();
    }

    /**
     * 创建/编辑
     */
    public function establish()
    {
        return (new FormPamEstablish())->render();
    }

    /**
     * 设置密码
     */
    public function password()
    {
        return (new FormPamPassword())->render();
    }

    /**
     * 设置备注
     */
    public function note()
    {
        return (new FormPamNote())->render();
    }

    /**
     * 禁用用户
     */
    public function disable()
    {
        return (new FormPamDisable())->render();
    }

    /**
     * 启用用户
     */
    public function enable()
    {
        return (new FormPamEnable())->render();
    }

    public function setting()
    {
        return (new FormPamSetting())->render();
    }


    public function mobile()
    {
        return (new FormPamMobile())->render();
    }

    public function clearMobile(int $id)
    {
        $Pam = new Pam();
        $Pam->setPam($this->pam());
        if (!$Pam->clearMobile($id)) {
            return Resp::error($Pam->getError());
        }
        return Resp::success('已清除此用户手机通行证', '_top_reload|1');
    }

    /**
     * @return Response|JsonResponse|RedirectResponse|string
     * @throws ApplicationException
     * @throws Throwable
     */
    public function log()
    {
        return (new Grid(new PamLog()))
            ->setLists(ListPamLog::class)->render();
    }

    public function settingLog()
    {
        return (new FormSettingLog())->render();
    }

    /**
     * @return Response|JsonResponse|RedirectResponse|string
     * @throws ApplicationException
     * @throws Throwable
     */
    public function token()
    {
        return (new Grid(new PamToken()))
            ->setLists(ListPamToken::class)->render();
    }

    public function ban($id, $type)
    {
        $Ban = new Ban();
        if (!$Ban->type($id, $type)) {
            return Resp::error($Ban->getError());
        }
        return Resp::success('禁用成功', '_top_reload|1');
    }

    /**
     * 删除用户的指定 Token
     * @param $id
     * @return JsonResponse|RedirectResponse|Response
     * @throws Exception
     */
    public function deleteToken($id)
    {
        $item = PamToken::find($id);

        // 踢下线(当前用户不可访问)
        (new Sso())->banToken($item);

        event(new PamTokenBanEvent($item, 'token'));
        return Resp::error('删除用户成功, 用户已无法访问(需重新登录)', '_top_reload|1');
    }
}