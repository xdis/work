
##路由设置-伪静态
```php
return [
    'class'=>'yii\web\UrlManager',
    'enablePrettyUrl'=>true,
    'showScriptName'=>false,
    'rules'=>[
        // url rules
        [
            'pattern' => 'coshop/<shop_id:\d+>/<type:\d+>',
            'route' => 'coshop/default/index',
            'defaults' => ['type' => 4],
        ],
    ]
];



```