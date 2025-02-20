<?php

namespace Weiran\MgrPage\Http\MgrPage;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Route;
use Weiran\Framework\Classes\Resp;
use Weiran\Framework\Validation\Rule;
use Weiran\MgrPage\Classes\Widgets\FormWidget;
use Weiran\MgrPage\Http\Request\Backend\Validation\RoleEstablishRequest;
use Weiran\System\Action\Role;
use Weiran\System\Models\PamAccount;
use Weiran\System\Models\PamRole;

class FormRoleEstablish extends FormWidget
{

    public bool $ajax = true;


    private int $id;

    /**
     * @var PamRole
     */
    private PamRole $item;


    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $id       = (int) Route::input('id');
        $this->id = $id;
        if ($id) {
            $this->item = PamRole::findOrFail($id);
        }
    }


    /**
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function handle($request)
    {
        $Role = (new Role());
        $Role->setPam(request()->user());
        $validated = app(RoleEstablishRequest::class, [$request])->validated();
        if ($Role->establish($validated, $this->id)) {
            return Resp::success('操作成功', '_top_reload|1;id|' . $Role->getRole()->id);
        }
        return Resp::error($Role->getError());
    }

    public function data(): array
    {
        if ($this->id) {
            return [
                'title' => $this->item->title,
                'name'  => $this->item->name,
                'type'  => $this->item->type,
            ];
        }
        return [];
    }

    public function form(): void
    {
        if ($this->id) {
            $this->select('type', '角色组')->options(PamAccount::kvType())->attribute([
                'lay-ignore',
            ])->disabled();
        }
        else {
            $this->select('type', '角色组')->options(PamAccount::kvType())->rules([
                Rule::required(),
            ])->attribute([
                'lay-ignore',
            ]);
        }
        $this->text('name', '标识')->help('角色标识在后台不进行显示, 如果需要进行项目内部约定');
        $this->text('title', '角色名称')->rules([
            Rule::required(),
        ])->help('显示的名称');
    }
}
