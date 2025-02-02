<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Operation;

use Illuminate\Support\Str;

/**
 * 批量设置
 */
final class BatchIframeOperation extends Operation
{

    protected string $renderType = 'button';

    /**
     * 宽度
     * @var int
     */
    private int $width = 550;

    /**
     * 高度
     * @var int
     */
    private int $height = 550;

    /**
     * 预览
     * @param int $width 宽度
     * @return void
     */
    public function width(int $width = 550): self
    {
        $this->width = $width;
        return $this;
    }

    public function height(int $height = 550): self
    {
        $this->height = $height;
        return $this;
    }


    public function widthNormal(): self
    {
        return $this->width(700);
    }


    public function widthLarge(): self
    {
        return $this->width(850);
    }

    public function render(): string
    {
        $this->attributes['data-url']  = $this->url;
        $this->attributes['lay-event'] = Str::random(4);
        $this->classes[]               = 'J_iframe';
        if ($this->width) {
            $this->attributes['data-width'] = $this->width;
        }
        if ($this->height) {
            $this->attributes['data-height'] = $this->height;
        }
        return parent::render();
    }
}
