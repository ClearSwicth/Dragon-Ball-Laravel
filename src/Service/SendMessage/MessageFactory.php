<?php
/**
 * MessageFactory.php
 * 发送短信的接口类
 * Created on 2023/11/2 16:35
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Service\SendMessage;


interface  MessageFactory
{
    public function sendMessage(...$args);
}
