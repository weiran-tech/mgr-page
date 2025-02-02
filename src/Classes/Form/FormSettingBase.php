<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Form;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Weiran\Core\Classes\Contracts\SettingContract;
use Weiran\Framework\Classes\Resp;
use Weiran\Framework\Classes\Traits\AppTrait;
use Weiran\Framework\Classes\Traits\KeyParserTrait;
use Weiran\MgrPage\Classes\Widgets\FormWidget;
use Weiran\System\Classes\Traits\PamTrait;
use Weiran\System\Exceptions\FormException;
use Weiran\System\Setting\Repository\SettingRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class FormSettingBase extends FormWidget
{
    use KeyParserTrait, PamTrait, AppTrait;

    /**
     * 是否设置用户
     * @var bool
     */
    public bool $ajax = true;

    /**
     * 是否 Inbox
     * @var bool
     */
    public bool $inbox = false;

    /**
     * 是否显示标题
     * @var string
     */
    protected string $title = '';

    /**
     * 是否包含框架内容
     * @var bool
     */
    protected $withContent = false;

    /**
     * 定义分组
     * @var string
     */
    protected $group = '';

    /**
     * @param Request $request
     * @return Response|JsonResponse|RedirectResponse
     */
    public function handle(Request $request)
    {
        /** @var SettingRepository $Setting */
        $Setting = app(SettingContract::class);
        $all     = $request->all();

        foreach ($this->fields as $field) {
            $key = $field->column();
            if (in_array($field->getType(), ['divider', 'html', 'link'], true)) {
                continue;
            }
            if (is_null($all[$key] ?? null)) {
                $value = $field->getType() === 'checkbox' ? [] : '';
            }
            else {
                $value = $all[$key];
            }
            $fullKey = $this->group . '.' . $key;
            if (!$Setting->set($fullKey, $value)) {
                return Resp::error($field->label() . '设置不符合规范:' . $Setting->getError()->getMessage());
            }
        }
        return Resp::success('更新配置成功');
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function data(): array
    {
        $Setting = app(SettingContract::class);
        $data    = [];
        foreach ($this->fields() as $field) {
            $key = $field->column();
            if (Str::startsWith($key, '_')) {
                continue;
            }
            $data[$key] = $Setting->get($this->group . '.' . $key);
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }
}
