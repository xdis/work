
##路由设置-伪静态

>例1
>访问地址： http://url/coshop/60   
>等同于访问 http://url/coshop/default/index/60/type/4

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