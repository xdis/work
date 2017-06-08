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
> 使用方式有两种 1.使用 new 2.使用 Yii::$app->sms


**frontend/controllers/DemoSmsController.php**  
```php
namespace frontend\controllers;

use ihacklog\sms\models\Sms;
use Yii;
use frontend\models\ContactForm;
use yii\web\Controller;

/**
 * Site controller
 */
class DemoSmsController extends Controller
{
    /**
     * @inheritdoc
     * 方法1 使用实例化来发送
     */
    public function actionIndex(){
        $sms = new Sms();
        $res = $sms->sendNotice('13418511035', [''], 1111);
        if($res){
            echo '发送完毕';
        }else{
            echo '发送失败';
        }
    }

    /**
     * 方法2 直接使用 Yii::$app->sms来发送，但这个会有一个问题，即，发送的认证码不会入数据库
     */
    public function actionTest(){
        $res = Yii::$app->sms->send('18899998888', ['6532','5']);
        if($res){
            echo '发送完毕';
        }else{
            echo '发送失败';
        }
    }
}


```

## 定义全局sms_actions挂裁
**GetSmsAction**
common/actions/GetSmsAction.php  
```php
<?php
/**
 * 获取短信验证码
 * Created by PhpStorm.
 * User: hacklog
 * Date: 12/18/16
 * Time: 5:11 PM
 */

namespace common\actions;

use yii\base\Action;
use yii\base\InvalidParamException;
use Yii;
use ihacklog\sms\models\Sms;

class GetSmsAction extends Action
{
    const ST_CODE_SUCC = 1;
    const ST_CODE_FAIL = 0;

    /**
     * @var string 手机号码
     */
    public $mobile;

    public $initCallback;

    /**
     * @var \Closure
     */
    public $beforeCallback;


    /**
     * @var \Closure
     */
    public $afterCallback;

    public $error = null;

    protected function formatResponse($status, $message = '', $url = '', $data = [])
    {
        return ['status' => $status, 'message' => $message, 'url' => $url, 'data' => $data];
    }

    public function run()
    {
        if ($this->initCallback && ($this->initCallback instanceof \Closure || is_callable($this->initCallback))) {
            call_user_func_array($this->initCallback, [$this]);
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $sms = new Sms();
        if (empty($this->mobile)) {
            return $this->formatResponse(self::ST_CODE_FAIL, '手机号错误', '');
        }
        $beforeCbSucc = true;
        if ($this->beforeCallback && ($this->beforeCallback instanceof \Closure || is_callable($this->beforeCallback))) {
            $beforeCbSucc = call_user_func_array($this->beforeCallback, [$this,]);
            if (!$beforeCbSucc) {
                $error = is_null($this->error) ? 'beforeCallback fail' : $this->error;
                return $this->formatResponse(self::ST_CODE_FAIL, $error, '');
            }
        }
        $sendRs = $sms->sendVerify($this->mobile, mt_rand(1000, 9999), Yii::$app->sms->verifyTemplateId);
        $afterCallbackRet = null;
        if ($this->afterCallback && ($this->afterCallback instanceof \Closure || is_callable($this->afterCallback))) {
            $afterCallbackRet = call_user_func_array($this->afterCallback, [$this, $sendRs]);
        }
        if ($afterCallbackRet) {
            return $afterCallbackRet;
        }
        $statusRet = $sendRs ? self::ST_CODE_SUCC : self::ST_CODE_FAIL;
        return $this->formatResponse($statusRet, $sms->getFirstError('id'), '', ['resendTimeSpan' => Yii::$app->getModule('sms')->resendTimeSpan ]);
    }
}
```


### 公司SignInController_z
company/controllers/SignInController.php  
```php
public function actions() {

return [
    'get-sms' => [
        'class' => GetSmsAction::className(),
//              'mobile' => Yii::$app->getRequest()->post((new SignupForm)->formName())['mobile'],
        'beforeCallback' => [$this, 'registerSmsBeforeCallback'],
        'initCallback' => [$this, 'registerSmsInitCallback'],
    ],
    'get-reset-pwd-sms' => [
        'class' => GetSmsAction::className(),
        'beforeCallback' => [$this, 'resetPwdBeforeCallback'],
        'initCallback' => [$this, 'resetSmsInitCallback'],
    ],
    'get-login-sms' => [
        'class' => GetSmsAction::className(),
        'beforeCallback' => [$this, 'loginSmsBeforeCallback'],
        'initCallback' => [$this, 'loginSmsInitCallback'],
    ],
];
}
```



