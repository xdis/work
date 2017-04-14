# module

## 短信sms配置代码采集

### components配置参数
```php
'components' => [
...
 'sms' => [
            'class' => 'ihacklog\sms\Sms',
            'provider' => YII_ENV_PROD ? 'Yuntongxun' : 'File', //set default provider
            'verifyTemplateId' => 150294,
            'services' => [
                'Yuntongxun' => [
                    'class' => 'ihacklog\sms\provider\Yuntongxun',
                    'apiUrl' => 'https://app.cloopen.com:8883',
//                'apiUrl' => 'https://sandboxapp.cloopen.com:8883',
                    'templateId' => 150294,
                    'appId' => '8a216da856c131340156d3ff1bb60d47',
                    'accountSid' => '8a216da856c131340156d3ff1b280d40',
                    'accountToken' => '9068a167e6254ae49fbf516e0a3dfffe',
                    'softVersion' => '2013-12-26',
                ],
                'File' => [
                    'class' => 'ihacklog\sms\provider\File',
                    'templateId' => 1,
                ],
            ],
        ],
],
...

``` 
### modules配置参数
```php
'modules' => [
...
'sms' => [
            'class' => 'ihacklog\sms\Module',
            'userModelClass' => '\common\models\User', // optional. your User model. Needs to be ActiveRecord.
            'resendTimeSpan' => YII_ENV_PROD ? 60 : 10, //重发时间间隔(单位：秒）
            'singleIpTimeSpan' => YII_ENV_PROD ? 3600 : 0, //单个ip用于统计允许发送的最多次数的限定时间
            'singleIpSendLimit' => YII_ENV_PROD ? 20 : 0, //单个ip在限定的时间内允许发送的最多次数
            'verifyTimeout' => 300, //验证码超时(秒)
            'enableHttpsCertVerify' => YII_ENV_PROD ? true : false, //是否校验https证书,线上环境建议启用
        ],
...

],

```