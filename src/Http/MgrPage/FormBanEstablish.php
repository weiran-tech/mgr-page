<?php

namespace Weiran\MgrPage\Http\MgrPage;

use Weiran\Framework\Classes\Resp;
use Weiran\Framework\Classes\Traits\AppTrait;
use Weiran\Framework\Exceptions\ApplicationException;
use Weiran\Framework\Validation\Rule;
use Weiran\MgrPage\Classes\Widgets\FormWidget;
use Weiran\System\Action\Ban;
use Weiran\System\Classes\Traits\PamTrait;
use Weiran\System\Models\PamBan;

class FormBanEstablish extends FormWidget
{

    use PamTrait, AppTrait;

    public bool $ajax = true;


    private $id;

    /**
     * @var PamBan
     */
    private $item;

    /**
     * 账号类型
     * @var string
     */
    private $accountType;

    public function setAccountType($type)
    {
        $this->accountType = $type;
    }

    /**
     * 设置id
     * @param $id
     * @return $this
     * @throws ApplicationException
     */
    public function setId($id)
    {
        $this->id = $id;
        if ($id) {
            $this->item = PamBan::find($id);

            if (!$this->item) {
                throw new ApplicationException('无设备信息');
            }
        }
        return $this;
    }

    public function handle()
    {
        $Ban = new Ban();
        if (!$Ban->establish(input())) {
            return Resp::error($Ban->getError());
        }

        return Resp::success('操作成功', '_top_reload|1');
    }

    public function data(): array
    {
        $data = [];
        if ($this->id) {
            return [
                'id'           => $this->item->id,
                'account_type' => $this->item->account_type,
                'type'         => $this->item->type,
                'value'        => $this->item->value,
            ];
        }

        return array_merge($data, [
            'account_type' => $this->accountType,
        ]);
    }

    public function form(): void
    {
        if ($this->id) {
            $this->hidden('id', '设备id');
        }
        $this->hidden('account_type', '账号类型');
        $this->select('type', '类型')->options(PamBan::kvType());
        $this->text('value', '限制值')->rules([
            Rule::nullable(),
        ])->help('如果是Ip支持如下几种格式 : <br> 固定IP(192.168.1.1) ; IP段 : (192.168.1.1-192.168.1.21); <br> IP 掩码(192.168.1.1/24); IP 通配符(192.168.1.*)');
        $this->text('note', '备注');
    }
}
