<?php

namespace Kyanag\Query;

use Kyanag\Query\Interfaces\ConnectionInterface;

class Connection implements ConnectionInterface
{
    protected array $queries = [];

    /**
     * @var \PDO
     */
    protected \PDO $pdo;


    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * update/delete/insert
     * @param string $sql
     * @param array $params
     * @return int
     */
    public function exec(string $sql, array $params = []): int
    {
        $this->_LogQuery($sql, $params);

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->rowCount();
    }


    /**
     * 查询接口
     * @param string $sql
     * @param array $params
     * @return mixed
     */
    public function select(string $sql, array $params = [])
    {
        $this->_LogQuery($sql, $params);

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }


    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }


    public function commit()
    {
        $this->pdo->commit();
    }


    public function rollback()
    {
        $this->pdo->rollBack();
    }


    /**
     * @param callable $callable
     * @param int $retry 重试次数
     * @return mixed
     * @throws \Throwable
     */
    public function transaction(callable $callable, int $retry = 1)
    {
        $retry = max($retry, 1);

        $exception = null;
        for ($i = 0; $i < $retry; $i++) {
            $this->beginTransaction();
            try {
                $res = call_user_func($callable, $this);
                $this->commit();

                return $res;
            } catch (\Throwable $e) {
                $exception = $e;
                $this->rollback();
            }
        }
        throw $exception;
    }



    /**
     * @param string $sql
     * @param array $params
     * @return string
     */
    protected function _Format(string $sql, array $params = []): string
    {
        $sql = str_replace("%", "%%", $sql);
        $sql = str_replace("?", "'%s'", $sql);
        //var_dump($sql, $params);exit();
        return vsprintf($sql, $params);
    }


    protected function _LogQuery(string $sql, array $params = [])
    {
        $this->queries[] = [
            $sql,
            $params,
            $this->_Format($sql, $params)
        ];
    }


    public function getLastQuery()
    {
        if (count($this->queries)) {
            return $this->queries[count($this->queries) - 1];
        }
        return null;
    }


    /**
     * @return array
     */
    public function getQueries(): array
    {
        return $this->queries;
    }
}
