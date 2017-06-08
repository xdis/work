<?php
$config = [
    'homeUrl'=>Yii::getAlias('@companyUrl'),
    'controllerNamespace' => 'company\controllers',
    'defaultRoute' => 'site/index',
    'bootstrap' => ['maintenance'],
    'modules' => [
        'user' => [
            'class' => 'company\modules\user\Module',
            //'shouldBeActivated' => true
        ],
        'shop' => [
            'class' => 'company\modules\shop\Module',
        ],
        'report' => [
            'class' => 'company\modules\report\Module',
            //'shouldBeActivated' => true
        ],
        'api' => [
            'class' => 'company\modules\api\Module',
            'modules' => [
                'v1' => 'company\modules\api\v1\Module'
            ]
        ]
    ],
    'components' => [
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'github' => [
                    'class' => 'yii\authclient\clients\GitHub',
                    'clientId' => env('GITHUB_CLIENT_ID'),
                    'clientSecret' => env('GITHUB_CLIENT_SECRET')
                ],
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => env('FACEBOOK_CLIENT_ID'),
                    'clientSecret' => env('FACEBOOK_CLIENT_SECRET'),
                    'scope' => 'email,public_profile',
                    'attributeNames' => [
                        'name',
                        'email',
                        'first_name',
                        'last_name',
                    ]
                ]
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'site/error'
        ],
        'maintenance' => [
            'class' => 'common\components\maintenance\Maintenance',
            'enabled' => function ($app) {
                return $app->keyStorage->get('company.maintenance') === 'enabled';
            }
        ],
        'request' => [
             'baseUrl' => '',
            'cookieValidationKey' => env('COMPANY_COOKIE_VALIDATION_KEY')
        ],
        'user' => [
            'class'=>'yii\web\User',
            'identityClass' => 'common\models\User',
            'loginUrl'=>['/user/sign-in/login'],
            'enableAutoLogin' => true,
            'as afterLogin' => 'common\behaviors\LoginTimestampBehavior',
            'identityCookie' => ['name' => '_identity_company', 'httpOnly' => false],
            'idParam' => '__id_company',
            'authTimeoutParam' => '__expire_company',
        ],
        'shopUser' => [
            'class'=> 'yii\web\User',
            'identityClass' => 'common\models\User',
            'loginUrl'=>['/shop/sign-in/login'],
            'enableAutoLogin' => true,
            'as afterLogin' => 'common\behaviors\LoginTimestampBehavior',
            'identityCookie' => ['name' => '_identity_shop', 'httpOnly' => false],
            'idParam' => '__id_shop',
            'authTimeoutParam' => '__expire_shop',
            'absoluteAuthTimeoutParam' => '__absoluteExpire_shop',
        ],
    ]
];

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'class'=>'yii\gii\Module',
        'generators'=>[
            'crud'=>[
                'class'=>'yii\gii\generators\crud\Generator',
                'messageCategory'=>'company'
            ]
        ]
    ];
}

return $config;
