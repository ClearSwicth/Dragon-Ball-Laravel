<?php
/**
 * AbstractJob.php
 * amqp 对列
 * Created on 2023/11/2 14:36
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Jobs;

use ClearSwitch\DragonBallLaravel\Traits\Log;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class AbstractJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use Log;

    /**
     * 便于把实例添加到队列中
     * @author ClearSwitch
     */
    use Dispatchable;

    /**
     * 用于提供一些与队列交互的方法和属性
     * @author ClearSwitch
     */
    use InteractsWithQueue;

    /**
     * 通常用于将普通对象添加到队列中，而不是专门用于队列任务类。
     * 他和ShouldQueue 接口分开使用，虽然他们的执行都是一样的
     * 还有一点的区别就会ShouldQueue 接口的队列会自动添加到队列中，而
     * 使用Queueable特性的类需要对列负责类处理才会放到队列队列中
     * @author ClearSwitch
     */
    use Queueable;

    /**
     * 可以序列化传递模型
     * @author ClearSwitch
     */
    use SerializesModels;

    /**
     * 最大连接次数
     * @var int
     * @author ClearSwitch
     */
    public $tries = 3;

    /**
     * 超时事件
     * @var int
     * @author ClearSwitch
     */
    public $timeout = 100;

    /**
     * 载荷数据
     * @var
     */
    public $payloadData;

    /**
     * @author  ClearSwitch
     */
    public function __construct($payloadData = null)
    {
        $this->payloadData = $payloadData;
        $this->prepare();
    }
    /**
     * 队列执行逻辑
     * @return mixed
     * @author SwitchSwitch
     */
    // abstract public function handle();

    /**
     * 设置最大连接次数
     * @param int $trie
     * @return void
     * @author SwitchSwitch
     */
    public function setTries(int $trie)
    {
        $this->tries = $trie;
    }

    /**
     * 设置连接超时时间
     * @param int $timeout
     * @return void
     * @author SwitchSwitch
     */
    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
    }


    /**
     * 失败的操作
     * @param Throwable $exception
     * @return void
     * @author SwitchSwitch
     */
    public function failed(Throwable $exception): void
    {
        $this->log()->debug($exception->getMessage());
    }

    public function getJobName()
    {
        if ($this->queue) {
            return $this->queue;
        } else {
            $className = static::class;
            $pos = strrpos($className, '\\');
            if ($pos !== false) {
                $className = substr($className, $pos + 1);
            }
            return ucfirst(preg_replace('/([a-z])([A-Z])/', '$1-$2', $className));
        }
    }

    /***
     * 设置队列的名字
     * @return $this
     * @author SwitchSwitch
     */
    public function setQueue()
    {
        $this->onQueue($this->getJobName());
        return $this;
    }

    /**
     * @return $this
     * @author SwitchSwitch
     */
    public function setConnection()
    {
        $this->onConnection('rabbitmq');
        return $this;
    }

    /**
     * 队列的准备工作
     * @return void
     * @throws \ReflectionException
     * @author SwitchSwitch
     */
    public function prepare()
    {
        $this->setQueue()->setConnection();
        if ($this->delay) {
            $this->createAmqpQueue($this->getAmqpObj());
        }
    }

    /**
     * 获得队列的服务对象
     * @return void
     * @throws \ReflectionException
     * @author SwitchSwitch
     */
    public function getAmqpObj()
    {
        $dispatcher = app(Dispatcher::class);
        $reflection = new \ReflectionClass($dispatcher);
        $property = $reflection->getProperty('queueResolver');
        $property->setAccessible(true);
        $queueResolver = $property->getValue($dispatcher);
        return call_user_func($queueResolver, 'rabbitmq');
    }

    /**
     * 创建一个amqp的队列
     * @param $queue
     * @return void
     * @throws \ReflectionException
     * @author SwitchSwitch
     */
    public function createAmqpQueue($queue)
    {
        $reflectionClassAmqp = new \ReflectionClass($queue);
        $publishPropertiesMethod = $reflectionClassAmqp->getMethod('publishProperties');
        $declareDestinationMethod = $reflectionClassAmqp->getMethod('declareDestination');
        $publishPropertiesMethod->setAccessible(true);
        $declareDestinationMethod->setAccessible(true);
        [$destination, $exchange, $exchangeType, $attempts] = $publishPropertiesMethod->invoke($queue, $this->queue, []);
        $declareDestinationMethod->invoke($queue, $destination, $exchange, $exchangeType);
    }

    public function getAmqpQueues()
    {
        $apiBaseUrl = 'http://localhost:15672';
        $apiUsername = 'guest';
        $apiPassword = 'guest';
        $client = new Client([
            'base_uri' => $apiBaseUrl,
            'auth' => [$apiUsername, $apiPassword],
        ]);
        $response = $client->get('/api/queues');
        if ($response->getStatusCode() == 200) {
            $queues = json_decode($response->getBody(), true);
            if (is_array($queues)) {
                $queueNames = array_column($queues, 'name');
                foreach ($queueNames as $queueName) {
                    echo $queueName . "\n";
                }
            }
        } else {
            echo "Failed to retrieve queues.\n";
        }
    }
}
