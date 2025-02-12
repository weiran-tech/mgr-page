<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\MgrPage;

use Illuminate\Http\Request;
use Weiran\Framework\Classes\Resp;
use Weiran\Framework\Classes\Traits\AppTrait;
use Weiran\Framework\Validation\Rule;
use Weiran\MgrPage\Classes\Widgets\FormWidget;
use Weiran\System\Action\Pam;
use Weiran\System\Classes\Contracts\PasswordContract;
use Weiran\System\Classes\Traits\PamTrait;
use Weiran\System\Http\Validation\PamConfirmedPasswordRequest;
use Weiran\System\Models\PamAccount;

class FormPassword extends FormWidget
{

    use PamTrait, AppTrait;

    public bool $ajax = true;

    protected string $title = '修改密码';

    public function handle(Request $request)
    {

        $old_password = input('old_password');
        $id           = input('account_id');

        $Pam       = new Pam();
        $this->pam = PamAccount::find($id);
        if (!app(PasswordContract::class)->check($this->pam, $old_password)) {
            return Resp::error('原密码错误!');
        }

        if (sys_is_demo()) {
            return Resp::error('演示模式下无法修改密码');
        }
        /** @var PamConfirmedPasswordRequest $reqPwd */
        $reqPwd = app(PamConfirmedPasswordRequest::class, [$request]);
        if (!$Pam->setPassword($this->pam, $reqPwd['password'])) {
            return Resp::error($Pam->getError());
        }
        app('auth')->guard(PamAccount::GUARD_BACKEND)->logout();

        return Resp::success('密码修改成功, 请重新登录', '_location|' . route('wr-mgr-page:backend.home.login'));

    }

    public function data(): array
    {
        return [
            'account_id' => data_get($this->pam, 'id'),
        ];
    }

    /**
     * Build a form here.
     */
    public function form(): void
    {
        $this->hidden('account_id', 'account_id');
        $this->password('old_password', '原密码')->rules([
            Rule::required(),
        ]);
        $this->password('password', '密码')->rules([
            Rule::required(),
            Rule::confirmed(),
        ]);
        $this->password('password_confirmation', '重复密码')->rules([
            Rule::required(),
        ]);;
    }
}
