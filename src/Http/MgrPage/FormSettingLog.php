<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\MgrPage;

use Weiran\MgrPage\Classes\Form\FormSettingBase;

class FormSettingLog extends FormSettingBase
{

    public const DAYS_FOREVER = 'forever';

    protected $withContent = true;

    protected string $title = '日志配置';

    protected $group = 'weiran-system::log';

    /**
     * Build a form here.
     */
    public function form(): void
    {
        $this->radio('days', '保存时间')->options([
            '60'               => '60天',
            '180'              => '180 天',
            '360'              => '360 天',
            self::DAYS_FOREVER => '永久',
        ])->default('180')->help('根据用户量和需求来设定, 太长时间数据量过大需要关注性能, 未设置默认为 180 天');
    }
}
