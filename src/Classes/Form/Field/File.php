<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Form\Field;

use Weiran\MgrPage\Classes\Form\Field;
use Weiran\System\Models\PamAccount;

class File extends Field
{
    public function image(): self
    {
        $this->options['type'] = 'images';
        return $this;
    }

    public function file(): self
    {
        $this->options['type'] = 'file';
        return $this;
    }

    public function audio(): self
    {
        $this->options['type'] = 'audio';
        return $this;
    }

    public function video(): self
    {
        $this->options['type'] = 'video';
        return $this;
    }

    /**
     * 自定义扩展
     * @param array $exts
     * @return File
     */
    public function exts(array $exts = []): self
    {
        $this->options['exts'] = $exts;
        return $this;
    }

    /**
     * 设置独立的Pam
     * @param PamAccount $pam
     * @return $this
     */
    public function pam(PamAccount $pam): self
    {
        $this->options['pam'] = $pam;
        return $this;
    }
}
