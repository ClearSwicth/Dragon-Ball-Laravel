<?php
/**
 * QueryListener.php
 * 控制台的打印sql 语句
 * Created on 2023/11/2 15:48
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Listeners;

use Illuminate\Database\Events\QueryExecuted;

class QueryListener
{
    /**
     * @param QueryExecuted $event
     * @return void
     * @author SwitchSwitch
     */
    public function handle(QueryExecuted $event)
    {
        if (Config('database.print_sql')) {
            $s = str_replace('?', '%s', $event->sql);
            $bindings = array_map(function ($binding) {
                if (is_string($binding) || is_object($binding)) {
                    return "\"{$binding}\"";
                }
                return $binding;
            }, $event->bindings);
//            $sql = $this->addColor(sprintf($s, ...$bindings), "\033[32m");
//            $nowTime = $this->addColor('(' . date("Y-m-d H:i:s", time()) . ')', "\033[36m");
//            $connectionName = $this->addColor($event->connectionName, "\033[34m");
//            $time = $this->addColor("(time:$event->time" . "ms)", "\033[33m");
//            $str = $nowTime . $connectionName . $time . $sql . PHP_EOL;
            $sql = $this->addSpace(sprintf($s, ...$bindings));
            $nowTime = $this->addSpace('(' . date("Y-m-d H:i:s", time()) . ')');
            $connectionName = $this->addSpace($event->connectionName);
            $time = $this->addSpace("(time:$event->time" . "ms)");
            $str = $nowTime . $connectionName . $time . $sql . PHP_EOL;
            $handle = fopen("php://stdout", "w");
            fwrite($handle, $str);
            fclose($handle);
        }
    }

    /**
     *
     * @param $str
     * @param $color
     * @return string
     * @author SwitchSwitch
     */
    public function addColor($str, $color): string
    {
        $length = strlen($str);
        $str = str_pad($str, $length + 2, " ");
        return $color . $str . "\033[0m";
    }

    public function addSpace($str): string
    {
        $length = strlen($str);
        return str_pad($str, $length + 2, " ");
    }
}
