<?php

namespace Kyanag\Query\QueryBuilders;

use Kyanag\Query\QueryBuilders\Traits\HasWhereTrait;
use Latitude\QueryBuilder\CriteriaInterface;
use Latitude\QueryBuilder\Query\UpdateQuery;

class UpdateBuilder extends AbstractQueryBuilder
{
    use HasWhereTrait;


    protected UpdateQuery $query;


    public function __construct(UpdateQuery $query)
    {
        $this->query = $query;
    }


    public function table($table)
    {
        $table = $this->_FormatTable($table);
        $this->query->table($table);
        return $this;
    }


    public function update(array $values)
    {
        $this->query->set($values);
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
}
