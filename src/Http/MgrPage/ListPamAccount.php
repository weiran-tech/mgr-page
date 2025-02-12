<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\MgrPage;

use Auth;
use Closure;
use Illuminate\Support\Str;
use Weiran\Framework\Exceptions\ApplicationException;
use Weiran\MgrPage\Classes\Grid\Column;
use Weiran\MgrPage\Classes\Grid\Displayer\Actions;
use Weiran\MgrPage\Classes\Grid\Filter;
use Weiran\MgrPage\Classes\Grid\Filter\Scope;
use Weiran\MgrPage\Classes\Grid\ListBase;
use Weiran\MgrPage\Classes\Operations;
use Weiran\System\Models\PamAccount;
use Weiran\System\Models\PamRole;
use Weiran\System\Models\PamRoleAccount;

class ListPamAccount extends ListBase
{

    public string $title = '账号管理';

    /**
     * @inheritDoc
     * @throws ApplicationException
     */
    public function columns(): void
    {
        $user = Auth::user();
        $this->column('id', 'ID')->sortable()->width(100);
        $this->column('username', '用户名')->width(130, true);
        $this->column('mobile', '手机号')->width(160, true)->display(function ($item) {
            return Str::after($item, '-');
        });
        if (config('poppy.system.enable_email')) {
            $this->column('email', '邮箱');
        }

        $this->column('login_times', '登录次数')->widthAsId();
        $this->column('created_at', '注册/创建时间')->widthAsDatetime();
        $this->column('reg_ip', '注册 IP')->widthAsIp();
        $this->column('logined_at', '最后登录时间')->widthAsDatetime();
        $this->column('note', '备注')->widthAsNote();
        $this->addColumn(Column::NAME_ACTION, '操作')->displayUsing(Actions::class, [function (Actions $actions) use ($user) {
            /** @var PamAccount $item */
            $item = $actions->row;

            $actions->dropdown('编辑', function (Operations $operations) use ($item, $user) {
                if ($user->can('password', $item)) {
                    $operations->iframe('修改密码', route('weiran-mgr-page:backend.pam.password', [$item->id]))->icon('key')->primary();
                }
                $operations->iframe('编辑', route_url('weiran-mgr-page:backend.pam.establish', [$item->id]))->icon('pen')->primary();
                if ($user->can('beMobile', $item)) {
                    $operations->iframe('修改手机号', route_url('weiran-mgr-page:backend.pam.mobile', [$item->id]))->icon('phone')->primary();
                }
                if ($user->can('beClearMobile', $item)) {
                    $operations->request('清空手机号', route_url('weiran-mgr-page:backend.pam.clear_mobile', [$item->id]))
                        ->confirm('确认要清空此用户的通行证, 清空之后此用户无法进行登录操作')
                        ->icon('phone-flip')->danger();
                }
                $operations->iframe('姓名', route_url('weiran-mgr-page:backend.pam.note', [$item->id]))->icon('sticky')->primary();
                $operations->iframe('设置', route_url('weiran-mgr-page:backend.pam.setting', [$item->id]))->icon('gear')->primary();
            })->icon('lay:edit');
            if ($user->can('disable', $item)) {
                $actions->iframe('已启用', route_url('weiran-mgr-page:backend.pam.disable', [$item->id]))->icon('check-circle')->default()
                    ->tooltip('当前启用, 点击禁用');
            }
            if ($user->can('enable', $item)) {
                $actions->iframe('已禁用', route_url('weiran-mgr-page:backend.pam.enable', [$item->id]))->icon('slash-circle')->danger()
                    ->tooltip('当前禁用, 点击启用');
            }
        },])->fixed()->width(165);
    }

    /**
     * @inheritDoc
     * @return Closure
     */
    public function filter(): Closure
    {
        return function (Filter $filter) {
            $type  = input(Scope::QUERY_NAME, PamAccount::TYPE_BACKEND);
            $roles = PamRole::getLinear($type);
            $filter->column(1 / 12, function (Filter $ft) {
                $ft->where(function ($query) {
                    $passport = input('passport');
                    $type     = PamAccount::passportType($passport);
                    if ($type === PamAccount::REG_TYPE_MOBILE && !Str::contains($passport, '-')) {
                        $prefix   = $type === PamAccount::TYPE_USER ? '86-' : PamAccount::BACKEND_MOBILE_PREFIX;
                        $passport = $prefix . $passport;
                    }
                    $query->where($type, $passport);
                }, '手机/用户名/邮箱', 'passport');
            });
            $filter->column(1 / 12, function (Filter $column) use ($roles) {
                $column->where(function ($query) {
                    $roleId      = input('role_id');
                    $account_ids = PamRoleAccount::where('role_id', $roleId)->limit(200)->pluck('account_id');
                    $query->whereIn('id', $account_ids);
                }, '用户角色', 'role_id')->select($roles);
            });
            $types = PamAccount::kvType();
            foreach ($types as $t => $v) {
                $filter->scope($t, $v)->where('type', $t);
            }
        };
    }

    public function quickButtons(): Closure
    {
        $scope = input(Scope::QUERY_NAME);
        return function (Operations $operations) use ($scope) {
            $operations->page('封禁管理', route_url('weiran-mgr-page:backend.ban.index', null, [Scope::QUERY_NAME => $scope]))
                ->icon('slash-circle')->sm();
            $operations->page('登录凭证', route('weiran-mgr-page:backend.pam.token'))->icon('person-badge')
                ->tooltip('登录用户管理, 开启单点登录可用')->sm();
            $operations->create(route_url('weiran-mgr-page:backend.pam.establish', null, ['type' => $scope]), '新增账号');
        };
    }
}
