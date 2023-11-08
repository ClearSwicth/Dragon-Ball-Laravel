<?php
/**
 * QyWeChatSend.php
 * 文件描述
 * Created on 2023/11/2 16:35
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Service\SendMessage\QyWeChat;

use Clearswitch\DragonBallLaraver\Validations\ValidationException;
use ClearSwitch\DragonBallLaravel\Service\SendMessage\MessageFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class QyWeChatSend implements MessageFactory
{

    static $token;

    public function __construct()
    {
        if (Redis::get('qyToken')) {
            self::$token = Redis::get('qyToken');
        } else {
            $this->getToken();
            self::$token = Redis::get('qyToken');
        }

    }

    /**
     * 获得企业微信的token
     * @author clearSwitch
     */
    public function getToken()
    {
        $corpid = config('robot.qy_we_chat.corpid');
        $corpsecret = config('robot.qy_we_chat.corpsecret');
        $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=" . $corpid . "&corpsecret=" . $corpsecret;
        $response = Http::get($url);
        if ($response->ok()) {
            $responseData = $response->json();
            if ($responseData['errcode'] === 0) {
                self::$token = $responseData['access_token'];
                $name = "qyToken";
                Redis::setex($name, 3600, self::$token);
            }
        } else {
            throw new ValidationException($response);
        }
    }

    /**
     * 获得企业微信部门中所有成员信息
     * @author clearSwitch
     */
    public function getUserInfo()
    {
        $access_token = self::$token;
        $department_id = 1;//企业微信的部门id
        $fetch_child = 1;//	1/0：是否递归获取子部门下面的成员
        $status = 0;//0获取全部成员，1获取已关注成员列表，2获取禁用成员列表，4获取未关注成员列表。status可叠加，未填写则默认为4
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token=" . $access_token . "&department_id=" . $department_id . "&fetch_child=" . $fetch_child . "&status=" . $status;
        $response = Http::get($url);
        if ($response->ok()) {
            $responseData = $response->body();
            print_r($responseData);
        } else {
            throw new ValidationException($response);
        }
    }

    public function sendMessage(...$args)
    {
        $sendData['touser'] = config('robot.qy_we_chat.touser');
        $sendData['toparty'] = "";
        $sendData['totag'] = "";
        $sendData['msgtype'] = "text";
        $sendData['agentid'] = "1000009";//创建的应用Id
        $sendData['text']['content'] = "【logistic】:🔥💔️⏰ERROR - " . date('Y-m-d H:i:S') . json_encode($args[0], JSON_UNESCAPED_UNICODE);
        $sendData['safe'] = 0;
        $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=" . self::$token;
        $response = Http::withHeaders([
            'Content-Type' => "application/json"
        ])->post($url, $sendData);
        if ($response->ok()) {
            $responseData = $response->json();
            if ($responseData['errmsg'] == "ok") {
                return true;
            } else {
                return false;
            }
        } else {
            throw new ValidationException($response);
        }
    }

    public function pushMessage(...$args)
    {
        $sendData['touser'] = $args[0];
        $sendData['toparty'] = "";
        $sendData['totag'] = "";
        $sendData['msgtype'] = "text";
        $sendData['agentid'] = "1000009";//创建的应用Id
        $sendData['text']['content'] = "【logistic】:🔥💔️⏰ERROR - " . date('Y-m-d H:i:S') . json_encode($args[1], JSON_UNESCAPED_UNICODE);
        $sendData['safe'] = 0;
        $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=" . self::$token;
        $response = Http::withHeaders([
            'Content-Type' => "application/json"
        ])->post($url, $sendData);
        if ($response->ok()) {
            $responseData = $response->json();
            if ($responseData['errmsg'] == "ok") {
                return true;
            } else {
                return false;
            }
        } else {
            throw new ValidationException($response);
        }
    }

}
