<?php
/**
 * DragonBallManagerProvider.php
 * 包对外提供的服务提供者
 * Created on 2023/11/2 10:09
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Providers;

class DragonBallManagerProvider extends AbstractProvider
{
    public function register()
    {
        $this->app->register(SnowflakeProvider::class);
        $this->app->register(ValidatorProvider::class);
        $this->app->register(EventProvider::class);
        $this->app->register(AuthProvider::class);
        $this->app->register(CacheServiceProvider::class);
    }
}
