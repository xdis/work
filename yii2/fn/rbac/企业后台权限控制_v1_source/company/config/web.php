<?php
$params = array_merge(
		require(__DIR__ . '/params.php')
);

$config = [
    'homeUrl'=>Yii::getAlias('@companyUrl'),
    'controllerNamespace' => 'company\controllers',
    'defaultRoute'=>'index/index',
    'controllerMap'=>[
        'file-manager-elfinder' => [
            'class' => 'mihaildev\elfinder\Controller',
            'access' => ['manager'],
            'disabledCommands' => ['netmount'],
            'roots' => [
                [
                    'baseUrl' => '@storageUrl',
                    'basePath' => '@storage',
                    'path'   => '/',
                    'access' => ['read' => 'manager', 'write' => 'manager']
                ]
            ]
        ]
    ],
    'components'=>[
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'request' => [
            'cookieValidationKey' => env('COMPANY_COOKIE_VALIDATION_KEY')
        ],
        'user' => [
            'class'=>'company\components\CompanyUser',
            'identityClass' => 'common\models\User',
            'loginUrl'=>['sign-in/login'],
            'enableAutoLogin' => false,
            'as afterLogin' => 'common\behaviors\LoginTimestampBehavior'
        ],
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
        'RbacManager' => [
          'class' => 'company\components\RbacManager',
        ],
    ],
    'modules'=>[
        'ucenter' => [
            'class' => 'company\modules\ucenter\Module',
        ],
        'i18n' => [
            'class' => 'company\modules\i18n\Module',
            'defaultRoute'=>'i18n-message/index'
        ],
        'lookup' => [
            'class' => 'company\modules\lookup\Module',
        ],
        'admin' => [
            'class' => 'company\modules\admin\Module',
        ],
        'car' => [
            'class' => 'company\modules\car\Module',
        ],
    	'driver' => [
    		'class' => 'company\modules\driver\Module',
    	],
    	'coshop' => [
    		'class' => 'company\modules\coshop\Module',
    	],
        'shop' => [
            'class' => 'company\modules\shop\Module',
        ],

    ],
    'as globalAccess'=>[
        'class'=>'\common\behaviors\GlobalAccessBehavior',
        'accessControlFilter' => 'common\filters\UrlAccessFilter',
        'rules'=>[
            [
                'controllers'=>['sign-in'],
                'allow' => true,
                'roles' => ['?', '@'],
            ],
            [
                'controllers'=>['site'],
                'allow' => true,
                'roles' => ['?', '@'],
                'actions'=>['error']
            ],
            [
                'controllers'=>['payment'],
                'allow' => true,
                'roles' => ['?', '@'],
            ],
        	[
        		'controllers'=>['binding'],
        		'allow' => true,
        		'roles' => ['?', '@'],
        	],
            [
                'controllers'=>['debug/default'],
                'allow' => true,
                'roles' => ['?'],
            ], 
            [
                'controllers'=>['shop/login'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
                'controllers'=>['shop/product'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
                'controllers'=>['shop/site'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
                'controllers'=>['shop/order'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
                'controllers'=>['shop/return'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
                'controllers'=>['coshop/default'],
                'allow' => true,
                'roles' => ['?', '@'],
            ],
            [
                'controllers'=>['coshop/product-list'],
                'allow' => true,
                'roles' => ['?', '@'],
            ],
            [
                'controllers'=>['shop/order-store'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
                'controllers'=>['shop/one-card-pass'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
                'controllers'=>['user'],
                'allow' => true,
                'roles' => ['administrator'],
            ],
            [
                'controllers'=>['user'],
                'allow' => false,
            ],
/*            [
                'allow' => true,
                'roles' => ['@'],
            ]*/
        ]
    ],
    'as theme' => [
        'class' => 'common\behaviors\ThemeBehavior',
    ],
    'params' => $params
];

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'class'=>'yii\gii\Module',
        'allowedIPs' => ['*'],
        'generators' => [
            'crud' => [
                'class'=>'yii\gii\generators\crud\Generator',
                'templates'=>[
                    'yii2-starter-kit' => Yii::getAlias('@company/views/_gii/templates')
                ],
                'template' => 'yii2-starter-kit',
                'messageCategory' => 'company'
            ]
        ]
    ];
}

return $config;
