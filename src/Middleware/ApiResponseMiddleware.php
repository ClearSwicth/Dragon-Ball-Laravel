<?php
/**
 * ApiResponseMiddleware.php
 * 验证返回参数是否数据统一
 * Created on 2023/11/2 17:06
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Middleware;

use ClearSwitch\DragonBallLaravel\Validations\ValidationException;
use Closure;
use Illuminate\Support\Facades\Validator;

class ApiResponseMiddleware extends AbstractMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if ($response->getStatusCode() == 200) {
            $data = $response->original;
            $validator = Validator::make($data, [
                'code' => ['required', 'integer'],
                'data' => ['array', 'nullable'],
                'msg' => ['required', 'string']
            ], [], ['code' => 'response必须含有code', 'data' => 'response必须含有data数组', 'msg' => 'response必须含有msg提示']);
            if ($validator->fails()) {
                throw new ValidationException($validator->errors()->first(), 110);
            }
        }
        return $response;
    }
}
