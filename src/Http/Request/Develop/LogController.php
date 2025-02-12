<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\Request\Develop;

use File;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Weiran\Framework\Classes\Resp;
use Weiran\System\Classes\LogViewer;
use Request;
use Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * 显示日志
 */
class LogController extends DevelopController
{
    /**
     * 入口
     * @return Factory|RedirectResponse|View|BinaryFileResponse
     */
    public function index()
    {
        if (input('l')) {
            LogViewer::setFile(base64_decode(input('l')));
        }

        if (input('dl')) {
            return Response::download(storage_path() . '/logs/' . base64_decode(input('dl')));
        }

        if (Request::has('del')) {
            File::delete(storage_path() . '/logs/' . base64_decode(input('del')));

            return Resp::success('已删除', '_reload|1');
        }

        $logs = LogViewer::all();

        return view('weiran-mgr-page::develop.log.index', [
            'logs'         => $logs,
            'files'        => LogViewer::getFiles(true),
            'current_file' => LogViewer::getFileName(),
        ]);
    }
}