<?php

include "./vendor/autoload.php";

//$dsn = "";
//$pdo = new \PDO($dsn);
//$database = \Kyanag\Query\DatabaseFactory::create($pdo);


//Mock
$factory = new \Latitude\QueryBuilder\QueryFactory(new \Latitude\QueryBuilder\Engine\MySqlEngine());
$connection = new \Kyanag\Query\Mock\FakerConnection();
$queryFactory = new \Kyanag\Query\QueryFactory($factory);
$queryFactory->setConnection($connection);
$database = new \Kyanag\Query\Database($connection, $queryFactory);

/**
 * @sql => SELECT * FROM `users` WHERE `id` IN (SELECT `user_id` FROM `admins` WHERE `status` = '1') AND (`name` = '张三' OR `nickname` = '张三') LIMIT 1
 */
$query = $database->query()
    ->table("users")
    ->whereIn("id", function() use($database){
        return $database->query()
            ->table("admins")
            ->select("user_id")
            ->where("status", 1);
    })
    ->where(function($query){
        $name = "张三";
        return $query->where("name", $name)
            ->orWhere("nickname", $name);
    })
    ->orderByRaw("id desc")
    ->groupBy("id", query_raw("from_unixtime(\"%Y\", created_at)"))
    ->havingRaw("count(*) > ?", [2])
    ->having("id", 100)
    ->first();

$query = $database->query();
$query->orderBy("id", "asc");
$query->orderBy("id", "desc");


$sqls = $database->getQueries();
foreach ($sqls as $sql){
    var_dump($sql[2]);
}