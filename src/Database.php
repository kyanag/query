<?php

namespace Kyanag\Query;

use Kyanag\Query\Interfaces\ConnectionInterface;
use Latitude\QueryBuilder\Engine\MySqlEngine;
use function Latitude\QueryBuilder\literal;

/**
 * @method mixed getLastQuery() //获取上一次执行的sql
 * @method array getQueries()   //获取所有sql
 * @method array select(string $query, array $params = [])       //原生执行查询
 * @method int exec(string $query, array $params = [])          //原生执行语句
 * @method void beginTransaction()  //开启事务
 * @method void commit()        //提交事务
 * @method void rollback()      //回滚事务
 * @method mixed transaction(\Closure $callable, int $retry = 1)    //laravel 风格的闭包事务
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