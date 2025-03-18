<?php

namespace Kyanag\Query;

use Kyanag\Query\Adapters\Delete;
use Kyanag\Query\Adapters\Insert;
use Kyanag\Query\Adapters\Select;
use Kyanag\Query\Adapters\Update;
use Kyanag\Query\Interfaces\ConnectionInterface;
use Kyanag\Query\Interfaces\QueryBuilderInterface;
use Latitude\QueryBuilder\StatementInterface;

/**
 * @mixin Insert
 * @mixin Update
 * @mixin Select
 * @mixin Delete
 */
class QueryProxy
{
    /**
     * @var QueryFactory
     */
    protected QueryFactory $queryFactory;


    protected array $callables = [];

    /**
     * @var string|StatementInterface
     */
    protected $table = null;


    protected ConnectionInterface $connection;


    public function __construct(QueryFactory $factory, ConnectionInterface $connection)
    {
        $this->queryFactory = $factory;
        $this->connection = $connection;
    }


    public function table($table): self
    {
        $this->table = $table;
        return $this;
    }


    /**
     * @param ...$columns
     * @return self
     * @throws \Exception
     */
    public function select(...$columns): self
    {
        $this->callables[] = [
            'select', $columns
        ];
        return $this;
    }


    /**
     * 更新
     * @param array $values
     * @return int
     * @throws \Exception
     */
    public function update(array $values): int
    {
        list($sql, $params) = $this->makeQuery("update")
            ->update($values)
            ->toSql();
        return $this->connection->exec($sql, $params) ?: 0;
    }

    /**
     * @throws \Exception
     */
    public function get()
    {
        list($sql, $params) = $this->makeQuery("select")
            ->get()
            ->toSql();
        return $this->connection->select($sql, $params);
    }


    /**
     * @return mixed
     * @throws \Exception
     */
    public function first()
    {
        list($sql, $params) = $this->makeQuery("select")
            ->first()
            ->toSql();
        $items = $this->connection->select($sql, $params);
        if (count($items) == 1) {
            return $items[0];
        }
        return null;
    }


    /**
     * @param mixed $value
     * @param string|array $field
     * @return mixed
     * @throws \Exception
     */
    public function find($value, $field = "id")
    {
        list($sql, $params) = $this->makeQuery("select")
            ->find($value, $field)
            ->toSql();

        $items = $this->connection->select($sql, $params);
        if (count($items) == 1) {
            return $items[0];
        }
        return null;
    }


    /**
     * @return int
     * @throws \Exception
     */
    public function delete(): int
    {
        list($sql, $params) = $this->makeQuery("delete")
            ->delete()
            ->toSql();

        return $this->connection->exec($sql, $params) ?: 0;
    }


    /**
     * @param array $values
     * @return bool|int
     * @throws \Exception
     */
    public function insert(array $values)
    {
        list($sql, $params) = $this->makeQuery("insert")
            ->insert($values)
            ->toSql();

        return $this->connection->exec($sql, $params);
    }


    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        $this->callables[] = [$name, $arguments];
        return $this;
    }


    /**
     * @param $type
     * @param bool $use
     * @return QueryBuilderInterface
     * @throws \Exception
     */
    protected function makeQuery($type, bool $use = true): QueryBuilderInterface
    {
        switch ($type) {
            case "update":
                $query = $this->queryFactory->createUpdate();
                break;
            case "select":
                $query = $this->queryFactory->createSelect();
                break;
            case "delete":
                $query = $this->queryFactory->createDelete();
                break;
            case "insert":
                $query = $this->queryFactory->createInsert();
                break;
            default:
                throw new \Exception("query:{$type} exists!");
        }
        if ($this->table) {
            $query->table($this->table);
        }
        if ($use) {
            foreach ($this->callables as $callable) {
                list($method, $args) = $callable;
                $query = $query->{$method}(...$args) ?: $query;
            }
            $this->callables = [];
        }
        return $query;
    }
}
