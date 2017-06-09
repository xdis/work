# function

**vendor/yiisoft/yii2/helpers/BaseArrayHelper.php**

# ArrayHelper

## ArrayHelper::merge

```php
  //例A
    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'oauth_create' => [
                    'oauth_client', 'oauth_client_user_id', 'email', 'username', '!status'
                ]
            ]
        );
    }

```
```php

  //例B
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
//                [['reception_status','trip_status'],'in', 'range' => [1, 2]],
                [
                    ['revoke_reason'],
                    'filter',
                    'filter' => function ($value) {
                        return \Yii::$app->formatter->asHtml($value);
                    }
                ],

                [['reception_status', 'trip_status'], 'filter', 'filter' => 'intval', 'skipOnArray' => true]
                //['customer_pay_amount', 'default', 'value' => 0.00],
                //['customer_pay_amount','compare','compareValue' => 0,'operator' => '>','message'=>'客户支付的值必须大于']
            ]
        );

    }
```
```php

  //例C

    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'updateStatus' => ['reception_status','revoke_reason','trip_status','customer_pay_amount'],
            ]
        );
    }
```

## ArrayHelper::map

```php
$array = [
    ['id' => '123', 'name' => 'aaa', 'class' => 'x'],
    ['id' => '124', 'name' => 'bbb', 'class' => 'x'],
    ['id' => '345', 'name' => 'ccc', 'class' => 'y'],
];
     *
$result = ArrayHelper::map($array, 'id', 'name');
// the result is:
// [
//     '123' => 'aaa',
//     '124' => 'bbb',
//     '345' => 'ccc',
// ]
     *
$result = ArrayHelper::map($array, 'id', 'name', 'class');
// the result is:
// [
//     'x' => [
//         '123' => 'aaa',
//         '124' => 'bbb',
//     ],
//     'y' => [
//         '345' => 'ccc',
//     ],
// ]
```

## 配置多个数据库_lh
common/config/web.php    
```php
$config = [
    'components' => [
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'linkAssets' => env('LINK_ASSETS'),
            'appendTimestamp' => YII_ENV_DEV
        ],
    	'db2' => [
    		'class' => 'yii\db\Connection',
    		'dsn' => 'mysql:host=192.168.1.3;dbname=vding_anyang', // Maybe other DBMS such as psql (PostgreSQL),...
    		'username' => '',
    		'password' => '',
    	] ,
    	'db3' => [
    		'class' => 'yii\db\Connection',
    		'dsn' => 'mysql:host=192.168.1.7;dbname=vding_anyang_test', // Maybe other DBMS such as psql (PostgreSQL),...
    		'username' => '',
    		'password' => '',
    	] ,
    ],
];
```


