<?php

namespace Kyanag\Query;

class DatabaseFactory
{
    /**
     * @param \PDO $pdo
     * @param string|null $driver_type
     * @return Database
     */
    public static function create(\PDO $pdo, string $driver_type = null): Database
    {
        if ($driver_type === null) {
            $driver_type = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
        }
        $driver_type = strtolower($driver_type);

        switch ($driver_type) {
            case "mysql":
                $factory = new \Latitude\QueryBuilder\QueryFactory(new \Latitude\QueryBuilder\Engine\MySqlEngine());
                break;
            case "sqlsrv":
                $factory = new \Latitude\QueryBuilder\QueryFactory(new \Latitude\QueryBuilder\Engine\SqlServerEngine());
                break;
            case "sqlite":
                $factory = new \Latitude\QueryBuilder\QueryFactory(new \Latitude\QueryBuilder\Engine\SqliteEngine());
                break;
            case "pgsql":
                $factory = new \Latitude\QueryBuilder\QueryFactory(new \Latitude\QueryBuilder\Engine\PostgresEngine());
                break;
            default:
                $factory = new \Latitude\QueryBuilder\QueryFactory(new \Latitude\QueryBuilder\Engine\CommonEngine());
                break;
        }
        $connection = new Connection($pdo);
        $queryFactory = new QueryFactory($factory);

        return new Database($connection, $queryFactory);
    }
}
