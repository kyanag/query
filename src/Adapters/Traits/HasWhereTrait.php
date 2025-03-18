<?php

namespace Kyanag\Query\Adapters\Traits;

use Kyanag\Query\Adapters\AbstractQuery;
use Kyanag\Query\Adapters\Select;
use Kyanag\Query\Adapters\SubQuery\WhereQuery;
use Kyanag\Query\Adapters\Update;
use Latitude\QueryBuilder\CriteriaInterface;
use Latitude\QueryBuilder\EngineInterface;
use Latitude\QueryBuilder\Query\Capability\HasFrom;
use Latitude\QueryBuilder\StatementInterface;

use function Latitude\QueryBuilder\criteria;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\group;
use function Latitude\QueryBuilder\identify;

/**
 * @property HasFrom $query
 */
trait HasWhereTrait
{
    /**
     * @param string|StatementInterface|\Closure $field
     * @param string|null|StatementInterface $operator
     * @param string|array|null|StatementInterface $value
     * @return self
     * @throws \Exception
     */
    public function where($field, $operator = null, $value = null, $type = "and"): self
    {
        if ($field instanceof \Closure) {
            $criteria = new WhereQuery();
            $res = call_user_func($field, $criteria);
            if ($res !== null) {
                $criteria = $res;
            }
            if (!$criteria->isEmpty()) {
                $criteria = group($criteria->toQuery());
                $this->addCondition($criteria, $type);
            }
            return $this;
        }
        if ($operator === null && $value === null) {
            $value = null;
            $operator = "=";
        } elseif ($value === null) {
            $value = $operator;
            $operator = "=";
        }
        $operator = strtolower($operator);
        switch ($operator) {
            case "=":
            case "eq":
                if ($value === null) {
                    $this->addCondition(field($field)->isNull(), $type);
                } else {
                    $this->addCondition(field($field)->eq($value), $type);
                }
                break;
            case "neq":
            case "!=":
            case "<>":
                if ($value === null) {
                    $this->addCondition(field($field)->isNotNull(), $type);
                } else {
                    $this->addCondition(field($field)->notEq($value), $type);
                }
                break;
            case ">=":
            case "gte":
                $this->addCondition(field($field)->gte($value), $type);
                break;
            case "<=":
            case "lte":
                $this->addCondition(field($field)->lte($value), $type);
                break;
            case ">":
            case "gt":
                $this->addCondition(field($field)->gt($value), $type);
                break;
            case "<":
            case "lt":
                $this->addCondition(field($field)->lt($value), $type);
                break;
            case "in":
                $this->addCondition(field($field)->in(...$value), $type);
                break;
            case "not in":
            case "notin":
                $this->addCondition(field($field)->notIn(...$value), $type);
                break;
            case "between":
                list($start, $end) = $value;
                $this->addCondition(field($field)->between($start, $end), $type);
                break;
            case "not between":
            case "notbetween":
                list($start, $end) = $value;
                $this->addCondition(field($field)->notBetween($start, $end), $type);
                break;
            default:
                throw new \Exception("operate: [{$operator}] 不存在");
        }
        return $this;
    }


    /**
     * @param $field
     * @param string|null $operator
     * @param $value
     * @return $this
     * @throws \Exception
     */
    public function orWhere($field, string $operator = null, $value = null): self
    {
        return $this->where($field, $operator, $value, "or");
    }


    public function whereNull($field): self
    {
        $this->addCondition(field($field)->isNotNull());
        return $this;
    }


    public function whereNotNull($field): self
    {
        $this->addCondition(
            criteria("%s NOT NULL", identify($field))
        );
        return $this;
    }

    /**
     * @param string $raw
     * @param array $params
     * @return $this
     */
    public function whereRaw(string $raw, array $params = []): self
    {
        $this->addCondition(
            criteria($raw, ...$params)
        );
        return $this;
    }

    /**
     * @param $field
     * @param array|callable $values
     * @return Update|Select|WhereQuery|HasWhereTrait
     */
    public function whereIn($field, $values): self
    {
        if ($values instanceof \Closure) {
            $values = call_user_func($values);
            if (!is_array($values)) {
                return $this->whereInQuery($field, $values);
            }
        }
        $this->addCondition(field($field)->in(...$values));
        return $this;
    }


    /**
     * @param $field
     * @param array|callable $values
     * @return Update|Select|WhereQuery|HasWhereTrait
     */
    public function whereNotIn($field, $values): self
    {
        if ($values instanceof \Closure) {
            $values = call_user_func($values);
            if (!is_array($values)) {
                return $this->whereNotInQuery($field, $values);
            }
        }
        $this->addCondition(field($field)->notIn(...$values));
        return $this;
    }


    /**
     * 子查询whereIn
     * @param string|StatementInterface $field
     * @param StatementInterface $query
     * @return void
     */
    protected function whereInQuery($field, $query): self
    {
        if ($query instanceof Select) {
            $query = $query->toQuery();
        }
        if (!$query instanceof StatementInterface) {
            throw new \Exception("WhereIn 子句查询请调用 asQuery 方法");
        }
        $this->addCondition(
            criteria("%s IN (%s)", identify($field), $query)
        );
        return $this;
    }

    /**
     * 子查询whereIn
     * @param string|StatementInterface $field
     * @param StatementInterface $query
     * @return void
     */
    protected function whereNotInQuery($field, $query): self
    {
        if ($query instanceof Select) {
            $query = $query->toQuery();
        }
        if (!$query instanceof StatementInterface) {
            throw new \Exception("WhereIn 子句查询请调用 asQuery 方法");
        }
        $this->addCondition(
            criteria("%s IN (%s)", identify($field), $query)
        );
        return $this;
    }


    /**
     * @param $field
     * @param array $between
     * @return self
     */
    public function whereBetween($field, array $between = []): self
    {
        list($start, $end) = $between;
        $this->addCondition(field($field)->between($start, $end));
        return $this;
    }


    public function whereNotBetween($field, array $between = []): self
    {
        list($start, $end) = $between;
        $this->addCondition(field($field)->notBetween($start, $end));
        return $this;
    }

    public function whereLike($field, string $pattern): self
    {
        $this->addCondition(
            criteria("%s LIKE %s", identify($field), $pattern)
        );
        return $this;
    }


    public function whereNotLike($field, string $pattern): self
    {
        $this->addCondition(
            criteria("%s NOT LIKE %s", identify($field), $pattern)
        );
        return $this;
    }


    abstract public function addCondition(CriteriaInterface $condition, $type = "and");
}


final class Statement implements StatementInterface
{
    /**
     * @var array
     */
    private array $params = [];

    /** @var string */
    protected string $sql;

    public function __construct(string $sql, array $params)
    {
        $this->sql = $sql;
        $this->params = $params;
    }

    public function sql(EngineInterface $engine): string
    {
        return $this->sql;
    }

    public function params(EngineInterface $engine): array
    {
        return $this->params;
    }
}
