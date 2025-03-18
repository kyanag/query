<?php

namespace Kyanag\Query\Adapters;

use Kyanag\Query\Adapters\Traits\HasWhereTrait;
use Latitude\QueryBuilder\CriteriaInterface;
use Latitude\QueryBuilder\Query\UpdateQuery;

class Update extends AbstractQuery
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
