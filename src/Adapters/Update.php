<?php

namespace Kyanag\Query\Adapters;

use Kyanag\Query\Interfaces\ConnectionInterface;
use Kyanag\Query\Adapters\Traits\HasWhereTrait;
use Kyanag\Query\Connection;
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


    /**
     * 执行查询
     * @param array $values
     * @return int
     */
    public function update(array $values): int
    {
        $query = $this->query->set($values)->compile();
        return $this->connection->exec($query->sql(), $query->params()) ?: 0;
    }


    public function addCondition(CriteriaInterface $condition, $type = "and")
    {
        if($type == "and"){
            $this->query->andWhere($condition);
        }else{
            $this->query->orWhere($condition);
        }
    }
}