<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Traits;

use Closure;
use Weiran\MgrPage\Classes\Operation\BatchIframeOperation;
use Weiran\MgrPage\Classes\Operation\BatchRequestOperation;
use Weiran\MgrPage\Classes\Operation\CopyOperation;
use Weiran\MgrPage\Classes\Operation\DropdownOperation;
use Weiran\MgrPage\Classes\Operation\IframeOperation;
use Weiran\MgrPage\Classes\Operation\LoadViewOperation;
use Weiran\MgrPage\Classes\Operation\PageOperation;
use Weiran\MgrPage\Classes\Operation\RequestOperation;
use Weiran\MgrPage\Classes\Operation\ToolbarOperation;

trait UseInteraction
{

    /**
     * 地址弹窗
     * @param string $title
     * @param string $url
     * @return IframeOperation
     */
    public function iframe(string $title, string $url): IframeOperation
    {
        $action = new IframeOperation($title, $url);
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }


    /**
     * 加载Tab
     * @param string $title
     * @param string $url
     * @return LoadViewOperation
     */
    public function loadView(string $title, string $url): LoadViewOperation
    {
        $action = new LoadViewOperation($title, $url);
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }


    /**
     * 复制
     * @param string $title
     * @param        $content
     * @return CopyOperation
     */
    public function copy(string $title, $content): CopyOperation
    {
        $action = new CopyOperation($title, $content);
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }

    /**
     * 请求
     * @param string $title
     * @param string $url
     * @return RequestOperation
     */
    public function request(string $title, string $url): RequestOperation
    {
        $action = new RequestOperation($title, $url);
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }

    /**
     * 请求
     * @param string $title
     * @param string $url
     * @return BatchRequestOperation
     * @deprecated
     * @see batchRequest
     */
    public function toolbar(string $title, string $url): BatchRequestOperation
    {
        return $this->batchRequest($title, $url);
    }

    /**
     * 请求
     * @param string $title
     * @param string $url
     * @return BatchRequestOperation
     */
    public function batchRequest(string $title, string $url): BatchRequestOperation
    {
        $action = new BatchRequestOperation($title, $url);
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }

    /**
     * 请求
     * @param string $title
     * @param string $url
     * @return BatchIframeOperation
     */
    public function batchIframe(string $title, string $url): BatchIframeOperation
    {
        $action = new BatchIframeOperation($title, $url);
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }

    /**
     * 页面
     * @param string $title
     * @param string $url
     * @return PageOperation
     */
    public function page(string $title, string $url): PageOperation
    {
        $action = (new PageOperation($title, $url));
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }

    /**
     * 下拉操作项
     * @param string  $title
     * @param Closure $callable
     * @return DropdownOperation
     */
    public function dropdown(string $title, Closure $callable): DropdownOperation
    {
        $action = (new DropdownOperation($title, ''));
        $action->operations($callable);
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }
}
