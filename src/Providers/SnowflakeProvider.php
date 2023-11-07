<?php
/**
 * SnowflakeProvider.php
 * 文件描述
 * Created on 2023/11/1 19:01
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Providers;

use Godruoyi\Snowflake\LaravelSequenceResolver;
use Godruoyi\Snowflake\Snowflake;

class SnowflakeProvider extends AbstractProvider
{
    /**
     * @var int
     */
    public $datacenterId = 1;

    /**
     * 计算Id
     * @var int
     */
    public $workerId = 1;

    /**
     * 注册雪花算法服务
     * @author clearSwitch
     */
    public function register()
    {
        $this->app->singleton('snowflake', function () {
            return (new Snowflake($this->datacenterId, $this->workerId))
                ->setStartTimeStamp(strtotime("2019-10-10") * 1000)
                ->setSequenceResolver(new LaravelSequenceResolver($this->app->get('cache')->store()
                ));
        });
    }
}
