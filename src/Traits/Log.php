<?php
/**
 * Log.php
 * 日志
 * Created on 2023/11/2 10:21
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Traits;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

trait Log
{
    public function log($fileName = null)
    {
        $log = new Logger('log');
        if (!$fileName) {
            $fileName = storage_path($this->getPath());
        }
        $dateFormat = "Y-m-d H:i:s";
        $output = "[%datetime%] %level_name%:%message% %context% %extra%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        $stream = new StreamHandler($fileName, 100);
        $stream->setFormatter($formatter);
        $log->pushHandler($stream);
        return $log;
    }

    /**
     * 获得日志的位置
     * @return string
     * @author SwitchSwitch
     */
    public function getPath()
    {
        $className = static::class;
        if (substr($className, -10) == 'Controllers') {
            return "logs/Controllers/" . date("Y-m-d") . ".log";
        }
        $explodeName = explode('\\', $className);
        if (is_array($explodeName) && in_array('Jobs', $explodeName)) {
            return "logs/Jobs/" . end($explodeName) . "/" . date("Y-m-d") . ".log";
        }
        if (is_array($explodeName) && in_array('Crontab', $explodeName)) {
            return "logs/Crontab/" . end($explodeName) . "/" . date("Y-m-d") . ".log";
        }
        return "logs/error.log";
    }
}
