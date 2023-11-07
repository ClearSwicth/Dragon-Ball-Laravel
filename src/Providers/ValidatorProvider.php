<?php
/**
 * ValidatorProvider.php
 * 自定义验证服务提供者
 * Created on 2023/11/2 14:23
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Providers;

use ClearSwitch\DragonBallLaravel\Validators\Decimal;
use ClearSwitch\DragonBallLaravel\Validators\IsEnglish;
use Illuminate\Support\Facades\Validator;

class ValidatorProvider extends AbstractProvider
{
    public function boot()
    {
        Validator::extend('decimal', [Decimal::class, 'decimal']);
        Validator::extend('english', [IsEnglish::class, 'isEnglish']);
        Validator::replacer('decimal', function ($message, $attribute, $rule, $parameters) {
            if (count($parameters)) {
                return str_replace([':values'], [$parameters[0] . "位小数"], $message);
            } else {
                return str_replace([':values'], ["整数"], $message);
            }
        });
    }
}
