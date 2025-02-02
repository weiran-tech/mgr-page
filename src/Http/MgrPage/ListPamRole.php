<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\MgrPage;

use Auth;
use Closure;
use Weiran\Framework\Exceptions\ApplicationException;
use Weiran\MgrPage\Classes\Grid\Column;
use Weiran\MgrPage\Classes\Grid\Displayer\Actions;
use Weiran\MgrPage\Classes\Grid\Filter;
use Weiran\MgrPage\Classes\Grid\ListBase;
use Weiran\MgrPage\Classes\Operations;
use Weiran\System\Models\PamAccount;
use Weiran\System\Models\PamRole;

class ListPamRole extends ListBase
{

    public string $title = '角色管理';

    /**
     * @inheritDoc
     * @throws ApplicationException
     */
    public function columns(): void
    {
        /** @var PamAccount $user */
        $user = Auth::user();

        $this->column('id', 'ID')->sortable()->width(80);
        $this->column('title', '名称')->width(200, true);
        $this->addColumn(Column::NAME_ACTION, '操作')->displayUsing(Actions::class, [function (Actions $actions) use ($user) {
            /** @var PamRole $item */
            $item = $actions->row;
            if ($user->can('permission', $item)) {
                $actions->iframe('权限', route('py-mgr-page:backend.role.menu', [$item->id]))->icon('x-diamond')
                    ->widthLarge()->height(660)
                    ->tooltip("编辑 [{$item->title}] 权限")->primary();
            }
            if ($user->can('edit', $item)) {
                $actions->edit(route('py-mgr-page:backend.role.establish', [$item->id]));
            }
            if ($user->can('delete', $item)) {
                $actions->delete(route('py-mgr-page:backend.role.delete', [$item->id]), "角色{$item->title}");
            }
        },])->fixed()->width(210);
    }

    /**
     * @inheritDoc
     * @return Closure
     */
    public function filter(): Closure
    {
        return function (Filter $filter) {
            $types = PamAccount::kvType();
            foreach ($types as $t => $v) {
                $filter->scope($t, $v)->where('type', $t);
            }
        };
    }


    public function quickButtons(): Closure
    {
        $user = Auth::user();
        return function (Operations $operations) use ($user) {
            if ($user->can('create', PamRole::class)) {
                $operations->create(route('py-mgr-page:backend.role.establish'));
            }
        };
    }
}
