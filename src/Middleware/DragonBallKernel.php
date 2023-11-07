<?php
/**
 * DragonBallKernel.php
 * 文件描述
 * Created on 2023/11/2 17:35
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Middleware;

class DragonBallKernel
{
    public static $middleware = [

    ];

    /**
     * 分组中间件
     * @var array[]
     */
    public static $middlewareGroups = [
        "api" => [
            ApiResponseMiddleware::class
        ]
    ];

    public static $routeMiddleware = [

    ];
}
