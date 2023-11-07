<?php
/**
 * Decimal.php
 * 文件描述
 * Created on 2023/11/2 11:06
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Validators;

/**
 * 验证小数
 * Created on 2023/11/2 11:07
 * Creat by ClearSwitch
 */
class Decimal
{
    public function decimal($attribute, $value, $parameters, $validator)
    {
        if (count($parameters)) {
            $number = $parameters[0];
            return preg_match("/^[0-9]+(.[0-9]{" . $number . "})$/", $value);
        } else {
            return preg_match("/^[0-9]$/", $value);
        }
    }
}
