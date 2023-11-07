<?php
/**
 * Robot.php
 * 机器人发送消息
 * Created on 2023/11/2 16:13
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Traits;

use ClearSwitch\DragonBallLaravel\Utils\Ioc;

trait Robot
{
    /**
     * 企业微信
     * @return void
     * @author SwitchSwitch
     */
    public function qyWeChat($message)
    {
        Ioc::make('ClearSwitch\DragonBallLaravel\Service\SendMessage\QyWeChat\QyWeChatSend')->sendMessage($message);
    }

    /**
     * 邮件
     * @param $title
     * @param $content
     * @param array $cc_list
     * @return void
     * @author SwitchSwitch
     */
    public function EmailSend($title, $content, array $cc_list)
    {
        Ioc::make('ClearSwitch\DragonBallLaravel\Service\SendMessage\Mail\EmailSend')->sendMessage($title, $content, $this->cc_list);
    }
}
