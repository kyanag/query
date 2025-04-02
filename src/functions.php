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


if (!function_exists("array_group")) {
    function array_group(array $items, $keyGetter): array
    {
        if (is_string($keyGetter)) {
            $keyGetter = function ($item) use ($keyGetter) {
                return $item[$keyGetter];
            };
        }

        $res = [];
        foreach ($items as $index => $item) {
            $key = call_user_func($keyGetter, $item, $index);
            if (!isset($res[$key])) {
                $res[$key] = [];
            }
            $res[$key][] = $item;
        }
        return $res;
    }
}
