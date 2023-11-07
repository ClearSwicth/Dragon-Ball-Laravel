<?php
/**
 * AbstractRequest.php
 * 请求
 * Created on 2023/11/2 16:52
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Requests;

use ClearSwitch\DragonBallLaravel\Events\ErrorWarnEvent;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class AbstractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Date: 2022/6/9 下午1:57
     * @return array
     * @author clearSwitch
     */
    public function attributes()
    {
        return [];
    }

    /**
     * 重写验证失败之后的重定向
     * @param Validator $validator
     * @author daikai
     */
    protected function failedValidation(Validator $validator)
    {
        $error = $validator->errors()->all();
        throw new HttpResponseException($this->fail(110, $error));
    }

    /**
     * 返回结果
     * Date: 2023/2/22 上午10:57
     * @param int $code
     * @param array $errors
     * @return JsonResponse
     * @author clearSwitch
     */
    protected function fail(int $code, array $errors): JsonResponse
    {
        event(new ErrorWarnEvent(json_encode($errors, JSON_UNESCAPED_UNICODE)));
        return response()->json(
            [
                'code' => $code,
                'msg' => $errors[0],
                'data' => $errors,
            ]
        );
    }

}
