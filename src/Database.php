<?php

namespace Kyanag\Query;

use Kyanag\Query\Interfaces\ConnectionInterface;
use Latitude\QueryBuilder\Engine\MySqlEngine;
use function Latitude\QueryBuilder\literal;

/**
 * @method mixed getLastQuery()
 * @method array getQueries()
 */
class Database
{


    /**
     * @var QueryFactory
     */
    protected QueryFactory $queryFactory;


    protected ConnectionInterface $connection;



    protected bool $queryLogging = false;


    /** @var array */
    protected array $sqls = [];


    public function __construct(ConnectionInterface $connection, QueryFactory $factory)
    {
        $this->queryFactory = $factory;
        $this->connection = $connection;
    }


    /**
     * @return QueryProxy
     */
    public function query($type = null): QueryProxy
    {
        return new QueryProxy($this->queryFactory);
    }


    /**
     * @return QueryFactory
     */
    public function queryFactory(): QueryFactory
    {
        return $this->queryFactory;
    }


    /**
     * @param $value
     * @return \Latitude\QueryBuilder\StatementInterface
     */
    public static function raw($value)
    {
        return literal($value);
    }


    public function __call($method, $params = [])
    {
        return $this->connection->{$method}(...$params);
    }
}