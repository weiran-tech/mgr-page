<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\MgrPage;

use Weiran\Framework\Classes\Resp;
use Weiran\Framework\Validation\Rule;
use Weiran\MgrPage\Classes\Widgets\FormWidget;
use Weiran\System\Action\Pam;
use Weiran\System\Models\PamAccount;
use Route;

class FormPamNote extends FormWidget
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
        $note = input('note');
        $Pam  = new Pam();
        $Pam->setNote($this->pam, $note);
        return Resp::success('设置成功', '_top_reload|1');
    }

    public function data(): array
    {
        return [
            'username' => $this->pam->username,
            'note'     => $this->pam->note,
        ];
    }

    /**
     * Build a form here.
     */
    public function form(): void
    {
        $this->text('username', '用户名')->readonly();
        $this->textarea('note', '姓名')->rules([
            Rule::string(),
            Rule::max(30),
        ]);
    }
}
