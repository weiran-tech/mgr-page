<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes;


class PyMgrPageDef
{
    /**
     * 拼音的缓存KEY
     * @return string
     */
    public static function ckSearchPy(): string
    {
        return 'search-pinyin';
    }
}