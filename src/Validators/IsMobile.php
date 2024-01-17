<?php
/**
 * IsMobile.php
 * 文件描述
 * Created on 2023/11/2 11:04
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Validators;

class IsMobile
{
    /**
     * 是否是验证手机号
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return false|int
     * @author SwitchSwitch
     */
    public static function isMobile($attribute, $value, $parameters, $validator)
    {
        return preg_match('#^1\d{10}$#', $value);
    }
}
