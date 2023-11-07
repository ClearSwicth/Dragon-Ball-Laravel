<?php
/**
 * AbstractMode.php
 * 文件描述
 * Created on 2023/11/1 11:21
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Models;

use ClearSwitch\DragonBallLaravel\Traits\Model\ModelCreating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class AbstractMode extends Model
{
    use ModelCreating;

    /**
     * 不需要模型帮我维护created_at 和 updated_at
     * @var bool
     */
    public $timestamps = false;

    /**
     * 指明模型的 ID 不是自增
     * @var bool
     */
    public $incrementing = false;


    /**
     * 设置表名都是按照模型名字来的，并设置成蛇形格式（xx_xx）
     * @return string
     * @author clearSwitch
     */
    public function getTable()
    {
        //为了之前的前缀只能在这个地方拼写，不能在配置那配置前缀
        return $this->table ? $this->table : Config('database.mysql.prefix') . Str::Snake(class_basename($this));
    }

    /**
     * 获取主键或生成主键
     * @return int
     * @author Verdient。
     */
    public function getKeyOrGenerate()
    {
        if (!$this->getKey()) {
            $this->{$this->getKeyName()} = $this->generateKey();
        }
        return $this->getKey();
    }

    /**
     * 调用自己的build 类，重写框架中的方法
     * @param $query
     * @return Builder
     * @author SwitchSwitch
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * 重写框架中的query
     * @param bool $cache
     * @return Builder
     * @author clearSwitch
     */
    public static function query($cache = false)
    {
        return parent::query($cache);
    }

    /**
     * 添加数据之前的处理
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return bool
     * @author clearSwitch
     */
    protected function performInsert(\Illuminate\Database\Eloquent\Builder $query)
    {
        $attributes = $this->attributes;
        $result = parent::performInsert($query);
        foreach ($attributes as $name => $value) {
            if (!array_key_exists($name, $this->attributes)) {
                $this->attributes[$name] = $value;
            }
        }
        return $result;
    }

    /**
     * 生成主键
     * @return int
     * @author clearSwitch。
     */
    public static function generateKey()
    {
        return App('snowflake')->id();
    }

    /**
     * 允许批量赋值
     * @var array
     */
    protected $guarded = [];


    public function getAttributes()
    {
        $this->mergeAttributesFromCachedCasts();
        return $this->attributes;
    }
}
