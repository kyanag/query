<?php

namespace Kyanag\Query\Adapters;

use Kyanag\Query\Interfaces\ConnectionInterface;
use Kyanag\Query\Adapters\Traits\HasWhereTrait;
use Latitude\QueryBuilder\CriteriaInterface;
use Latitude\QueryBuilder\Query\DeleteQuery;
use Latitude\QueryBuilder\Query\SelectQuery;

class Delete extends AbstractQuery
{
    use HasWhereTrait;


    protected DeleteQuery $query;


    public function __construct(DeleteQuery $query)
    {
        $this->query = $query;
    }

    public function addCondition(CriteriaInterface $condition, $type = "and")
    {
        if ($type == "and") {
            $this->query->andWhere($condition);
        } else {
            $this->query->orWhere($condition);
        }
    }


    public function limit(int $limit = null): self
    {
        $this->query->limit($limit);
        return $this;
    }

    public function table($table)
    {
        $table = $this->_FormatTable($table);
        $this->query->from($table);
        return $this;
    }


    public function delete()
    {
        return $this;
    }
}
