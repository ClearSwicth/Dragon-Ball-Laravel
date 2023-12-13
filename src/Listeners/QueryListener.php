<?php
/**
 * QueryListener.php
 * 控制台的打印sql 语句
 * Created on 2023/11/2 15:48
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class QueryListener
{
    /**
     * @param QueryExecuted $event
     * @return void
     * @author SwitchSwitch
     */
    public function handle(QueryExecuted $event)
    {
        if (Config('dragonBallLaravel.print_sql')) {
            $sql = $event->sql;
            if (!Arr::isAssoc($event->bindings)) {
                $placeholder = md5(random_bytes(64));
                foreach ($event->bindings as $value) {
                    if (is_null($value)) {
                        $value = 'null';
                    } else if (is_int($value) || is_float($value)) {
                        $value = (string) $value;
                    } else {
                        $value = "'" . str_replace('?', $placeholder, $value) . "'";
                    }
                    $sql = Str::replaceFirst('?', $value, $sql);
                }
                $sql = str_replace($placeholder, '?', $sql);
            }
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
