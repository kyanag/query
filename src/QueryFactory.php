<?php

namespace Kyanag\Query;

use Kyanag\Query\Interfaces\ConnectionInterface;
use Kyanag\Query\Adapters\Delete;
use Kyanag\Query\Adapters\Select;
use Kyanag\Query\Adapters\Update;
use Kyanag\Query\Adapters\Insert;

class QueryFactory
{
    protected \Latitude\QueryBuilder\QueryFactory $factory;


    public function __construct(\Latitude\QueryBuilder\QueryFactory $factory)
    {
        $this->factory = $factory;
    }



    public function createUpdate(string $table = ""): Update
    {
        return new Update($this->factory->update($table));
    }


    public function createSelect(string $table = ""): Select
    {
        $select = new Select($this->factory->select());
        if($table){
            $select->table($table);
        }
        return $select;
    }


    public function createInsert(string $table = ""): Insert
    {
        return new Insert($this->factory->insert($table));
    }


    public function createDelete(string $table = ""): Delete
    {
        return new Delete($this->factory->delete($table));
    }
}
