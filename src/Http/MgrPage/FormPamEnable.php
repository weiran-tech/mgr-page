<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\MgrPage;

use Weiran\Framework\Classes\Resp;
use Weiran\MgrPage\Classes\Widgets\FormWidget;
use Weiran\System\Action\Pam;
use Weiran\System\Models\PamAccount;
use Route;

class FormPamEnable extends FormWidget
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
        $Pam    = (new Pam())->setPam(request()->user());
        $reason = input('reason', '');
        if (!$Pam->enable($this->pam->id, $reason)) {
            return Resp::error($Pam->getError());
        }

        return Resp::success('当前用户启用', '_top_reload|1');

    }

    public function data(): array
    {
        return [
            'id'   => $this->pam->id,
            'date' => $this->pam->disable_end_at,
        ];
    }

    /**
     * Build a form here.
     */
    public function form(): void
    {
        $this->datetime('date', '解禁日期')->disabled();
        $this->textarea('reason', '解禁原因');
    }
}
