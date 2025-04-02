<?php

namespace Kyanag\Query\With\Relations;

use Kyanag\Query\Query\SelectQuery;

use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\identify;

class BelongsToMany extends AbstractRelation
{
    /**
     * @var SelectQuery
     */
    protected SelectQuery $query;

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
     * 中间表表名
     * @var string
     */
    protected string $link_table;


    /**
     * 中间表字段信息 [0]主表主键在中间表对应的字段名 [1]附表外键在中间表对应的字段名
     * @var array
     */
    protected array $link_fields;


    /**
     * 中间表关联字段查询结果的最终下标名称
     * @var array
     */
    protected array $link_as_columns;


    /**
     * @param SelectQuery $query query
     * @param string $column 主表主键名
     * @param string $field 附表外键名
     * @param string $table 附表表名
     * @param string $link_table 中间表表名
     * @param array $link_columns 中间表关联字段 [0]主键对应中间表字段 [1]附表对应中间表字段
     */
    public function __construct(
        SelectQuery $query,
        string $column,
        string $field,
        string $table,
        string $link_table,
        array $link_columns
    ) {
        parent::__construct($query, $column, $field, $table);

        $this->link_table = $link_table;
        $this->link_fields = $link_columns;
    }


    public function withLinkAsColumns(array $columns)
    {
        if (count($columns) == 2) {
            $this->link_as_columns = $columns;
        }
        throw new \Exception("");
    }


    public function fetchData(array $records): array
    {
        if (count($records) == 0) {
            return [];
        }
        //预先生成为Join结构
        $this->query = $this->buildQuery();

        $values = array_column($records, $this->column);

        list($pk_in_link) = $this->link_fields;
        $link_table = $this->link_table;
        $full_pk_in_link = identify("{$link_table}.{$pk_in_link}");

        return $this->query->whereIn($full_pk_in_link, $values)->get();
    }



    protected function buildQuery(): SelectQuery
    {
        $primary_field = $this->column;
        $foreign_field = $this->foreign_field;

        if (count($this->link_as_columns) == 0) {
            $this->link_as_columns = [
                "pivot_{$primary_field}",
                "pivot_{$foreign_field}"
            ];
        }
        //中间表的查询数组结果字段
        list($pk_in_link_as, $fk_in_link_as) = $this->link_as_columns;

        //中间表的数据库字段
        list($pk_in_link, $fk_in_link) = $this->link_fields;
        $link_table = $this->link_table;

        $foreign_table = $this->foreign_table;

        //带表名的字段名
        $full_foreign_field = identify("{$foreign_table}.{$this->foreign_field}");
        $full_pk_in_link = identify("{$link_table}.{$pk_in_link}");
        $full_fk_in_link = identify("{$link_table}.{$fk_in_link}");

        $columns = [
            identify("{$foreign_table}.*"),
            alias($full_pk_in_link, $pk_in_link_as),
            alias($full_fk_in_link, $fk_in_link_as),
        ];

        return $this->query->table($full_foreign_field)
            ->join($link_table, $full_foreign_field, $full_fk_in_link)
            ->select($columns);
    }


    /**
     * @noinspection DuplicatedCode
     */
    public function match($records, $foreigners, $relation_name): array
    {
        list($pk_in_link_as, $fk_in_link_as) = $this->link_as_columns;

        $foreigners = array_group($foreigners, $pk_in_link_as);

        $as_key = $relation_name;
        $id_key = $this->column;

        $default = [];
        return array_map(function ($record) use ($foreigners, $as_key, $id_key, $default) {
            $record_id = $record[$id_key];

            $record[$as_key] = $default;
            if (isset($foreigners[$record_id])) {
                $record[$as_key] = $foreigners[$record_id];
            }
            return $record;
        }, $records);
    }
}
