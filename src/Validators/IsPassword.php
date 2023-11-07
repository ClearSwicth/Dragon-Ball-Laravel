<?php

namespace ClearSwitch\DragonBallLaravel\Validators;

/**
 * 是否是密码
 * Created on 2023/11/2 11:05
 * Creat by ClearSwitch
 */
class IsPassword
{
    public function isPassword($attribute, $value, $parameters, $validator)
    {
        // 6-16个字符，数字+字母组合
        // return preg_match('#^(?![0-9]+$)(?![a-z]+$)[[:alnum:]]{6,16}$#i', $value);

        // 6-16个字符，数字+字母+特殊字符两者组合
        return preg_match('/^(?![a-zA-Z]+$)(?![0-9]+$)(?!((?=[\x21-\x7e]+)[^A-Za-z0-9])+$).{6,16}$/', $value);
    }
}
