<?php
/**
 * Builder.php
 * 文件描述
 * Created on 2023/11/1 11:28
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Models;

/**
 * 继承模型中的builder 进行修改模型builder 中的方法
 * Class Builder
 * @package App\Models
 */
class Builder extends \Illuminate\Database\Eloquent\Builder
{

    /**
     * @param array $attributes
     * @param array $values
     * @return bool
     * @author clearSwitch
     */
    public function updateOrInsert(array $attributes, array $values = [])
    {
        if (!$this->where($attributes)->exists()) {
            $keyName = $this->model->getKeyName();
            if (empty($values[$keyName])) {
                $values['id'] = App('snowflake')->id();
            }
            return $this->insert(array_merge($attributes, $values));
        }
        if (empty($values)) {
            return true;
        }
        return (bool)$this->limit(1)->update($values);
    }
}
