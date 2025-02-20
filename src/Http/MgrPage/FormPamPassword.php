<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\MgrPage;

use Illuminate\Http\Request;
use Route;
use Weiran\Framework\Classes\Resp;
use Weiran\MgrPage\Classes\Widgets\FormWidget;
use Weiran\System\Action\Pam;
use Weiran\System\Http\Request\Web\Validation\AuthConfirmedPasswordRequest;
use Weiran\System\Models\PamAccount;

class FormPamPassword extends FormWidget
{
    public bool $ajax = true;

    /**
     * @var PamAccount
     */
    private $pam;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $id        = (int) Route::input('id');
        $this->pam = PamAccount::findOrFail($id);
    }

    public function handle(Request $request)
    {
        $Pam = new Pam();
        if (sys_is_demo()) {
            return Resp::error('演示模式下无法修改密码');
        }

        /** @var AuthConfirmedPasswordRequest $reqPwd */
        $reqPwd = app(AuthConfirmedPasswordRequest::class, [$request]);
        if ($Pam->setPassword($this->pam, $reqPwd['password'])) {
            return Resp::success('设置密码成功', '_top_reload|1');
        }

        return Resp::error($Pam->getError());
    }

    public function data(): array
    {
        return [
            'id'       => $this->pam->id,
            'username' => $this->pam->username,
        ];
    }

    /**
     * Build a form here.
     */
    public function form(): void
    {
        $this->text('username', '用户名')->disabled();
        $this->password('password', '密码');
        $this->password('password_confirmation', '重复密码');
    }
}
