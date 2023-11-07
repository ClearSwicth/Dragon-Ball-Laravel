<?php
/**
 * IsEnglish.php
 * 文件描述
 * Created on 2023/11/2 11:08
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Validators;

/**
 * 验证是否是英文
 * Created on 2023/11/2 11:09
 * Creat by ClearSwitch
 */
class IsEnglish
{
    public function isEnglish($attribute, $value, $parameters, $validator)
    {
        return preg_match("/^[^\x80-\xff]+$/", $value);
    }
}
