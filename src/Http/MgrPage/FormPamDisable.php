<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\MgrPage;

use Weiran\Framework\Classes\Resp;
use Weiran\Framework\Validation\Rule;
use Weiran\MgrPage\Classes\Widgets\FormWidget;
use Weiran\System\Action\Pam;
use Weiran\System\Models\PamAccount;
use Route;

class FormPamDisable extends FormWidget
{
    public bool $ajax = true;

    /**
     * @var PamAccount
     */
    private $pam;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $id        = Route::input('id');
        $this->pam = PamAccount::findOrFail($id);
    }

    public function handle()
    {
        if (!$this->pam) {
            return Resp::error('您尚未选择用户!');
        }

        $date   = input('datetime', '');
        $reason = input('reason', '');
        $Pam    = (new Pam())->setPam(request()->user());
        if (!$Pam->disable($this->pam->id, $date, $reason)) {
            return Resp::error($Pam->getError());
        }

        return Resp::success('当前用户已封禁', '_top_reload|1');

    }

    public function data(): array
    {
        return [
            'id'       => $this->pam->id,
            'datetime' => $this->pam->disable_end_at,
            'reason'   => $this->pam->disable_reason,
        ];
    }

    /**
     * Build a form here.
     */
    public function form(): void
    {
        $this->datetime('datetime', '解禁时间')->rules([
            Rule::required(),
        ])->placeholder('选择解禁时间');
        $this->textarea('reason', '封禁原因');
    }
}
