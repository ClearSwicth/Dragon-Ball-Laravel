<?php
/**
 * CacheServiceProvider.php
 * 缓存
 * Created on 2023/11/4 12:03
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Providers;

use ClearSwitch\DragonBallLaravel\Cache\TokenFileStore;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class CacheServiceProvider extends AbstractProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->booting(function () {
            Cache::extend('token_file', function (Application $app) {
                return Cache::repository(new TokenFileStore(App::make(Filesystem::class),$app['config']['cache']['stores']['token_file']['path']));
            });
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