## 店铺LoginController_w
company/modules/shop/controllers/LoginController.php  
```php
public function actions() {
    return [
        'smscode' => [
            'class' => GetSmsAction::className(),
            'beforeCallback' => [$this, 'smsBeforeCallback'],
            'initCallback' => [$this, 'smsInitCallback'],
        ],
    ];
}

```


---
## 解析“云通讯接口“文档要求与代码开发
[详细的文档](sms/yuntongxun_api_work.md)

### 文档要求

>有主帐号与子帐号,默认选择主帐号即可  


**访问地址**
https://app.cloopen.com:8883/2013-12-26  

**统一请求包头_主帐号鉴权**
/Accounts/{accountSid}/{func}/{funcdes}?sig={SigParameter}  

**HTTP标准包头字段（必填**
```php
Accept:application/xml;
Content-Type:application/xml;charset=utf-8; 
Content-Length:256; 
Authorization:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX				
```
**SigParameter是REST API 验证参数**

• URL后必须带有sig参数，例如：sig=AAABBBCCCDDDEEEFFFGGG。  
• 使用MD5加密（主帐号Id + 主帐号授权令牌 +时间戳）。其中主帐号Id和主帐号授权令牌分别对应管理控制台中的ACCOUNT SID和AUTH TOKEN。  
• 时间戳是当前系统时间，格式"yyyyMMddHHmmss"。时间戳有效时间为24小时，如：20140416142030  
• SigParameter参数需要大写  


**Authorization是包头验证信息**

• 使用Base64编码（账户Id + 冒号 + 时间戳）其中账户Id根据url的验证级别对应主账户或子账户  
• 冒号为英文冒号  
• 时间戳是当前系统时间，格式"yyyyMMddHHmmss"，需与SigParameter中时间戳相同。  


### 代码开发
vendor/ihacklog/yii2-sms-module/provider/Yuntongxun.php  
```php
namespace ihacklog\sms\provider;

use ihacklog\sms\ISms;
use ihacklog\sms\components\BaseSms;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

class Yuntongxun extends BaseSms implements ISms
{
    //apiUrl https://$this->ServerIP:$this->serverPort/

    public $appId;

    public $accountSid;

    public $accountToken;

    public $softVersion;

    /**
     * @param $mobile 短信接收彿手机号码集合,用英文逗号分开
     * @param $data array 内容数据
     * @return bool
     */
    public function send($mobile, $data)
    {
        $error = '';
        $timestampParam = date('YmdHis');
        // 大写的sig参数
        $sig = strtoupper(md5($this->accountSid . $this->accountToken . $timestampParam));
        // 生成请求URL
        $url = $this->apiUrl . "/$this->softVersion/Accounts/$this->accountSid/SMS/TemplateSMS?sig=$sig";
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->accountSid . ':' . $timestampParam);
        // 生成包头
        $header = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json;charset=utf-8',
            'Authorization' => $authen
        ];
        // 发送请求
        $options = [];
        if (!$this->getModule()->enableHttpsCertVerify) {
            $options = ['verify' => false];
        }
        $client = new Client($options);
        try {
            //"{'to':'$to','templateId':'$tempId','appId':'$this->AppId','datas':[".$data."]}";
            $body = [
                'to' => $mobile,
                'templateId' => $this->templateId,
                'appId' => $this->appId,
                'datas' => $data
            ];
            // Request gzipped data, but do not decode it while downloading
            $response = $client->post($url, [
                'headers' => $header,
                'json' => $body
            ]);
        } catch (TransferException $e) {
            $error = sprintf('class: %s, error: %s', self::className(), $e->getMessage());
            $this->addErrMsg(500, $error);
            return false;
        }
        $result = (string)$response->getBody();
        $json = json_decode($result);
        if ($json->statusCode == 0) {
            return true;
        } else {
//            var_dump($json->statusMsg);die();
            $this->addErrMsg($json->statusCode, $json->statusMsg);
            return false;
        }
    }
}
```

## GuzzleHttp\Client [模拟登陆] 代替curl
```php
	$body = [
	    'to' => $mobile,
	    'templateId' => $this->templateId,
	    'appId' => $this->appId,
	    'datas' => $data
	];
	// Request gzipped data, but do not decode it while downloading
	$response = $client->post($url, [
	    'headers' => $header,
	    'json' => $body
	]);

```
