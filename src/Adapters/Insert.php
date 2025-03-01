<?php

namespace Kyanag\Query\Adapters;

use Kyanag\Query\Connection;
use Kyanag\Query\Interfaces\ConnectionInterface;
use Latitude\QueryBuilder\Query\InsertQuery;


class Insert extends AbstractQuery
{

    protected InsertQuery $query;


    protected ConnectionInterface $connection;


    public function __construct(InsertQuery $query)
    {
        $this->query = $query;
    }


    public function table(string $table): self
    {
        $table = $this->_FormatTable($table);
        $this->query = $this->query->into($table);
        return $this;
    }

    /**
     * @param array $value
     * @return bool
     */
    public function insert(array $value): bool
    {
        return $this->insertAll([$value]) >= 1;
    }

    /**
     * @param array $values
     * @return int
     */
    public function insertAll(array $values): int
    {
        $maps = array_keys($values[0]);
        $query = $this->query->columns(...$maps);

        foreach ($values as $value){
            $query->values(...array_values($value));
        }
        $query = $query->compile();
        return $this->connection->exec($query->sql(), $query->params());
    }
}