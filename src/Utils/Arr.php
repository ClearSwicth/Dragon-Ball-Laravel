<?php
/**
 * Arr.php
 * 数组函数
 * Created on 2023/11/2 10:56
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Utils;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Arr
{
    /**
     * 以字段的值作为数组的索引
     * @param $array
     * @param $column
     * @return array
     * @author clearSwitch
     */
    public static function indexBy($array, $column)
    {
        if ($array instanceof Collection) {
            $array = $array->all();
        } else if ($array instanceof Arrayable) {
            $array = $array->toArray();
        }
        $result = [];
        foreach ($array as $value) {
            if (is_array($value)) {
                $key = $value[$column];
            } else if (is_object($value)) {
                $key = $value->$column;
            } else {
                throw new \Exception('The contents of the array must be an array or stdClass');
            }
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * 以字段的值作为数组的索引
     * @param $array
     * @param $column
     * @return array
     * @author clearSwitch
     */
    public static function indexByMulit($array, $column)
    {
        if ($array instanceof Collection) {
            $array = $array->all();
        } else if ($array instanceof Arrayable) {
            $array = $array->toArray();
        }
        $result = [];
        foreach ($array as $value) {
            if (is_array($value)) {
                $key = $value[$column];
            } else if (is_object($value)) {
                $key = $value->$column;
            } else {
                throw new \Exception('The contents of the array must be an array or stdClass');
            }
            if (!isset($result[$key])) {
                $result[$key] = [];
            }
            $result[$key][] = $value;
        }
        return $result;
    }

    /**
     * 修改数组键名
     * @param array $array 数组
     * @param string $from 要修改的键名
     * @param string $to 要修改为的键名
     * @return array
     * @author clearSwitch。
     */
    public static function changeKey($array, $from, $to)
    {
        $alias[$from] = $to;
        return array_combine(array_map(function ($key) use ($alias) {
            return $alias[$key] ?? $key;
        }, array_keys($array)), array_values($array));
    }
}
