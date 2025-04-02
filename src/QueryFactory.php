<?php

namespace Kyanag\Query;

use Kyanag\Query\Interfaces\ConnectionInterface;
use Kyanag\Query\QueryBuilders\DeleteBuilder;
use Kyanag\Query\QueryBuilders\SelectBuilder;
use Kyanag\Query\QueryBuilders\UpdateBuilder;
use Kyanag\Query\QueryBuilders\InsertBuilder;

class QueryFactory
{
    protected \Latitude\QueryBuilder\QueryFactory $factory;


    public function __construct(\Latitude\QueryBuilder\QueryFactory $factory)
    {
        $this->factory = $factory;
    }



    public function createUpdate(string $table = ""): UpdateBuilder
    {
        return new UpdateBuilder($this->factory->update($table));
    }


    public function createSelect(string $table = ""): SelectBuilder
    {
        $select = new SelectBuilder($this->factory->select());
        if ($table) {
            $select->table($table);
        }
        return $select;
    }


    public function createInsert(string $table = ""): InsertBuilder
    {
        return new InsertBuilder($this->factory->insert($table));
    }


    public function createDelete(string $table = ""): DeleteBuilder
    {
        return new DeleteBuilder($this->factory->delete($table));
    }
}
