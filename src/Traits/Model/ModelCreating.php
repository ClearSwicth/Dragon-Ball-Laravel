<?php

/**
 * 添加数据的时候，帮助没有写主键的数据增加主键值
 */

namespace ClearSwitch\DragonBallLaravel\Traits\Model;

trait ModelCreating
{
    /**
     * 这是模型的事件
     * @var string[]
     */
    protected static $recordEvents = [
        'creating',
        'created',
        'updating',
        'updated',
        'saving',
        'saved',
        'deleting',
        'deleted',
        'restoring',
        'restored'
    ];

    /**
     * 基础模型中有个bootTraits方法，这个方法就是自动执行所有的traits
     * boot 开头后面和方法名字一样
     * @author clearSwitch
     */
    public static function bootModelCreating()
    {
        foreach (static::getModelEvents() as $event) {
            //模型事件的执行 static::事件名字
            static::$event(function ($model) {
                $model->setLoad();
            });
        }
    }

    /**
     * @return string[]
     * @author clearSwitch
     */
    public static function getModelEvents()
    {
        return ['creating'];
    }

    /**
     * Date: 2022/4/22 下午7:15
     * 调用的方法必须是set开头
     * @author clearSwitch
     */
    public function setLoad()
    {
        //如果是自增长就不需要补充主机
        if (!$this->incrementing) {
            if (!$this->getKey()) {
                $this->{$this->getKeyName()} = App('snowflake')->id();
            }
        }
    }

    // 写法还有很多这样也还可以的
    /* protected static function booted(): void
     {
         static::created(function (User $user) {
             // ...
         });
     }*/
}
