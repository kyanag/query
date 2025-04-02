<?php

namespace Kyanag\Query\With;

interface RelationInterface
{
    /**
     * 组装关联数据
     * @param $records
     * @param $foreigners
     * @param $relation_name
     * @return array
     */
    public function match($records, $foreigners, $relation_name): array;


    /**
     * @param array $records
     * @return array
     */
    public function fetchData(array $records): array;


    /**
     * @return array
     */
    public function getRelations(): array;
}
