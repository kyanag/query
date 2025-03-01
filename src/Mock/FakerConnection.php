<?php

namespace Kyanag\Query\Mock;

use Kyanag\Query\Connection;
use Kyanag\Query\Interfaces\ConnectionInterface;

class FakerConnection extends Connection
{

    protected array $queries = [];



    public function __construct()
    {
        //parent::__construct($pdo);
    }


    /**
     * @param string $sql
     * @param array $params
     * @return int
     */
    public function exec(string $sql, array $params = []): int
    {
        $this->_LogQuery($sql, $params);
        return 0;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return mixed|void
     */
    public function select(string $sql, array $params = [])
    {
        $this->_LogQuery($sql, $params);
        return [];
    }
}