<?php

namespace Kyanag\Query\QueryBuilders;

use Latitude\QueryBuilder\Query\InsertQuery;

class InsertBuilder extends AbstractQueryBuilder
{
    protected InsertQuery $query;


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
     * @param array $values
     * @return $this
     */
    public function insert(array $values): self
    {
        $first = array_first($values);
        if (!is_array($first)) {
            $values = [$values];
        }

        $values = array_values($values);
        $maps = array_keys($first);

        $this->query->columns(...$maps);
        foreach ($values as $value) {
            $this->query->values(...array_values($value));
        }
        return $this;
    }
}
