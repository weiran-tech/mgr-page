<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\MgrPage;

use Weiran\Framework\Classes\Resp;
use Weiran\Framework\Validation\Rule;
use Weiran\MgrPage\Classes\Widgets\FormWidget;
use Weiran\System\Classes\PySystemDef;
use Weiran\System\Classes\Traits\UserSettingTrait;
use Weiran\System\Models\PamAccount;
use Route;

class FormPamSetting extends FormWidget
{
    public bool $ajax = true;

    use UserSettingTrait;

    private PamAccount $pam;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $id        = Route::input('id');
        $this->pam = PamAccount::findOrFail($id);
    }


    public function handle()
    {
        $hour = round((float) input('expired_hour'), 1);
        $this->userSettingSet($this->pam->id, PySystemDef::uskAccount(), [
            'expired_hour' => $hour,
        ], [
            'expired_hour',
        ]);
        return Resp::success('已设置');

    }

    public function data(): array
    {
        $settings = $this->userSettingGet($this->pam->id, PySystemDef::uskAccount());
        return [
            'expired_hour' => $settings['expired_hour'] ?? '12',
        ];
    }

    /**
     * Build a form here.
     */
    public function form(): void
    {
        $this->text('expired_hour', '登录有效期')->rules([
            Rule::numeric(),
            Rule::between(3, 24),
        ])->help('登录有效期的时间为 3- 24 小时之间, 允许存在 1 位小数, 超过的小数位数将四舍五入, 默认的有效时间为(12 小时)');
    }
}
