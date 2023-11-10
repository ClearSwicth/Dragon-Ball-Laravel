<?php
/**
 * Token.php
 *  token的解析和加密
 * Created on 2023/11/1 17:28
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Utils;

class Token
{
    /**
     * 加密的算法
     * @var
     */
    protected static $method;

    /**
     * 加密密钥
     * @var
     */
    protected static $key;

    /**
     * 可选参数，加密时的选项
     * @var
     */
    protected static $options;


    /**
     * 偏移量
     * @var
     */
    protected static $vi;

    /**
     * 获得密钥
     * @author clearSwitch
     */
    public static function getKey()
    {
        return self::$key = config('token.tokenKey');
    }

    public static function getVi()
    {
        return self::$vi = config('token.vi');
    }

    /**
     * 获得加密方式
     * @author clearSwitch
     */
    public static function getMethod()
    {
        return self::$key = "AES-256-CBC";
    }

    /**
     * 获得指定的加密解密选项
     * @return int
     * @author clearSwitch
     */
    public static function getOptions()
    {
        return self::$options = 0;
    }

    /**
     * 生成token
     * @param string $string
     * @return false|string
     * @author clearSwitch
     */
    public static function generate(string $string)
    {
        //openssl_random_pseudo_bytes(16)
        return self::bas64Encode(openssl_encrypt(
            $string,
            self::getMethod(),
            self::getKey(),
            self::getOptions(),
            self::getVi()
        ));
    }

    /**
     * 解密
     * @param $token
     * @return bool|false|string
     * @author clearSwitch
     */
    public static function parse($token)
    {
        if (empty($token)) {
            return false;
        }
        return openssl_decrypt(
            self::base64Decode($token),
            self::getMethod(),
            self::getKey(),
            self::$options,
            self::getVi()
        );
    }

    /**
     * 编码
     * @param $str
     * @return array|string|string[]
     * @author SwitchSwitch
     */
    public static function bas64Encode($str)
    {
        return str_replace('=', '', base64_encode($str));
    }

    /**
     * 解码
     * @param $str
     * @return false|string
     * @author SwitchSwitch
     */
    public static function base64Decode($str)
    {
        $strlength = mb_strlen($str);
        $remainder = $strlength % 4;
        if ($remainder === 0) {
            return base64_decode($str);
        } else {
            $equals = str_repeat('=', 4-$remainder);
            return base64_decode($str . $equals);
        }
    }
}
