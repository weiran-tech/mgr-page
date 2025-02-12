<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\MgrPage;

use Weiran\Framework\Validation\Rule;
use Weiran\MgrPage\Classes\Form\FormSettingBase;
use Weiran\MgrPage\Classes\Operations;

class FormMailStore extends FormSettingBase
{

    public bool $inbox = true;

    protected $withContent = true;

    protected string $title = '邮件配置';

    protected $group = 'py-system::mail';

    /**
     * Build a form here.
     */
    public function form(): void
    {
        $this->boxTools(function (Operations $operations) {
            $operations->iframe('发送测试邮件', route('weiran-mgr-page:backend.mail.test'));
        });

        $this->radio('driver', '发送方式')->options([
            'mail' => '内置Mail函数',
            'smtp' => 'SMTP 服务器',
        ])->default('smtp');
        $this->radio('encryption', '加密方式')->options([
            'none' => '不加密',
            'ssl'  => 'SSL',
        ])->default('none');
        $this->number('port', '服务器端口')->rules([
            Rule::required(),
            Rule::integer(),
        ]);
        $this->text('host', '服务器地址')->rules([
            Rule::nullable(),
        ]);
        $this->email('from', '发邮箱地址');
        $this->text('username', '账号')->rules([
            Rule::nullable(),
        ]);
        $this->password('password', '密码')->help('如果重新保存, 必须要设置密码, 否则密码会被置空');
    }
}
