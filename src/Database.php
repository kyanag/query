<?php

namespace Kyanag\Query;

use Kyanag\Query\Interfaces\ConnectionInterface;
use Kyanag\Query\Query\QueryProxy;
use Kyanag\Query\Query\SelectQuery;
use Kyanag\Query\With\RelationLoader;
use Kyanag\Query\With\RelationLoaderInterface;
use Kyanag\Query\With\Relations\BelongsToMany;
use Kyanag\Query\With\Relations\HasMany;
use Kyanag\Query\With\Relations\HasOne;

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


    protected RelationLoaderInterface $relationLoader;


    public function __construct(ConnectionInterface $connection, QueryFactory $factory)
    {
        $this->queryFactory = $factory;
        $this->connection = $connection;

        $this->relationLoader = new RelationLoader($connection);
    }


    /**
     * @return QueryProxy
     */
    public function query($type = null): QueryProxy
    {
        return new QueryProxy($this->queryFactory, $this->connection);
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


    public function table(string $table): QueryProxy
    {
        return $this->query()->table($table);
    }



    public function setRelationLoader(RelationLoaderInterface $relationLoader)
    {
        $this->relationLoader = $relationLoader;
    }


    public function load(array $items, array $relations)
    {
        return $this->relationLoader->load($items, $relations);
    }


    /**
     * 一对一
     * @param string $column    关联字段名
     * @param string $field     附键字段名(使用 user.id 时无需传附表表名)
     * @param string|null $table    附表表名
     * @return HasOne
     * @throws \Exception
     */
    public function hasOne(string $column, string $field, string $table = null)
    {
        if ($table === null) {
            list($table, $field) = explode('.', $field . ".");
        }
        if (!$field) {
            throw new \Exception("缺少 附表外键键名");
        }
        if (!$table) {
            throw new \Exception("缺少 附表表名");
        }
        $query = new SelectQuery($this->queryFactory->createSelect($table), $this->connection);
        return new HasOne($query, $column, $table, $field);
    }

    /**
     * 一对多
     * @param string $column    关联字段名
     * @param string $field     附键字段名(使用 user.id 时无需传附表表名)
     * @param string|null $table    附表表名
     * @return HasMany
     * @throws \Exception
     */
    public function hasMany(string $column, string $field, string $table = null)
    {
        if ($table === null) {
            list($table, $field) = explode('.', $field . ".");
        }
        if (!$field) {
            throw new \Exception("缺少 附表外键键名");
        }
        if (!$table) {
            throw new \Exception("缺少 附表表名");
        }
        $query = new SelectQuery($this->queryFactory->createSelect($table), $this->connection);
        return new HasMany($query, $column, $table, $field);
    }

    /**
     * 远程一对多
     * @param string $column 关联字段名
     * @param string $field 附键字段名（需要带上表名 user.id ）
     * @param string $link_table 关联关系表表名
     * @param array|string $link_columns 关联关系字段名 [0] A表主键对应字段名 [1]B表主键对应字段名 （也支持 A_FIELD=B_FIELD 此类写法）
     * @return BelongsToMany
     */
    public function belongsToMany(
        string $column,
        string $field,
        string $link_table,
        $link_columns = []
    ) {
        list($table, $field) = explode('.', $field . ".");
        if (is_string($link_columns)) {
            $link_columns = array_values(
                array_map("trim", explode("=", $link_columns))
            );
        }

        $query = new SelectQuery($this->queryFactory->createSelect($table), $this->connection);
        return new BelongsToMany($query, $column, $field, $table, $link_table, $link_columns);
    }
}
