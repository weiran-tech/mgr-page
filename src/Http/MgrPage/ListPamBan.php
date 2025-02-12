<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\MgrPage;

use Closure;
use Weiran\Framework\Exceptions\ApplicationException;
use Weiran\MgrPage\Classes\Grid\Column;
use Weiran\MgrPage\Classes\Grid\Displayer\Actions;
use Weiran\MgrPage\Classes\Grid\Filter;
use Weiran\MgrPage\Classes\Grid\Filter\Scope;
use Weiran\MgrPage\Classes\Grid\ListBase;
use Weiran\MgrPage\Classes\Operations;
use Weiran\System\Models\PamAccount;
use Weiran\System\Models\PamBan;
use Weiran\System\Models\SysConfig;

class ListPamBan extends ListBase
{

    public string $title = '风险拦截';

    /**
     * @inheritDoc
     * @throws ApplicationException
     */
    public function columns()
    {
        $this->column('id', "ID")->sortable()->width(80);
        $this->column('type', "类型")->display(function ($type) {
            return PamBan::kvType($type);
        });
        $this->column('value', "限制值");
        $this->column('note', "备注");
        $this->addColumn(Column::NAME_ACTION, '操作')->displayUsing(Actions::class, [function (Actions $actions) {
            /** @var PamBan $item */
            $item = $actions->row;
            $actions->delete(route('wr-mgr-page:backend.ban.delete', [$item->id]), $item->type . $item->value);
        },])->fixed()->width(70);
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
                $filter->scope($t, $v)->where('account_type', $t);
            }
        };
    }


    public function quickButtons(): Closure
    {
        $type = input(Scope::QUERY_NAME, PamAccount::TYPE_USER);
        return function (Operations $operations) use ($type) {
            $status = sys_setting('wr-system::ban.status-' . $type, SysConfig::STR_NO);
            $url    = route_url('wr-mgr-page:backend.ban.status', null, ['type' => $type,]);
            if ($status === 'Y') {
                $operations->disable($url, '风险拦截');
            }
            else {
                $operations->enable($url, '风险拦截');
            }

            $isBlack = sys_setting('wr-system::ban.type-' . $type, PamBan::WB_TYPE_BLACK) === PamBan::WB_TYPE_BLACK;
            $url     = route_url('wr-mgr-page:backend.ban.type', null, ['type' => $type,]);
            if ($isBlack) {
                $operations->request('黑名单模式', $url)->icon('pause-circle')->tooltip('当前黑名单, 点击切换到白名单')->sm()
                    ->confirm('当前黑名单, 是否切换到白名单?')->danger();
            }
            else {
                $operations->request('白名单模式', $url)->icon('play-circle')->tooltip('当前白名单, 点击切换到黑名单')->sm()
                    ->confirm('当前白名单, 是否切换到黑名单?');
            }
            $operations->create(route_url('wr-mgr-page:backend.ban.establish', null, ['type' => $type,]), '新增');
            $operations->setting(route_url('wr-mgr-page:backend.ban.setting', null, ['type' => $type,]));
        };
    }
}
