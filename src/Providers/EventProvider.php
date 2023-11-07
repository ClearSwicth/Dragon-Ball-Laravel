<?php
/**
 * EventProvider.php
 * 文件描述
 * Created on 2023/11/2 15:54
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Providers;

use ClearSwitch\DragonBallLaravel\Events\ErrorWarnEvent;
use ClearSwitch\DragonBallLaravel\Listeners\ErrorWarnListener;
use ClearSwitch\DragonBallLaravel\Listeners\QueryListener;
use Illuminate\Database\Events\QueryExecuted;

class EventProvider extends AbstractEventProvider
{
    protected $listen = [
        QueryExecuted::class => [
            QueryListener::class,
        ],
        //错误事件
        ErrorWarnEvent::class => [
            ErrorWarnListener::class
        ],
    ];
}
