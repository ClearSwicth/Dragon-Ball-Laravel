<?php

namespace ClearSwitch\DragonBallLaravel\Validators;

/**
 * 验证是否是英文
 * Created on 2023/11/2 11:09
 * Creat by ClearSwitch
 */
class IsEnglish
{
    public static function isEnglish($attribute, $value, $parameters, $validator)
    {
        return preg_match("/^[^\x80-\xff]+$/", $value);
    }
}
