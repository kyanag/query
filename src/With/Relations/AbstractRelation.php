<?php

namespace Kyanag\Query\With\Relations;

use Kyanag\Query\Query\SelectQuery;
use Kyanag\Query\With\RelationInterface;

/**
 * 单表单字段关联
 * @mixin SelectQuery
 */
abstract class AbstractRelation implements RelationInterface
{
    /**
     * 主表主键键名
     * @var string
     */
    protected string $column;

    /**
     * 附表表名
     * @var string|mixed|null
     */
    protected string $foreign_table;

    /**
     * 附表外键键名
     * @var string
     */
    protected string $foreign_field;

    /**
     * @var SelectQuery
     */
    protected SelectQuery $query;


    /**
     * 附表的要加载的关联
     * @var array
     */
    protected array $relations = [];


    /**
     * @param SelectQuery $query query
     * @param string $column 主表主键名
     * @param string $field 附表外键名
     * @param string $table 附表表名
     */
    public function __construct(SelectQuery $query, string $column, string $table, string $field)
    {
        $this->column = $column;
        $this->foreign_table = $table;
        $this->foreign_field = $field;

        $this->query = $query->table($table);
    }


    public function with(array $relations): self
    {
        $this->relations = array_replace($this->relations, $relations);
        return $this;
    }

    public function getRelations(): array
    {
        return $this->relations;
    }


    abstract public function match($records, $foreigners, $relation_name): array;


    public function fetchData(array $records): array
    {
        $foreign_values = array_column($records, $this->column);
        return $this->query->whereIn($this->foreign_field, $foreign_values)->get();
    }


    public function __call($name, $arguments)
    {
        call_user_func_array([$this->query, $name], $arguments);
        return $this;
    }


    public function get()
    {
        throw new \BadMethodCallException("关联定义时不支持调用 [" . __METHOD__ . "] 方法");
    }

    public function first()
    {
        throw new \BadMethodCallException("关联定义时不支持调用 [" . __METHOD__ . "] 方法");
    }

    public function find()
    {
        throw new \BadMethodCallException("关联定义时不支持调用 [" . __METHOD__ . "] 方法");
    }
}
