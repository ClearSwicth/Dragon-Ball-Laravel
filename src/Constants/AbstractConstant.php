<?php
/**
 * AbstractConstant.php
 * 文件描述
 * Created on 2023/11/13 15:53
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Constants;

use Illuminate\Support\Facades\Cache;

/**
 * 常量
 * Created on 2023/11/13 11:32
 * Creat by ClearSwitch
 */
class AbstractConstant
{

    /**
     * 获得存储的缓存
     * @return false|mixed
     * @author SwitchSwitch
     */
    protected static function getConstant()
    {
        if (Config('dragonBallLaravel.has_constant_cache')) {
            if ($constants = Cache::get(basename(str_replace('\\', '/', static::class)))) {
                return unserialize($constants)->getConstants();
            } else {
                $reflectionClass = new \ReflectionClass(static::class);
                $constants = $reflectionClass->getReflectionConstants();
                Cache::put(basename(str_replace('\\', '/', static::class)), serialize(new SerializableReflectionClassConstant($constants)));
                return $constants;
            }
        } else {
            $reflectionClass = new \ReflectionClass(static::class);
            return $reflectionClass->getReflectionConstants();
        }
    }

    /**
     * 调用不存在的静态方法调用
     * @param string $name
     * @param array $arguments
     * @return false|mixed|void|null
     * @author SwitchSwitch
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (count($arguments) == 0) {
            throw new \Exception("缺少常量的值");
        }
        $subString = mb_substr($name, 0, 3);
        if ($subString == 'get') {
            $constName = mb_substr($name, 3);
            return self::getAnnotations($constName, current($arguments));
        }
    }

    /**
     * 或的注解的值
     * @param $constName
     * @param $constValue
     * @return false|mixed|null
     * @author SwitchSwitch
     */
    public static function getAnnotations($constName, $constValue)
    {
        $annotations = self::getConstantAnnotations($constName, $constValue);
        return $annotations;
    }

    /**
     * 解析注解
     * @param $class
     * @param $constName
     * @param $constValue
     * @return false|mixed|void
     * @throws \ReflectionException
     * @author SwitchSwitch
     */
    protected static function getConstantAnnotations($constName, $constValue)
    {
        $constants = self::getConstant();
        foreach ($constants as $constant) {
            if ($constant->getValue() == $constValue) {
                $docComment = $constant->getDocComment();
                $matches = [];
                preg_match_all('/' . $constName . '\("(.+)"\)/', $docComment, $matches);
                if (!empty(current(end($matches)))) {
                    return current(end($matches));
                } else {
                    throw new \Exception("调用了不存在的常量注解");
                }
            }
        }
        return false;
    }

    /**
     * 获得所有的常量
     * @return array
     * @throws \ReflectionException
     * @author SwitchSwitch
     */
    public static function list()
    {
        $constants = self::getConstant();
        $result = [];
        foreach ($constants as $value) {
            $result[] = $value->getValue();
        }
        return $result;
    }


    /**
     * 获得常量
     * @return array
     * @throws \ReflectionException
     * @author SwitchSwitch
     */
    public static function messages()
    {
        $constants = self::getConstant();
        foreach ($constants as $constant) {
            $docComment = $constant->getDocComment();
            $matches = [];
            preg_match_all('/Message\("(.+)"\)/', $docComment, $matches);
            $resule[] = [
                'code' => $constant->getValue(),
                'message' => end($matches)[0]
            ];
        }
        return $resule;
    }
}