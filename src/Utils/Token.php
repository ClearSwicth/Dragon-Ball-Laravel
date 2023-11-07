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
        return base64_encode(openssl_encrypt(
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
            base64_decode($token),
            self::getMethod(),
            self::getKey(),
            self::$options,
            self::getVi()
        );
    }
}
