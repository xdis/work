#vding_api
##api获取TOKENE和访问

    
**简要描述：** 

- 获取接口token

**请求URL：** 
http://api.d.v.w/v1/user/login-test
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|username |是  |string |用户名 |  如 zkyucn  |
|password |是  |string | 密码  | 访问 http://d.v.w/test/enc?pwd=123456 获取 |
|login_type     |否  |string | 类型  | password_login  |

 **返回示例**

``` 
{
  "status": 200,
  "message": "",
  "data": {
    "token": "6BE2B5A7FB8761379D0CCEA8B30BDD70A67D3575AAD1EAADE2091C43C333CC4928521380B3E84773CD669A673CF77A7E093FA6B0D0887DB6344D67D7746F501311405E7052F5A4F80D57D19E138178E787ABAA16D981D9BA2F62D1BD177CB6175C003AC408A2C521AC6B1112EA59ADBFBFE582F4A64D4F4C1C5D15C6386A254C",
    "token_client": "74F49FFCA6B83310DCEC6D478D3120073D7CB82E83B932B05341239D1BE1083E6D20B847B8DC0FD694B5785105F6780A7EE7517CC26BE5AF33C442158F22AE3A6FBD5189D4CF87B4B6AA744CD20D6FC43E316A1CA51E25789B4AF74B791D3535E5E572919C3AF1ADA10F77A6572F3AC23C50CDAF32B402F67E311CE880F2C774",
    "token_plain": "ZEbEHS485UBcjU8g8adomsG8RjBeD39in2w0IX-x",
    "id": 4
  }
}
```

 **返回参数说明** 

|参数名|类型|说明|
|:-----  |:-----|-----                           |
|token |string   | 访问的TOKEN  |

 **使用访问** 
 ``` 
http://api.v2.v.w/v1/user/token-test?access-token=28E4D51B3625E8C96506C4D845F823DE79C8B212B1705CD36B5BF2AF3CD19E690E3A276C0DCB47409C7C801A20E672025D0FFD24CDB193D766F49B35B898CC6CF84FE7E9EEC296761122021F78566FA3BA61754369E9AAF45391D469C8379200A9B2BFA82AD841C806A3B5190603D19042E04AC10560C0A48F70049C15F56859 
```  
 **备注** 

- 更多返回错误代码请看首页的错误代码描述
---
##api认证_zhou
>>参考 
###控制器基类
 common/controllers/BaseRestController.php  
```php 
namespace common\controllers;
use yii\rest\ActiveController;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\web\ForbiddenHttpException;
use rest\filters\auth\SecureTokenAuth;

class BaseRestController extends ActiveController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => SecureTokenAuth::className(),
            //注意，这个只能限制到action,不能指定controller
            'except' => ['login', 'login-test', 'register', 'get-sms', 'get-register-sms', 'end-user-license','forget-pass'],
        ];
        return $behaviors;
    }
}

``` 
### 认证类
rest/filters/auth/SecureTokenAuth.php  
```php 
namespace rest\filters\auth;

use yii\filters\auth\AuthMethod;
use Yii;

/**
 * SecureTokenAuth is an action filter that supports the authentication based on the access token passed through a query parameter.
 * access-token 是rsa加密后的十六进制ASCII字符串
 * 这是原版auth认证的一个改进版，增加了rsa加密
 * @TODO 增加timestamp 和 singature
 * @author HuangYeWuDeng
 */
class SecureTokenAuth extends AuthMethod
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'access-token';

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->get($this->tokenParam);
        $accessTokenBin = hex2bin(strtolower($accessToken));
        $accessTokenPlain = Yii::$app->rsa->privateDecrypt($accessTokenBin);
        if (is_string($accessTokenPlain)) {
            $identity = $user->loginByAccessToken($accessTokenPlain, get_class($this));
            if ($identity !== null) {
                return $identity;
            }
        }
        if ($accessTokenPlain !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}

``` 

##将密码转换为 access_toekn
frontend/controllers/TestController.php  
访问:http://vding.dev/test/enc?pwd=123456
```php 
    public function actionEnc()
    {
        $password = Yii::$app->request->getQueryParam('pwd');
        return bin2hex(Yii::$app->rsa->publicEncrypt($password));
    }
// 输出 
3eeb6ade484c07e28e72fe676d237ac72196f7de5b3aab7dcbdd70c4691f118d5b11c306262f2c8932ad342a8b5ec7b499714bea3a41583725ff65e943b187242c70a62c9978987efe778bea8e77f209231301907007528825d16b1704ca296793844e060762d3ab19e2c812857e12deaa8f68de14a9d5e728ad3006adbc6ec2

``` 
## API的地址访问
```php 
http://api.v2.v.w/v1/user/token-test?access-token=3eeb6ade484c07e28e72fe676d237ac72196f7de5b3aab7dcbdd70c4691f118d5b11c306262f2c8932ad342a8b5ec7b499714bea3a41583725ff65e943b187242c70a62c9978987efe778bea8e77f209231301907007528825d16b1704ca296793844e060762d3ab19e2c812857e12deaa8f68de14a9d5e728ad3006adbc6ec2 

```