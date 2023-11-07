<?php
/**
 * Time.php
 * 文件描述
 * Created on 2023/11/2 10:23
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Utils;

class Time
{
    /**
     * 获得毫秒的时间
     * @return string
     * @author SwitchSwitch
     */
    public static function getMillisecond()
    {
        $time = explode(" ", microtime());
        $time = $time [1] . ($time [0] * 1000);
        $time2 = explode(".", $time);
        $time = $time2 [0];
        if (strlen($time) == 12) {
            $time = $time . "0";
        }
        return $time;
    }

    /**
     * 获得相差的小时数
     * @param $beforeTime
     * @param $lastTime
     * @return int
     * @throws \Exception
     * @author SwitchSwitch
     */
    public static function diffHour($beforeTime, $lastTime)
    {
        $beforeTime = self::checkTimeFormat($beforeTime);
        $lastTime = self::checkTimeFormat($lastTime);
        $before = new \DateTime($beforeTime);
        $last = new \DateTime($lastTime);
        $interval = $before->diff($last);
        //这个是时间戳格式的
        /*$datetime1 = new DateTime();
        $datetime1->setTimestamp($timestamp1);
        $interval = $datetime1->diff($datetime2);*/
        return $interval->h;
    }

    /**
     * 获得相差的天数
     * @param $beforeTime
     * @param $lastTime
     * @return false|int
     * @throws \Exception
     * @author SwitchSwitch
     */
    public static function diffDay($beforeTime, $lastTime)
    {
        $beforeTime = self::checkTimeFormat($beforeTime);
        $lastTime = self::checkTimeFormat($lastTime);
        $before = new \DateTime($beforeTime);
        $last = new \DateTime($lastTime);
        $interval = $before->diff($last);
        return $interval->days;
    }

    /**
     * 获得相差星期
     * @param $beforeTime
     * @param $lastTime
     * @return float
     * @throws \Exception
     * @author SwitchSwitch
     */
    public static function diffWeek($beforeTime, $lastTime)
    {
        $day = self::diffDay($beforeTime, $lastTime);
        return floor($day / 7);

    }

    /**
     * 获得相差的月份
     * @param $beforeTime
     * @param $lastTime
     * @return int
     * @throws \Exception
     * @author SwitchSwitch
     */
    public static function diffMonth($beforeTime, $lastTime)
    {
        $beforeTime = self::checkTimeFormat($beforeTime);
        $lastTime = self::checkTimeFormat($lastTime);
        $before = new \DateTime($beforeTime);
        $last = new \DateTime($lastTime);
        $interval = $before->diff($last);
        return $interval->m;
    }

    /**
     * 获得相差的年数
     * @param $beforeTime
     * @param $lastTime
     * @return int
     * @throws \Exception
     * @author SwitchSwitch
     */
    public static function diffYear($beforeTime, $lastTime)
    {
        $beforeTime = self::checkTimeFormat($beforeTime);
        $lastTime = self::checkTimeFormat($lastTime);
        $before = new \DateTime($beforeTime);
        $last = new \DateTime($lastTime);
        $interval = $before->diff($last);
        return $interval->y;
    }

    /**
     * 验证时间并返回 Y-m-d H:i:s
     * @param $time
     * @return mixed|string
     * @throws \ErrorException
     * @author SwitchSwitch
     */
    public static function checkTimeFormat($time)
    {
        if (is_numeric($time) && (int)$time == $time) {
            return date("Y-m-d H:i:s");
        }
        if (strtotime($time)) {
            return $time;
        }
        throw  new \ErrorException("时间只能是时间戳或者Y-m-d H:i:s 格式");
    }
}
