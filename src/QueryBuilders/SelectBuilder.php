<?php

namespace Kyanag\Query\QueryBuilders;

use Kyanag\Query\QueryBuilders\SubQuery\WhereQuery;
use Kyanag\Query\QueryBuilders\Traits\HasOrderTrait;
use Kyanag\Query\QueryBuilders\Traits\HasWhereTrait;
use Latitude\QueryBuilder\CriteriaInterface;
use Latitude\QueryBuilder\Query\SelectQuery;

use function Latitude\QueryBuilder\on;

class SelectBuilder extends AbstractQueryBuilder
{
    use HasWhereTrait;
    use HasOrderTrait;


    protected SelectQuery $query;


    protected ?WhereQuery $having = null;


    public function __construct(SelectQuery $query)
    {
        $this->query = $query;
    }


    public function table($table): self
    {
        $table = $this->_FormatTable($table);
        $this->query->from($table);
        return $this;
    }


    public function select(...$columns): self
    {
        if (count($columns) > 1 && is_array($columns[0])) {
            $columns = $columns[0];
        }
        $columns = array_map([$this, "_FormatField"], $columns);
        $this->query->columns(...$columns);
        return $this;
    }


    public function join($table, $left_column, $right_column, $type = "join"): self
    {
        $table = $this->_FormatTable($table);
        $left_column = $this->_FormatField($left_column);
        $right_column = $this->_FormatField($right_column);

        $on = on($left_column, $right_column);
        if (strtolower($type) == "join") {
            $type = "";
        }
        $this->query->join($table, $on, $type);
        return $this;
    }


    public function leftJoin($table, $right_column, $left_column): self
    {
        return $this->join($table, $right_column, $left_column, "LEFT");
    }


    public function rightJoin($table, $right_column, $left_column): self
    {
        return $this->join($table, $right_column, $left_column, "RIGHT");
    }


    public function innerJoin($table, $right_column, $left_column): self
    {
        return $this->join($table, $right_column, $left_column, "INNER");
    }


    public function fullJoin($table, $right_column, $left_column): self
    {
        return $this->join($table, $right_column, $left_column, "FULL");
    }


    public function limit(int $limit = null): self
    {
        $this->query->limit($limit);
        return $this;
    }


    public function groupBy(...$fields): self
    {
        $this->query->groupBy(...$fields);
        return $this;
    }


    public function groupByRaw($raw)
    {
        $this->query->groupBy(query_raw($raw));
        return $this;
    }


    public function having($field, $operator = null, $value = null, $type = "and")
    {
        if ($this->having === null) {
            $this->having = new WhereQuery();
        }
        $this->having->where($field, $operator, $value, $type);
        return $this;
    }


    public function orHaving($field, $operator = null, $value = null)
    {
        $this->having($field, $operator, $value, "or");
        return $this;
    }


    public function havingRaw(string $raw, array $params = [])
    {
        if ($this->having === null) {
            $this->having = new WhereQuery();
        }
        $this->having->whereRaw($raw, $params);
        return $this;
    }


    public function addCondition(CriteriaInterface $condition, $type = "and")
    {
        if ($type == "and") {
            $this->query->andWhere($condition);
        } else {
            $this->query->orWhere($condition);
        }
    }



    public function toQuery()
    {
        /** @var SelectQuery $query */
        $query = parent::toQuery();
        if ($this->having !== null && !$this->having->isEmpty()) {
            $query = $query->having($this->having->toQuery());
        }
        return $query;
    }


    public function first()
    {
        $this->query->limit(1);
        return $this;
    }


    public function find($value, $field = "id")
    {
        $this->where($field, $value);

        $this->query->limit(1);
        return $this;
    }

    public function get()
    {
        return $this;
    }
}
