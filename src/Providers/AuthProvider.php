<?php
/**
 * AuthProvider.php
 * token认证的
 * Created on 2023/11/7 14:55
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Providers;


use ClearSwitch\DragonBallLaravel\AuthService\Guard\TokenGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class AuthProvider extends AbstractAuthProvider
{
    public function boot(): void
    {
        Auth::extend('token', function (Application $app, string $name, array $config) {
            return new TokenGuard(Auth::createUserProvider($config['provider']), $app['request']);
        });
    }
}
