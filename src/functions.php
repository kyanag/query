<?php

use function Latitude\QueryBuilder\literal;

if (!function_exists("query_raw")) {
    /**
     * @param string $raw
     * @return \Latitude\QueryBuilder\StatementInterface
     */
    function query_raw(string $raw)
    {
        return literal($raw);
    }
}


if (!function_exists("array_first")) {
    function array_first(array $items)
    {
        foreach ($items as $item) {
            return $item;
        }
        return null;
    }
}
