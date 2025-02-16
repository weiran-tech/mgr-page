<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Hooks\MgrPage;

use Weiran\Core\Services\Contracts\ServiceHtml;

class HtmlJsVar implements ServiceHtml
{

    public function output(): string
    {
        $rules = preg_replace('/\s+/', ';', (string) sys_setting('weiran-system::picture.preview_rule', ''));
        return <<<JS
window.WEIRAN.MGRPAGE = {
    'picturePreviewRule' : '{$rules}'
}
JS;

    }
}