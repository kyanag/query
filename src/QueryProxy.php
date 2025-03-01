<?php

namespace Kyanag\Query;

use Kyanag\Query\Adapters\Delete;
use Kyanag\Query\Adapters\Insert;
use Kyanag\Query\Adapters\Select;
use Kyanag\Query\Adapters\Update;
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


    public function __construct(QueryFactory $factory)
    {
        $this->queryFactory = $factory;
    }

    /**
     * @param array $values
     * @return mixed
     * @throws \Exception
     */
    public function update(array $values)
    {
        $query = $this->makeQuery("update");
        return $query->update($values);
    }


    public function table($table): self
    {
        $this->table = $table;
        return $this;
    }


    /**
     * @param ...$columns
     * @return Select
     * @throws \Exception
     */
    public function select(...$columns)
    {
        $query = $this->makeQuery("select");
        $query->select(...$columns);
        return $query;
    }

    /**
     * @throws \Exception
     */
    public function get()
    {
        $query = $this->makeQuery("select");
        return $query->get();
    }


    /**
     * @return mixed
     * @throws \Exception
     */
    public function first()
    {
        $query = $this->makeQuery("select");
        return $query->first();
    }

    /**
     * @param mixed $value
     * @param string|array $field
     * @return mixed
     * @throws \Exception
     */
    public function find($value, $field = "id")
    {
        $query = $this->makeQuery("select");
        return $query->where($field, $value)->first();
    }


    public function delete()
    {
        $query = $this->makeQuery("delete");
        return $query->delete();
    }

    /**
     * @param array $values
     * @return bool|int
     * @throws \Exception
     */
    public function insert(array $values)
    {
        $query = $this->makeQuery("insert");

        $first = array_first($values);
        $is_multi = is_array($first);
        if($is_multi) {
            return $query->insertAll($values);
        }else{
            return $query->insert($values);
        }
    }

    public function __call($name, $arguments)
    {
        $this->callables[] = [$name, $arguments];
        return $this;
    }


    /**
     * @param $type
     * @param $use
     * @return Delete|Insert|Select|Update
     * @throws \Exception
     */
    protected function makeQuery($type, $use = true)
    {
        switch ($type){
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
        if($this->table){
            $query->table($this->table);
        }
        if($use){
            foreach ($this->callables as $callable){
                list($method, $args) = $callable;
                $query = $query->{$method}(...$args) ?: $query;
            }
            $this->callables = [];
        }
        return $query;
    }
}