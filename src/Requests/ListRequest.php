<?php
/**
 * ListRequest.php
 * 列表的请求
 * Created on 2023/11/2 16:55
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Requests;


use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;

class ListRequest extends QueryRequest
{

    /**
     * 验证之前我给他一个值
     * @author clearSwitch
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'page' => 1,
            'page_size' => 1000
        ]);
    }
    /**
     * Date: 2022/6/9 下午2:05
     * @param ValidationFactory $factory
     * @return Validator
     * @author clearSwitch
     */
    protected function createDefaultValidator(ValidationFactory $factory): Validator
    {
        $rules = array_merge([
            'page' => ['int', ['min', 1]],
            'page_size' => ['int', ['min', 1], ['max', 1000]]
        ], $this->rules());
        $attributes = array_merge([
            'page' => '页码',
            'page_size' => '分页大小'
        ], $this->attributes());
        return $factory->make(
            $this->validationData(),
            $rules,
            $this->messages(),
            $attributes
        );
    }
}
