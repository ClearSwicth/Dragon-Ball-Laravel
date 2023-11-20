<?php
/**
 * AutoSetJsonFields.php
 * 自动设置表的json 字段
 * Created on 2023/11/20 11:27
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Traits\Model;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

trait AutoSetJsonFields
{
    public function setJsonFields()
    {
        $table = $this->getTable();
        $arrayColumn = Cache::remember($table, 60, function () use ($table) {
            $arrayColumn = [];
            $columns = Schema::getColumnListing($table);
            foreach ($columns as $column) {
                $type = Schema::getColumnType($table, $column);
                if ($type === 'json' || $type === 'jsonb') {
                    $arrayColumn[] = $column;
                }
            }
            return $arrayColumn;
        });
        foreach ($arrayColumn as $column) {
            if (array_key_exists($column, $this->attributes)) {
                $this->attributes[$column] = json_encode($this->attributes[$column]);
            }
            $this->casts[$column] = 'array';
        }
    }
}