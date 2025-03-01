<?php

namespace Kyanag\Query\Adapters;

use Kyanag\Query\Interfaces\ConnectionInterface;
use function Latitude\QueryBuilder\alias;

/**
 * @property \Latitude\QueryBuilder\Query\AbstractQuery $query
 */
abstract class AbstractQuery
{

    protected ConnectionInterface $connection;


    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }


    public function asQuery()
    {
        return $this->query;
    }


    protected function _FormatField($field)
    {
        if(is_string($field))
        {
            $result = preg_split("/\s+as\s+/i", $field);
            if(count($result) == 2){
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