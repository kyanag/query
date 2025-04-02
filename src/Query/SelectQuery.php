<?php

namespace Kyanag\Query\Query;

use Kyanag\Query\QueryBuilders\SelectBuilder;
use Kyanag\Query\Interfaces\ConnectionInterface;

/**
 * @mixin SelectBuilder
 */
class SelectQuery
{
    protected ConnectionInterface $connection;


    protected SelectBuilder $query;


    public function __construct(SelectBuilder $query, ConnectionInterface $connection)
    {
        $this->query = $query;
        $this->connection = $connection;
    }

    public function __call($name, $arguments)
    {
        call_user_func_array([$this->query, $name], $arguments);
        return $this;
    }


    /**
     * @return array
     */
    public function get()
    {
        list($sql, $params) = $this->query->get()->toSql();
        return $this->connection->select($sql, $params);
    }


    public function first()
    {
        list($sql, $params) = $this->query->first()->toSql();
        return $this->connection->select($sql, $params);
    }


    public function find($value, $field = "id")
    {
        list($sql, $params) = $this->query->find($value, $field)->toSql();
        return $this->connection->select($sql, $params);
    }
}
