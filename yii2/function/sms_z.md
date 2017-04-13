# 短信模块分析
## sms短信_z

### sms短信_配置
common/config/base.php  
``` php
'components' => [
        'sms' => [
            'class' => 'ihacklog\sms\Sms',
            'provider' => YII_ENV_PROD ? 'Yuntongxun' : 'File', //set default provider
            'verifyTemplateId' => 150294,
            'services' => [
                'Yuntongxun' => [
                    'class' => 'ihacklog\sms\provider\Yuntongxun',
                    'apiUrl' => 'https://app.cloopen.com:8883',
//                'apiUrl' => 'https://sandboxapp.cloopen.com:8883',
                    'templateId' => ,
                    'appId' => '',
                    'accountSid' => '',
                    'accountToken' => '',
                    'softVersion' => '',
                ],
                'File' => [
                    'class' => 'ihacklog\sms\provider\File',
                    'templateId' => 1,
                ],
            ],
        ],
],
```

### sms短信_使用

```php
use ihacklog\sms\models\Sms;

public function actionCreate(){
	...
    $trance->commit();//此后需要发送短信
    $sms = new Sms();
    $sms->sendNotice(Yii::$app->request->post('contact_phone'), [''], 1111);
    ...
        
}

```