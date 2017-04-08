# H5店铺登陆

## 环境说明
>此登陆在于Company Module下

## 配置文件
company/config/web.php  
```php
'shopUser' => [
    'class'=> 'yii\web\User',
    'identityClass' => 'common\models\User',
    'loginUrl'=>['sign-in/login'],
    'enableAutoLogin' => true,
    'as afterLogin' => 'common\behaviors\LoginTimestampBehavior',
    'identityCookie' => ['name' => '_identity_shop', 'httpOnly' => false],
    'idParam' => '__id_shop',
    'authTimeoutParam' => '__expire_shop',
    'absoluteAuthTimeoutParam' => '__absoluteExpire_shop',
],

//start-kit 默认的配置

'user' => [
    'class'=>'yii\web\User',
    'identityClass' => 'common\models\User',
    'loginUrl'=>['/user/sign-in/login'],
    'enableAutoLogin' => true,
    'as afterLogin' => 'common\behaviors\LoginTimestampBehavior'
]

```

