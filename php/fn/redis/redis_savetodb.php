<?php

//首先呢，我要加载一下redis组件，
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->name = "miaosha";
for ($i = 0; $i < 100; $i++) {
    $uid = rand(100000, 999999);
//接受用户的id,
//$uid = $_GET[，uid.]:
//获取一下reis里面己有的数量，
    $num = 10;
//如果当天人数少于十的时候，则加入这个队列，
    if ($redis->lLen($redis_name) < 10) {
        $redis->rPush($redis_name, $uid . '%' . microtime());
        echo $uid . "秒杀成功";
    } else {
//如果当天人数己经达到了十个人，则返回秒杀己完成，
        echo "秒杀己结束";
    }
}
$redis->close();


