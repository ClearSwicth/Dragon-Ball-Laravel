<?php
/**
 * JsonRequest.php
 * 请求参数为一个json
 * Created on 2023/11/2 16:53
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Requests;

class JsonRequest extends AbstractRequest
{
    /**
     * 确定当前请求是JSON。
     * @return bool
     * @author daikai
     */
    public function wantsJson()
    {
        return true;
    }

    /**
     * 确定当前请求是否可能需要一个JSON响应。
     * @return bool
     * @author daikai
     */
    public function expectsJson()
    {
        return true;
    }
}
