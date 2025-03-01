# Kyanag/Query

一个简答的，Laravel风格的 查询构造器 和 database库

## 安装
```php
composer require kyanag/query
```

## 使用

### 创建
```php
$dsn = "";
$pdo = new \PDO($dsn);

$database = \Kyanag\Query\DatabaseFactory::create($pdo);
```

### 基础
```php
//查询
$records = $database->select("select * from users where id = ?", [1]);

//执行
$count = $database->exec("update users set status = ? where id = ?", [1, 2]);

//事务
$database->beginTransaction();
try{
    //do something
    $database->commit();
}catch (\Exception $e){
    $database->rollback();
}

//闭包事务
$retry = 3; //重试次数(默认不进行重试)
$database->transaction(function(){
    //do something
}, $retry)
```

### Where子句
```php
$query = $database->query();

//等于
$query->where("id", 1);

//比较语句
$query->where("id", ">=", 100);
$query->where("id", "<=", 100);
$query->where("id", "<>", 100);

//between
$query->whereBetween("id", [100, 200]);     //between
$query->whereNotBetween("id", [100, 200]);  //not between

//Like
$query->whereLike("name", "%张三%");  //like
$query->whereNotLike("name", "%张三%");   //not like

//Null
$query->whereNull("name");      //not null
$query->whereNotNull("name");   //not null

//WhereIn
$query->whereIn("name");      //not null
$query->whereNotIn("name");   //not null


//条件组(增加括号)
$query->where(function($query){
    return $query->where("name", $name)
        ->orWhere("nickname", $name);
});     // =>  (name = ? or nickname = ?)

//子查询
$query->whereIn("id", function() use($database){
    $database->query()->whereIn("id", function() use($database){
            //子查询
            return $database->query()
                ->table("admins")
                ->select("user_id")
                ->where("status", 1);
        })
}); // =>  `id` IN (SELECT `user_id` FROM `admins` WHERE `status` = '1')
```

### Order 、 Group 、 Limit 、 Having
```php
//Order By
$query->orderBy("id", "asc");
$query->orderBy("id", "desc");
$query->orderByRaw("id desc");

//Group
$query->groupBy("id", query_raw("from_unixtime(\"%Y\", created_at)"))

//Having(和Where子句类似)
$query->having("id", ">", 100)
$query->orHaving("id", ">", 200)
```


### Select

```php
//普通查询
$articles = $database->query()
    ->from("articles as a")
    ->join("categories as b", "a.cate_id", "b.id")
    ->select(
        "a.*",
        query_raw("from_unixtime(a.created_at, \"%Y-%m-%d\") as create_date"),
        "b.title as cate_title"
    )
    ->where("a.status", 1)
    ->whereIn("b.id", [1, 2, 3])
    ->whereBetween("a.created_at", [
        strtotime("2024-01-01"),
        strtotime("2024-12-31")
    ])
    ->where("a.view_count", ">", 1000)
    ->whereLike("a.title", "%PHP%")
    ->get();


//高级用法
/**
 * @sql => SELECT * FROM `users` WHERE `id` IN (SELECT `user_id` FROM `admins` WHERE `status` = '1') AND (`name` = '张三' OR `nickname` = '张三') LIMIT 1
 */
$query = $database->query()
    ->table("users")
    ->whereIn("id", function() use($database){
        //子查询
        return $database->query()
            ->table("admins")
            ->select("user_id")
            ->where("status", 1);
    })
    ->where(function($query){
        //分组
        $name = "张三";
        return $query->where("name", $name)
            ->orWhere("nickname", $name);
    })
    ->first();
```

对应sql
```SQL
SELECT `a`.*,
         from_unixtime(a.created_at, "%Y-%m-%d") AS create_date,
         `b`.`title` AS `cate_title`
FROM `articles` AS `a`
JOIN `categories` AS `b`
    ON `a`.`cate_id` = `b`.`id`
WHERE `a`.`status` = '1'
        AND `b`.`id` IN ('1' , '2', '3')
        AND `a`.`created_at` BETWEEN '1704067200' AND '1735603200'
        AND `a`.`view_count` > '1000'
        AND `a`.`title` LIKE '%PHP%'
```

### Insert
```php
/**
 * @sql INSERT INTO `articles` (`title`, `content`, `created_at`, `view_count`) VALUES ('小米su7发布了！', '小米su7发布了！小米su7发布了！', '1740827255', '0')
 */
$database->query()
    ->table("articles")
    ->insert([
        'title' => "小米su7发布了！",
        'content' => "小米su7发布了！小米su7发布了！",
        'created_at' => time(),
        'view_count' => 0,
    ]);

//插入(批量)
/**
* @sql INSERT INTO `articles` (`title`, `content`, `created_at`, `view_count`) VALUES ('小鹏G6发布了！', '小鹏G6发布了！小鹏G6发布了！', '1740827758', '0'), ('理想L6发布了！', '理想L6发布了！', '1740827758', '0')
 */
$database->query()
    ->table("articles")
    ->insertAll([
        [
            'title' => "小鹏G6发布了！",
            'content' => "小鹏G6发布了！小鹏G6发布了！",
            'created_at' => time(),
            'view_count' => 0,
        ],
        [
            'title' => "理想L6发布了！",
            'content' => "理想L6发布了！",
            'created_at' => time(),
            'view_count' => 0,
        ]
    ]);
```

### Update
```php
/**
 * @sql UPDATE `articles` SET `view_count` = view_count + 1, `comment_count` = '3' WHERE `id` = '1'
 */
$database->query()
    ->table("articles")
    ->where("id", 1)
    ->update([
        'view_count' => query_raw("view_count + 1"),
        'comment_count' => 3,
    ]);
```

### Delete
```php
/**
 * @sql UPDATE `articles` SET `view_count` = '1' WHERE `id` = '1'
 */
$database->query()
    ->table("articles")
    ->where("id", 1)
    ->delete();

/**
 *  DELETE FROM `articles` WHERE `view_count` < '100'
 */
$database->query()
    ->table("articles")
    ->where("view_count", "<", 100)
    ->delete();
```