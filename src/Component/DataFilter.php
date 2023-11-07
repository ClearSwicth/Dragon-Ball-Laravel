<?php
/**
 * DataFilter.php
 * 文件描述
 * Created on 2023/11/1 16:50
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Component;

use App\Exceptions\ServerException;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

class DataFilter
{
    /**
     * @var array $query 查询参数
     * @author clearSwitch。
     */
    protected $querys;

    /**
     * @var array 规则
     * @author clearSwitch。
     */
    protected $rules = [];

    /**
     * @var array 关联规则
     * @author clearSwitch。
     */
    protected $hasRules = [];

    /**
     * @var array 连接规则
     * @author clearSwitch。
     */
    protected $joinRules = [];

    /**
     * @var array 无关联规则
     * @author clearSwitch。
     */
    protected $doesntHaveRules = [];

    /**
     * @var array 存在规则
     * @author clearSwitch。
     */
    protected $existRules = [];

    /**
     * @var bool 是否是导出
     * @author clearSwitch。
     */
    protected $isExport = false;

    /**
     * @param User 用户信息
     * @author clearSwitch。
     */
    protected $user = null;

    /**
     * @var int 周期
     * @author clearSwitch。
     */
    protected $period = null;

    /**
     * @var Callable 构建器
     * @author clearSwitch。
     */
    protected $builder = null;

    /**
     * 构造函数
     * @author clearSwitch。
     */
    public function __construct(array $querys)
    {
        $this->querys = $querys;
    }

    /**
     * 设置导出
     * @param bool $export 是否是导出
     * @author clearSwitch。
     */
    public function setExport($export = true)
    {
        $this->isExport = $export;
        $this->period = floor(time() / 60);
        return $this;
    }

    /**
     * 获取检索条件
     * @return array
     * @author clearSwitch。
     */
    public function getQuerys(): array
    {
        return $this->querys;
    }

    /**
     * 根据名称获取检索条件
     * @return mixed
     * @author clearSwitch。
     */
    public function getQuery($name)
    {
        return $this->querys[$name] ?? false;
    }

    /**
     * 删除一条检索条件
     * @return mixed
     * @author Berlin
     */
    public function delQuery($name)
    {
        if (isset($this->querys[$name])) {
            unset($this->querys[$name]);
        }
        return $this;
    }

    /**
     * 添加规则
     * @param string $name 参数名称
     * @param string $operator 操作符
     * @param string|array $field 字段
     * @param bool $skipEmpty 为空时是否跳过
     * @param string $boolean 规则间的关系
     * @return static
     * @author clearSwitch。
     */
    public function addRule($name, $operator = '=', $field = null, $skipEmpty = true, $boolean = 'and')
    {
        $this->rules[] = [
            'name' => $name,
            'field' => $field,
            'operator' => $operator,
            'skipEmpty' => $skipEmpty,
            'boolean' => $boolean
        ];
        return $this;
    }

    /**
     * 添加无关联规则
     * @param string $relation 关联关系
     * @param string $name 参数名称
     * @param string $operator 操作符
     * @param string $field 字段
     * @param bool $skipEmpty 为空时是否跳过
     * @param string $boolean 规则间的关系
     * @return static
     * @author clearSwitch。
     */
    public function addDoesntHaveRule($relation, $name, $operator = '=', $field = null, $skipEmpty = true, $boolean = 'and')
    {
        $this->doesntHaveRules[] = [
            'relation' => $relation,
            'name' => $name,
            'operator' => $operator,
            'field' => $field,
            'skipEmpty' => $skipEmpty,
            'boolean' => $boolean
        ];
        return $this;
    }

    /**
     * 添加存在规则
     * @param string $relation 关联关系
     * @param string $name 参数名称
     * @param string $operator 操作符
     * @param string $field 字段
     * @param bool $skipEmpty 为空时是否跳过
     * @param string $boolean 规则间的关系
     * @return static
     * @author clearSwitch。
     */
    public function addHasRule($relation, $name, $operator = '=', $field = null, $skipEmpty = true, $boolean = 'and')
    {
        $this->hasRules[] = [
            'relation' => $relation,
            'name' => $name,
            'operator' => $operator,
            'field' => $field,
            'skipEmpty' => $skipEmpty,
            'boolean' => $boolean
        ];
        return $this;
    }

    /**
     * 添加存在规则
     * @param string $relation 关联关系
     * @param string $name 参数名称
     * @param bool $skipEmpty 为空时是否跳过
     * @param string $boolean 规则间的关系
     * @return static
     * @author clearSwitch。
     */
    public function addExistRule($relation, $name, $skipEmpty = true, $boolean = 'and')
    {
        $this->existRules[] = [
            'relation' => $relation,
            'name' => $name,
            'skipEmpty' => $skipEmpty,
            'boolean' => $boolean
        ];
        return $this;
    }

    /**
     * 添加连接规则
     * @param string $relation 关联关系
     * @param string $name 参数名称
     * @param string $operator 操作符
     * @param string $field 字段
     * @param bool $skipEmpty 为空时是否跳过
     * @param string $boolean 规则间的关系
     * @return static
     * @author clearSwitch。
     */
    public function addJoinRule($relation, $name, $operator = '=', $field = null, $skipEmpty = true, $boolean = 'and')
    {
        $this->joinRules[] = [
            'relation' => $relation,
            'name' => $name,
            'operator' => $operator,
            'field' => $field,
            'skipEmpty' => $skipEmpty,
            'boolean' => $boolean
        ];
        return $this;
    }

    /**
     * 获取活跃的规则
     * @return array
     * @author clearSwitch。
     */
    public function getActiveRules()
    {
        $rules = [];
        foreach ($this->rules as $rule) {
            $value = $this->getQuery($rule['name']);
            if ($value !== false || (is_array($value) && count($value) > 0)) {
                if ($value !== '' || $rule['skipEmpty'] === false) {
                    $rule['value'] = $value;
                    if ($rule['field'] === null) {
                        $rule['field'] = $rule['name'];
                    }
                    $rules[] = $rule;
                }
            }
        }
        return $rules;
    }

    /**
     * 获取活跃的存在规则
     * @return array
     * @author clearSwitch。
     */
    public function getActiveHasRules()
    {
        return $this->getActiveRelationRules('hasRules');
    }

    /**
     * 获取活跃的不存在规则
     * @return array
     * @author clearSwitch。
     */
    public function getActiveDoesntHaveRules()
    {
        return $this->getActiveRelationRules('doesntHaveRules');
    }

    /**
     * 获取活跃的关联规格
     * @param string $type 关联类型
     * @return array
     * @author clearSwitch。
     */
    protected function getActiveRelationRules($type)
    {
        $rules = [];
        foreach ($this->$type as $rule) {
            $value = $this->getQuery($rule['name']);
            if ($value !== false) {
                if ($value !== '' || $rule['skipEmpty'] === false) {
                    if (!isset($rules[$rule['relation']])) {
                        $rules[$rule['relation']] = [];
                    }
                    $rule['value'] = $value;
                    if ($rule['field'] === null) {
                        $rule['field'] = $rule['name'];
                    }
                    $rules[$rule['relation']][] = $rule;
                }
            }
        }
        return $rules;
    }

    /**
     * 获取活跃的存在规则
     * @return array
     * @author clearSwitch。
     */
    public function getActiveExistRules()
    {
        $rules = [];
        foreach ($this->existRules as $rule) {
            $value = $this->getQuery($rule['name']);
            if ($value !== false) {
                if ($value !== '' || $rule['skipEmpty'] === false) {
                    $rule['value'] = $value;
                    $rules[] = $rule;
                }
            }
        }
        return $rules;
    }

    /**
     * 构建
     * @param Builder 构建器
     * @return Builder
     * @author clearSwitch。
     */
    public function build(Builder $builder): Builder
    {
        if (is_callable($this->builder)) {
            return call_user_func($this->builder, $builder, $this);
        } else {
            return $this->buildIt($builder);
        }
    }

    /**
     * 自带的构建方法
     * @param Builder 构建器
     * @return Builder
     * @author clearSwitch。
     */
    public function buildIt(Builder $builder)
    {
        foreach ($this->getActiveRules() as $rule) {
            $this->buildWhere($builder, $rule);
        }
        foreach ([
                     'hasRules' => 'whereHas',
                     'doesntHaveRules' => 'whereDoesntHave'
                 ] as $type => $method) {
            foreach ($this->getActiveRelationRules($type) as $relation => $rules) {
                call_user_func([$builder, $method], $relation, function ($builder2) use ($rules) {
                    foreach ($rules as $rule) {
                        $this->buildWhere($builder2, $rule);
                    }
                });
            }
        }
        foreach ($this->getActiveExistRules() as $rule) {
            if ($this->isTrue($rule['value'])) {
                $builder->has($rule['relation'], '>=', 1, $rule['boolean']);
            } else if ($this->isFalse($rule['value'])) {
                $builder->doesntHave($rule['relation'], $rule['boolean']);
            }
        }
        foreach ($this->getActiveRelationRules('joinRules') as $relation => $rules) {
            $model = $builder->getModel();
            $table = null;
            if (method_exists($model, $relation)) {
                $modelRelation = call_user_func([$model, $relation]);
                if ($modelRelation instanceof HasOneOrMany) {
                    $table = $modelRelation->getRelated()->getTable();
                    $builder->join($table, $modelRelation->getQualifiedParentKeyName(), '=', $modelRelation->getQualifiedForeignKeyName());
                    $builder->groupBy($modelRelation->getQualifiedParentKeyName());
                } else {
                    throw new ServerException('Unknown relation ' . $relation);
                }
            } else {
                throw new ServerException('Unknown relation ' . $relation);
            }
            foreach ($rules as $rule) {
                if (strpos($rule['field'], '.') === false) {
                    $rule['field'] = $table . '.' . $rule['field'];
                }
                $this->buildWhere($builder, $rule);
            }
        }
        return $builder;
    }

    /**
     * 获取唯一哈希值
     * @return string
     * @author clearSwitch。
     */
    public function getHash(): string
    {
        return hash('SHA256', serialize($this));
    }

    /**
     * 获取用户
     * @param User 用户
     * @return static
     * @author clearSwitch。
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * 设置构造器
     * @return static
     * @author clearSwitch。
     */
    public function setBuilder(callable $builder)
    {
        $this->builder = $builder;
        return $this;
    }

    /**
     * 获取用户
     * @return User|null
     * @author clearSwitch。
     */
    public function getUser()
    {
        if ($this->user === null) {
            $this->user = auth('api')->user() ?? null;
        }
        return $this->user;
    }

    /**
     * 构建检索条件
     * @param Builder 构建器
     * @param array $rule 规则
     * @author clearSwitch。
     */
    protected function buildWhere(Builder $builder, $rule)
    {
        if (!is_array($rule['field'])) {
            $field = $this->supplementTableName($rule['field'], $builder);
            switch ($rule['operator']) {
                case 'isNotNull':
                    $builder->whereNull($field, $rule['boolean'], $this->isTrue($rule['value']));
                    break;
                case 'isNull':
                    $builder->whereNull($field, $rule['boolean'], $this->isFalse($rule['value']));
                    break;
                case 'like':
                    $builder->where($field, 'like', '%' . $rule['value'] . '%', $rule['boolean']);
                    break;
                case 'in':
                    if (!is_array($rule['value'])) {
                        $rule['value'] = [$rule['value']];
                    }
                    $builder->whereIn($field, $rule['value'], $rule['boolean']);
                    break;
                default:
                    $builder->where($field, $rule['operator'], $rule['value'], $rule['boolean']);
                    break;
            }
        } else {
            $fields = $rule['field'];
            $boolean = array_shift($fields);
            $fields = $this->supplementTableName($fields, $builder);
            $builder->where(function ($query) use ($fields, $rule, $boolean) {
                switch ($rule['operator']) {
                    case 'isNotNull':
                        foreach ($fields as $field) {
                            $query->whereNull($field, $boolean, $this->isTrue($rule['value']));
                        }
                        break;
                    case 'isNull':
                        foreach ($fields as $field) {
                            $query->whereNull($field, $boolean, $this->isFalse($rule['value']));
                        }
                        break;
                    case 'like':
                        foreach ($fields as $field) {
                            $query->where($field, 'like', '%' . $rule['value'] . '%', $boolean);
                        }
                        break;
                    case 'in':
                        if (!is_array($rule['value'])) {
                            $rule['value'] = [$rule['value']];
                        }
                        foreach ($fields as $field) {
                            $query->whereIn($field, $rule['value'], $boolean);
                        }
                        break;
                    default:
                        foreach ($fields as $field) {
                            $query->where($field, $rule['operator'], $rule['value'], $boolean);
                        }
                        break;
                }
            }, null, null, $rule['boolean']);
        }
    }

    /**
     * 补充表名称
     * @param array|string 字段名称
     * @param Builder $builder 查询构建器
     * @return array|string
     * @author clearSwitch。
     */
    public function supplementTableName($field, $builder)
    {
        if (is_array($field)) {
            foreach ($field as $key => $value) {
                if (strpos($value, '.') === false) {
                    $field[$key] = $builder->getModel()->getTable() . '.' . $value;
                }
            }
        } else {
            if (strpos($field, '.') === false) {
                $field = $builder->getModel()->getTable() . '.' . $field;
            }
        }
        return $field;
    }

    /**
     * 判断是否为真
     * @param mixed $value 待判断的值
     * @return bool
     * @author clearSwitch。
     */
    protected function isTrue($value)
    {
        return $value === true || $value === 1 || $value === '1';
    }

    /**
     * 判断是否为假
     * @param mixed $value 待判断的值
     * @return bool
     * @author clearSwitch。
     */
    protected function isFalse($value)
    {
        return $value === false || $value === 0 || $value === '0';
    }
}
