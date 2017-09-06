# redis

# session保存到redis

## composer安装redis
```
composer require --prefer-dist yiisoft/yii2-redis
```

## redis配置
**common/config/web.php**
```php
'components' => [
    'session' => [
        'class' => 'yii\redis\Session',
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 1,
            //'unixSocket' => '/var/run/redis/redis.sock',
            'password'  => '1234567',
            // 'unixsocket' => '/var/run/redis/redis.sock',
            //  'unixSocket' => '/tmp/redis.sock',
        ],
    ],
]
```


## 缓存保存到redis

```php
return [
    'components' => [
       
    'session' => [
      //'class' => 'yii\mongodb\Session',
      'class' => 'yii\redis\Session',
      //'db' => 'mongodb',
      //'sessionCollection' => 'session',
      'timeout' => 6000,
    ],
    
    'cache' => [
            'class' => 'yii\redis\Cache',
      //'class' => 'yii\caching\FileCache',  
      'keyPrefix' => 'rediscache-##$fdas5ygjD',
        ],

```