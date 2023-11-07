<?php
/**
 * ErrorWarnListener.php
 * 异常发送短信
 * Created on 2023/11/2 16:10
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Listeners;


//use App\Models\Abnormal;
use ClearSwitch\DragonBallLaravel\Events\ErrorWarnEvent;
use ClearSwitch\DragonBallLaravel\Traits\Robot;
use Illuminate\Support\Facades\Redis;

class ErrorWarnListener
{
    use Robot;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @var array 发送的消息的哈希和时间
     */
    protected static $hashs = [];

    /**
     * @var int 静默时间
     */
    protected $silence = 7200;

    /**
     * @var int 上次发送时间
     */
    protected $lastSendAt = null;

    /**
     * @param ErrorWarnEvent $event
     * @return void
     * @author SwitchSwitch
     */
    public function handle(ErrorWarnEvent $event)
    {
//        if (!empty($event->message['logistic'])) {
//            $model = new Abnormal(
//                [
//                    'orderId' => $event->message['orderId'],
//                    'content' => $event->message['logistic']
//                ]
//            );
//            $model->save();
//        }
        if ($this->should($event->message)) {
            $this->qyWeChat($event->message);
        }
    }

    /**
     * 判断是不是已经发送过了消息
     * @param $content
     * @return bool
     * @author clearSwitch
     */
    protected function should($content)
    {
        $now = time();
        $contentHash = hash('sha256', json_encode($content));
        if (Redis::get($contentHash)) {
            return false;
        } else {
            Redis::set($contentHash, time());
            Redis::expireAt($contentHash, $now + $this->silence);
            return true;
        }
    }
}
