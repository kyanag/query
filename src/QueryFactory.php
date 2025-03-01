<?php

namespace Kyanag\Query;

use Kyanag\Query\Interfaces\ConnectionInterface;
use Kyanag\Query\Adapters\Delete;
use Kyanag\Query\Adapters\Select;
use Kyanag\Query\Adapters\Update;
use Kyanag\Query\Adapters\Insert;


class QueryFactory
{

    protected ?ConnectionInterface $connection = null;


    protected \Latitude\QueryBuilder\QueryFactory $factory;


    public function __construct(\Latitude\QueryBuilder\QueryFactory $factory)
    {
        $this->factory = $factory;
    }


    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }


    /**
     * @param string $table
     * @return Update
     */
    public function createUpdate(string $table = ""): Update
    {
        return $this->tap(new Update($this->factory->update($table)), function($query){
            return $query->setConnection($this->connection);
        });
    }

    /**
     * @param string $table
     * @return Select
     */
    public function createSelect(string $table = ""): Select
    {
        return $this->tap(new Select($this->factory->select()), function($query){
            return $query->setConnection($this->connection);
        });
    }

    /**
     * @param string $table
     * @return Insert
     *@deprecated
     */
    public function createInsert(string $table = ""): Insert
    {
        return $this->tap(new Insert($this->factory->insert($table)), function($query){
            return $query->setConnection($this->connection);
        });
    }

    /**
     * @param string $table
     * @return Delete
     */
    public function createDelete(string $table = ""): Delete
    {
        return $this->tap(new Delete($this->factory->delete($table)), function($query){
            return $query->setConnection($this->connection);
        });
    }


    private function tap($value, callable $callable)
    {
        call_user_func($callable, $value);
        return $value;
    }
}