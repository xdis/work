## user组件定义

### company模块里包含"企业用户"与小模块里的"H5店铺"两个用户  
>company模块里,包含一个小模块shop,原本一个模块里只有一个用户的,但是由于H5店铺里的登陆是独立的,特添加一个新的用户shopUser


company/config/web.php  

```php
'components'=>[
...
 'user' => [
		//企业后台,新建一个名字,继承yii\web\User
            'class'=>'company\components\CompanyUser',
            'identityClass' => 'common\models\User',
            'loginUrl'=>['sign-in/login'],
            'enableAutoLogin' => false,
            'as afterLogin' => 'common\behaviors\LoginTimestampBehavior'
        ],
		//H5店铺,使用yii2自带的
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
        'session' => [
        	'cookieParams' => [
        		'httpOnly' => false,
        	],
        ],

...
   ],
```

### ResultApi用户
rest/config/base.php  
```php
 'components' => [
...
        'user' => [
            'class' => 'rest\components\WebUser',
            'identityClass' => 'common\models\User',
            'enableSession' => false,
            'as afterLogin' => 'common\behaviors\LoginTimestampBehavior'
        ],
...
 ],
```


