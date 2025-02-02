<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\Request\Develop;

use DB;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Weiran\Core\Classes\Traits\CoreTrait;

// todo 赵殿有

/**
 * 环境检测工具
 */
class EnvController extends DevelopController
{
    use CoreTrait;

    /**
     * php info
     * @return Factory|View
     */
    public function phpinfo(): Factory|View
    {
        return view('py-mgr-page::develop.env.phpinfo');
    }

    /**
     * 检查数据库设计
     * @url http://blog.csdn.net/zhezhebie/article/details/78589812
     */
    public function db()
    {
        $tables = DB::select('show tables');
        $tables = array_map(function ($item) {
            return reset($item);
        }, $tables);

        $suggestString   = function ($col) {
            if (str_contains($col['Type'], 'char')) {
                if ($col['Null'] === 'YES') {
                    return '(Char-null)';
                }
                if (!is_null($col['Default']) && $col['Default'] !== '') {
                    if (!is_string($col['Default'])) {
                        return '(Char-default)';
                    }
                }
            }

            return '';
        };
        $suggestInt      = function ($col) {
            if ($col['Key'] !== 'PRI' && str_contains($col['Type'], 'int')) {
                if (!is_numeric($col['Default'])) {
                    return '(Int-default)';
                }
                if ($col['Null'] === 'YES') {
                    return '(Int-Null)';
                }
            }

            return '';
        };
        $suggestDecimal  = function ($col) {
            if (str_contains($col['Type'], 'decimal')) {
                if ($col['Default'] !== '0.00') {
                    return '(Decimal-default)';
                }
                if ($col['Null'] === 'YES') {
                    return '(Decimal-Null)';
                }
            }

            return '';
        };
        $suggestDatetime = function ($col) {
            if (str_contains($col['Type'], 'datetime')) {
                if (!is_null($col['Default'])) {
                    return '(Datetime-default)';
                }
                if ($col['Null'] === 'NO') {
                    return '(Datetime-null)';
                }
            }

            return '';
        };
        $suggestFloat    = function ($col) {
            if (str_contains($col['Type'], 'float')) {
                return '(Float-set)';
            }

            return '';
        };

        $formatTables = [];
        foreach ($tables as $table) {
            $columns       = DB::select('show full columns from ' . $table);
            $formatColumns = [];
            /*
             * column 字段
             * Field      : account_no
             * Type       : varchar(100)
             * Collation  : utf8_general_ci
             * Null       : NO
             * Key        : ""
             * Default    : ""
             * Extra      : ""
             * Privileges : select,insert,update,references
             * Comment    : 账号
             * ---------------------------------------- */

            foreach ($columns as $column) {
                $column                          = (array) $column;
                $column['suggest']               =
                    $suggestString($column) .
                    $suggestInt($column) .
                    $suggestDecimal($column) .
                    $suggestDatetime($column) .
                    $suggestFloat($column);
                $formatColumns[$column['Field']] = $column;
            }
            $formatTables[$table] = $formatColumns;
        }

        return view('py-mgr-page::develop.env.db', [
            'items' => $formatTables,
        ]);
    }
}