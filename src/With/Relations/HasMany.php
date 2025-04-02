<?php

namespace Kyanag\Query\With\Relations;

class HasMany extends AbstractRelation
{
    /**
     * @noinspection DuplicatedCode
     */
    public function match($records, $foreigners, $relation_name): array
    {
        $foreigners = array_group($foreigners, $this->foreign_field);

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
