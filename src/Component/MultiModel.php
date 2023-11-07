<?php
/**
 * MultiModel.php
 * 多模型
 * Created on 2023/11/1 17:18
 * Creat by ClearSwitch
 */
namespace ClearSwitch\DragonBallLaravel\Component;

use ClearSwitch\DragonBallLaravel\Models\AbstractMode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class MultiModel
{
    /**
     * @var array 模型集合
     * @author clearSwitch
     */
    protected $models = [];


    /**
     * @var array 要删除的模型
     * @author clearSwitch
     */
    protected $deleteModels = [];


    /**
     * Date: 2022/5/19 下午5:27
     * @param $models
     * @param bool $isDelete
     * @return $this
     * @throws \Exception
     * @author clearSwitch
     */
    public function attach($models, $isDelete = false)
    {
        //Collection
        if ($models instanceof Collection) {
            $models = $models->all();
        }
        if (!is_array($models)) {
            $models = [$models];
        }
        foreach ($models as $model) {
            if ($model instanceof static) {
                foreach ($model->models as $e) {
                    $this->models[] = $e;
                }
                foreach ($model->deleteModels as $e) {
                    $this->deleteModels[] = $e;
                }
            } else if (
                $model instanceof AbstractMode
                || $model instanceof Builder
                || $model instanceof Relation
            ) {
                if ($isDelete) {
                    $this->deleteModels[] = $model;
                } else {
                    $this->models[] = $model;
                }
            } else {
                throw new \Exception('model must instance of ' . implode(', ', [AbstractMode::class, Builder::class, Relation::class]));
            }
        }
        return $this;
    }

    /**
     * Date: 2022/5/19 下午5:27
     * @param bool $includeDelete
     * @return array
     * @author clearSwitch
     */
    public function getModels($includeDelete = false): array
    {
        if ($includeDelete) {
            return $this->models + $this->deleteModels;
        }
        return $this->models;
    }

    /**
     * Date: 2022/5/19 下午5:26
     * @return bool
     * @throws \Throwable
     * @author clearSwitch
     */
    public function save(): bool
    {
        DB::beginTransaction();
        try {
            foreach ($this->models as $model) {
                $model->save();
            }
            foreach ($this->deleteModels as $model) {
                $model->delete();
            }
            Db::commit();
            return true;
        } catch (\Throwable $e) {
            Db::rollBack();
            throw $e;
        }
        return false;
    }
}
