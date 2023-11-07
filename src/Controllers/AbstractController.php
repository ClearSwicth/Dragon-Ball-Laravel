<?php
/**
 * AbstractController.php
 * 文件描述
 * Created on 2023/11/3 14:39
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Controllers;

use ClearSwitch\DragonBallLaravel\Component\DataProvider;
use ClearSwitch\DragonBallLaravel\Events\ErrorWarnEvent;
use Illuminate\Contracts\Support\Arrayable;

class AbstractController extends Controller
{

    /**
     * 获得用户信息
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     * @author clearSwitch
     */
    public function user()
    {
        $user = auth('api')->user();
        return $user;
    }

    /**
     * 发送响应
     * @param $result
     * @param int $code
     * @param string $message
     * @author clearSwitch
     */
    protected function send($result, $code = 200, $message = 'Success')
    {
        if ($code != 200 && $code != 144) {
            event(new ErrorWarnEvent($result));
        }
        if ($result instanceof DataProvider) {
            $result = $this->serializeDataProvider($result);
        }
        return response()->json(
            $this->normalizeData(['code' => $code, 'data' => $result, 'msg' => $message])
        );
    }

    /**
     * 发送消息
     * @param $message
     * @param int $code
     * @author clearSwitch
     */
    protected function message($message, $code = 200)
    {
        return $this->send(null, $code, $message);
    }

    /**
     * 错我消息
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @author SwitchSwitch
     */
    protected function errorMessage($message, $code = 310)
    {
        return $this->send(null, $code, $message);
    }

    /**
     * 格式化数据
     * @param $data
     * @return array|int|string
     * @author clearSwitch
     */
    protected function normalizeData($data)
    {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }
        if (is_array($data)) {
            foreach ($data as $key => $e) {
                $data[$key] = $this->normalizeData($e);
            }
        } else if (is_int($data)) {
            if ($data > 2147483647) {
                $data = (string)$data;
            }
        }
        return $data;
    }

    /**
     * 序列化数据提供器
     * @param DataProvider $provider 数据提供器
     * @return array
     * @author clearSwitch。
     */
    protected function serializeDataProvider(DataProvider $provider): array
    {
        return ['page' => $provider->getPage(), 'page_size' => $provider->getPageSize(), 'last_page' => $provider->getLastPage(), 'count' => $provider->getCount(), 'rows' => $provider->getRows()];
    }

}
