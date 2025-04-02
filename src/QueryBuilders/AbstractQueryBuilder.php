<?php

namespace Kyanag\Query\QueryBuilders;

use Kyanag\Query\Interfaces\QueryBuilderInterface;

use function Latitude\QueryBuilder\alias;

/**
 * @property \Latitude\QueryBuilder\Query\AbstractQuery $query
 */
abstract class AbstractQueryBuilder implements QueryBuilderInterface
{
    /**
     * @return \Latitude\QueryBuilder\Query\AbstractQuery
     */
    public function toQuery()
    {
        return $this->query;
    }


    /**
     * @return array
     */
    public function toSql(): array
    {
        $query = $this->query->compile();
        return [$query->sql(), $query->params()];
    }


    protected function _FormatField($field)
    {
        if (is_string($field)) {
            $result = preg_split("/\s+as\s+/i", $field);
            if (count($result) == 2) {
                return alias($result[0], $result[1]);
            }
        }
        return $field;
    }

    protected function _FormatTable($table)
    {
        return $this->_FormatField($table);
    }
}
