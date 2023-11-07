<?php
/**
 * Ioc.php
 * 服务容器
 * Created on 2023/11/2 16:25
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Utils;


class Ioc
{
    /**
     * @var array
     */
    protected static $instances = [];

    public function __construct()
    {
    }


    /**
     * 获得实例
     * @param $abstract
     * @return mixed|object|null
     * @throws \ReflectionException
     * @author SwitchSwitch
     */
    public static function getInstances($abstract)
    {
        $reflector = new \ReflectionClass($abstract);
        $constructor = $reflector->getConstructor();
        if (!$constructor) {
            return new $abstract();
        }
        $dependencies = $constructor->getParameters();
        if (!$dependencies) {
            return new $abstract();
        }
        foreach ($dependencies as $dependency) {
//            if (!is_null($dependency->getClass())) {
//                $p[] = self::make($dependency->getClass()->name);
//            }
            $parameterType = $dependency->getType();
            if ($parameterType instanceof \ReflectionNamedType) {
                $p[] = self::make($parameterType->getName());
            }
        }
        //创建一个类的新实例,给出的参数将传递到类的构造函数
        return $reflector->newInstanceArgs($p);
    }

    /**
     * 解析依赖注入
     * @param string $className
     * @return mixed|object|null
     * @author SwitchSwitch
     */
    public static function make(string $className)
    {
        return static::getInstances($className);
    }
}
