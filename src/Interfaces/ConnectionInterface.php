<?php

namespace Kyanag\Query\Interfaces;

interface ConnectionInterface
{
    /**
     * update/delete/insert
     * @param string $sql
     * @param array $params
     * @return int
     */
    public function exec(string $sql, array $params = []);


    /**
     * 查询接口
     * @param string $sql
     * @param array $params
     * @return mixed
     */
    public function select(string $sql, array $params = []);
}
