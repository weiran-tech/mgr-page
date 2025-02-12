<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Hooks\MgrPage;

use Weiran\Core\Services\Contracts\ServiceHtml;

class HtmlJsVar implements ServiceHtml
{

    public function output(): string
    {
        $rules = preg_replace('/\s+/', ';', (string) sys_setting('wr-system::picture.preview_rule', ''));
        return <<<JS
window.POPPY.MGRPAGE = {
    'picturePreviewRule' : '{$rules}'
}
JS;

    }
}