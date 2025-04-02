<?php

use Kyanag\Query\Database;
use Kyanag\Query\DatabaseFactory;

include "./vendor/autoload.php";


$pdo = new PDO("mysql:host=localhost;dbname=test", "root", "");
$database = DatabaseFactory::create($pdo);

/**
 * @sql SELECT * FROM `users` WHERE `id` IN (SELECT `user_id` FROM `admins` WHERE `status` = '1') AND (`name` = '张三' OR `nickname` = '张三') LIMIT 1
 */
$query = $database->query()
    ->table("users")
    ->whereIn("id", function () use ($database) {
        return $database->query()
            ->table("admins")
            ->select("user_id")
            ->where("status", 1);
    })
    ->where(function ($query) {
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


$users = $database->table("user")
    ->select("id", "name", "p_id", "com_id")
    ->where("com_id", "COM_ID")
    ->where("status", 1)
    ->limit(3)
    ->get();
/**
 * 关联模型
 */
/** @var array $users */
$users = $database->load($users, [
    //一对一
    'com' => $database->hasOne("com_id", "com.id"),
    //一对多
    'sub_users' => $database->hasMany("id", "users.p_id"),
    //远程一对多
    'members' => $database->belongsToMany("id", "members.id", "user_id=member_id")
    //'members' => $database->belongsToMany("id", "members.id", ['user_id', 'member_id'])
]);

/**
 * 支持的 Select 后续调用
 * @see \Kyanag\Query\QueryBuilders\SelectBuilder
 * @see \Kyanag\Query\Query\SelectQuery
 */
$users = $database->load($users, [
    //一对一
    'com' => $database->hasOne("com_id", "com.id")->where("status", 1)->orderBy("id", "desc"),
    //一对多
    'sub_users' => $database->hasMany("id", "users.p_id"),
    //远程一对多
    'members' => $database->belongsToMany("id", "members.id", "user_id=member_id")
    //'members' => $database->belongsToMany("id", "members.id", ['user_id', 'member_id'])
]);
