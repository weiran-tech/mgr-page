<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes;

use Overtrue\Pinyin\Pinyin;

/**
 * 搜索缓存
 */
class SearchCache
{
    /**
     * @param string $text
     * @return string
     */
    public static function py(string $text): string
    {
        static $pinyin;
        $Rds = sys_tag('py-mgr-page');
        if (class_exists(Pinyin::class)) {
            if ($py = $Rds->hget(PyMgrPageDef::ckSearchPy(), $text)) {
                return $py;
            }
            if (!$pinyin) {
                $pinyin = new Pinyin();
            }
            /** @var  $pinYin */
            $py = $pinyin->abbr($text)->join('');
            $Rds->hset(PyMgrPageDef::ckSearchPy(), $text, $py);
            return $py;
        }
        return '';
    }
}
