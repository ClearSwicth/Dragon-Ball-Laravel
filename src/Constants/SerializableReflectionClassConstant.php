<?php
/**
 * SerializableReflectionClassConstant.php
 * 文件描述
 * Created on 2023/11/13 15:53
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Constants;

class SerializableReflectionClassConstant implements \Serializable
{

    private $constants;

    public function __construct(array $constant)
    {
        $this->constants = $constant;
    }

    /**
     * 序列化
     * @return string|null
     * @author SwitchSwitch
     */
    public function serialize()
    {
        $serializedConstants = [];
        foreach ($this->constants as $constant) {
            $serializedConstants[] = [
                'name' => $constant->getName(),
                'class' => $constant->getDeclaringClass()->getName(),
            ];
        }
        return serialize($serializedConstants);
    }

    /**
     * 反序列化
     * @param $data
     * @return void
     * @throws \ReflectionException
     * @author SwitchSwitch
     */
    public function unserialize($data): void
    {
        $serializedConstants = unserialize($data);
        $this->constants = [];
        foreach ($serializedConstants as $constantData) {
            $reflectionClass = new \ReflectionClass($constantData['class']);
            $this->constants[] = $reflectionClass->getReflectionConstant($constantData['name']);
        }
    }

    public function getConstants()
    {
        return $this->constants;
    }
}