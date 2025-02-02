<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\Request\Backend;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Weiran\Framework\Classes\Resp;
use Weiran\Framework\Exceptions\ApplicationException;
use Weiran\MgrPage\Classes\Grid;
use Weiran\MgrPage\Http\MgrPage\FormBanEstablish;
use Weiran\MgrPage\Http\MgrPage\FormSettingBan;
use Weiran\MgrPage\Http\MgrPage\ListPamBan;
use Weiran\System\Action\Ban;
use Weiran\System\Models\PamAccount;
use Weiran\System\Models\PamBan;
use Weiran\System\Models\SysConfig;
use Response;
use Throwable;

class BanController extends BackendController
{
    /**
     * 设备
     * @throws ApplicationException
     * @throws Throwable
     */
    public function index()
    {
        $grid = new Grid(new PamBan());
        $grid->setLists(ListPamBan::class);
        return $grid->render();
    }


    public function status()
    {
        $type   = input('type');
        $key    = 'py-system::ban.status-' . $type;
        $status = sys_setting($key, SysConfig::STR_NO);
        app('poppy.system.setting')->set($key, $status === 'Y' ? SysConfig::STR_NO : SysConfig::STR_YES);
        return Resp::success('已切换', '_reload|1');
    }

    public function type()
    {
        $type    = input('type');
        $key     = 'py-system::ban.type-' . $type;
        $isBlank = sys_setting($key, PamBan::WB_TYPE_BLACK) === PamBan::WB_TYPE_BLACK;
        app('poppy.system.setting')->set($key, $isBlank ? PamBan::WB_TYPE_WHITE : PamBan::WB_TYPE_BLACK);
        return Resp::success('已切换封禁模式', '_reload|1');
    }

    /**
     * 创建/编辑
     * @param null $id
     * @return array|JsonResponse|RedirectResponse|\Illuminate\Http\Response|Redirector|mixed|Resp|Response|string
     * @throws ApplicationException
     */
    public function establish($id = null)
    {
        $form = new FormBanEstablish();
        $form->setId($id);
        $form->setAccountType(input('type', PamAccount::TYPE_USER));
        return $form->render();
    }

    /**
     * 创建/编辑
     * @return array|JsonResponse|RedirectResponse|\Illuminate\Http\Response|Redirector|mixed|Resp|Response|string
     */
    public function setting()
    {
        $form = new FormSettingBan();
        $form->setAccountType(input('type', PamAccount::TYPE_USER));
        return $form->render();
    }

    /**
     * 删除
     * @param $id
     * @return \Illuminate\Http\Response|JsonResponse|RedirectResponse
     */
    public function delete($id)
    {
        $Ban = new Ban();
        if (!$Ban->delete((int) $id)) {
            return Resp::error($Ban->getError());
        }
        return Resp::success('删除成功', '_reload|1');
    }
}