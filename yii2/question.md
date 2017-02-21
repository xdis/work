

##为什么日志不入数据库
在生产模式没有问题，在开发环境下有问题？  
找出的原因是在common/config/base.php插入
```php
$config['components']['log']['targets'] = [
        /**
         * 错误级别日志：当某些需要立马解决的致命问题发生的时候，调用此方法记录相关信息。
         * 使用方法：Yii::error()
         */
        [
            'class' => 'common\components\FileTarget',
            // 日志等级
            'levels' => ['error'],
            // 被收集记录的额外数据
            'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
            // 指定日志保存的文件名
            'logFile' => '@app/runtime/logs/error/app.log',
            // 是否开启日志 (@app/runtime/logs/error/20151223_app.log)
            'enableDatePrefix' => true,
        ],
        /**
         * 警告级别日志：当某些期望之外的事情发生的时候，使用该方法。
         * 使用方法：Yii::warning()
         */
        [
            'class' => 'common\components\FileTarget',
            // 日志等级
            'levels' => ['warning'],
            // 被收集记录的额外数据
            'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
            // 指定日志保存的文件名
            'logFile' => '@app/runtime/logs/warning/app.log',
            // 是否开启日志 (@app/runtime/logs/warning/20151223_app.log)
            'enableDatePrefix' => true,
        ],
        /**
         * info 级别日志：在某些位置记录一些比较有用的信息的时候使用。
         * 使用方法：Yii::info()
         */
        [
            'class' => 'common\components\FileTarget',
            // 日志等级
            'levels' => ['info'],
            // 被收集记录的额外数据
            'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
            // 指定日志保存的文件名
            'logFile' => '@app/runtime/logs/info/app.log',
            // 是否开启日志 (@app/runtime/logs/info/20151223_app.log)
            'enableDatePrefix' => true,
        ],
        /**
         * trace 级别日志：记录关于某段代码运行的相关消息。主要是用于开发环境。
         * 使用方法：Yii::trace()
         */
        [
            'class' => 'common\components\FileTarget',
            // 日志等级
            'levels' => ['trace'],
            // 被收集记录的额外数据
            'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
            // 指定日志保存的文件名
            'logFile' => '@app/runtime/logs/trace/app.log',
            // 是否开启日志 (@app/runtime/logs/trace/20151223_app.log)
            'enableDatePrefix' => true,
        ],
        [
            'class' => 'common\components\FileTarget',
            'levels' => ['info'],
            'logVars' => [], //除了except对应的分类之外，其他的都写入到
            'categories' => ['curl'],
            'logFile' => '@app/runtime/logs/curl/app.log',
            'enableDatePrefix' => true,
        ],
        [
            'class' => 'common\components\FileTarget',
            'levels' => ['profile'],
            'logVars' => [], //除了except对应的分类之外，其他的都写入到
            'categories' => ['curl'],
            'logFile' => '@app/runtime/logs/profile/app.log',
            'enableDatePrefix' => true,
        ],
    ];

```
###解决的办法  
1.将该代码删除
2.使用ArrayHelper::merge合并  
如下  
```php
 $config['components']['log']['targets'] =  \yii\helpers\ArrayHelper::merge(
        $config['components']['log']['targets'],
        [
        /**
         * 错误级别日志：当某些需要立马解决的致命问题发生的时候，调用此方法记录相关信息。
         * 使用方法：Yii::error()
         */
        [
            'class' => 'common\components\FileTarget',
            // 日志等级
            'levels' => ['error'],
            // 被收集记录的额外数据
            'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
            // 指定日志保存的文件名
            'logFile' => '@app/runtime/logs/error/app.log',
            // 是否开启日志 (@app/runtime/logs/error/20151223_app.log)
            'enableDatePrefix' => true,
        ],
        /**
         * 警告级别日志：当某些期望之外的事情发生的时候，使用该方法。
         * 使用方法：Yii::warning()
         */
        [
            'class' => 'common\components\FileTarget',
            // 日志等级
            'levels' => ['warning'],
            // 被收集记录的额外数据
            'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
            // 指定日志保存的文件名
            'logFile' => '@app/runtime/logs/warning/app.log',
            // 是否开启日志 (@app/runtime/logs/warning/20151223_app.log)
            'enableDatePrefix' => true,
        ],
        /**
         * info 级别日志：在某些位置记录一些比较有用的信息的时候使用。
         * 使用方法：Yii::info()
         */
        [
            'class' => 'common\components\FileTarget',
            // 日志等级
            'levels' => ['info'],
            // 被收集记录的额外数据
            'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
            // 指定日志保存的文件名
            'logFile' => '@app/runtime/logs/info/app.log',
            // 是否开启日志 (@app/runtime/logs/info/20151223_app.log)
            'enableDatePrefix' => true,
        ],
        /**
         * trace 级别日志：记录关于某段代码运行的相关消息。主要是用于开发环境。
         * 使用方法：Yii::trace()
         */
        [
            'class' => 'common\components\FileTarget',
            // 日志等级
            'levels' => ['trace'],
            // 被收集记录的额外数据
            'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
            // 指定日志保存的文件名
            'logFile' => '@app/runtime/logs/trace/app.log',
            // 是否开启日志 (@app/runtime/logs/trace/20151223_app.log)
            'enableDatePrefix' => true,
        ],
        [
            'class' => 'common\components\FileTarget',
            'levels' => ['info'],
            'logVars' => [], //除了except对应的分类之外，其他的都写入到
            'categories' => ['curl'],
            'logFile' => '@app/runtime/logs/curl/app.log',
            'enableDatePrefix' => true,
        ],
        [
            'class' => 'common\components\FileTarget',
            'levels' => ['profile'],
            'logVars' => [], //除了except对应的分类之外，其他的都写入到
            'categories' => ['curl'],
            'logFile' => '@app/runtime/logs/profile/app.log',
            'enableDatePrefix' => true,
        ],
    ]);

```

