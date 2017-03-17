#vding_api

## 重置密码
http://vding.dev/test/hash?pwd=123456   
frontend/controllers/TestController.php  
```php
public function actionHash()
{
    $password = Yii::$app->request->getQueryParam('pwd');
    return Yii::$app->getSecurity()->generatePasswordHash($password);
}
``` 

## 获取token  

### postman地址:http://api.vding.dev/v1/user/login-test  

![](vding_api/postman.png)
```php
POST请求  
postman地址参数  
username:15014600030
password:59f0437ca25c6515d2c631b28c5de6627348c4eadb1c659f9b9b0a63b96fcb366bb91228a7eccd66ae6431ee6a00901b00b4071e3a659eaae13b658735334b6a4a09599d00e6a22392b61a137d45f1c949e92cda43629fd0c70fbb241d8920e4124fcf7b728f5c3e35d510d6d5f0fbabb1318403c6fe9bb3fe31a6886084b2d2
login_type:password_login
``` 
//返回
```php
{
  "status": 200,
  "message": "",
  "data": {
    "token": "9557BFD8214DDD335F9EDBD252D75CB046849922CFE332F53E45DFC3C7A7486A1EAE21AB92D9B0B85D0EC020BDB731FEC7BFFD965FFACAE7A7E36565BAB34D45832E78D576B08B272A125DB2B6394402C54263EBE19EA2DAB4B31313BD7EED07EE713208C34B911A219EAD9BA833D79B4F495AABE0D7B9528A833BCE4803C5A5",
    "token_client": "9851A3EC4AC74F50AE3502B2F356FBEEAFCF5C1BC2140AC33EA40AD9A7A11D807CD7814C96E4B31F91CC12EE0DE33FEBC55846F5CC4225A98CECF6054EEDC1483C60EC68FEB75CD04607FE73D08120DF89DB318F7C8261F59F14E5CBE83A7D4B6E2EBD2851563292246BC07985905CFA85B1DE150CCAD0F92BAA5F378E75A757",
    "token_plain": "Al36UNH3HPyZiPw2cT-onp7TRBlwRxGglUAnJCpY",
    "id": 200
  }
}
```

### 代码
```php
rest/versions/v1/controllers/UserController.php
    /**
     * 登录接口
     * 接收参数：mobile or username password loginType (password or sms)
     * @return string AuthKey or model with errors
     * @throws BadRequestHttpException
     */
    public function actionLoginTest()
    {
        $model = new LoginForm();
        $this->chooseScenario($model);
        if ($model->load(\Yii::$app->getRequest()->post(), '') && $model->login()) {
            return $this->getTheAccessToken(true);
        } else {
            throw new BadRequestHttpException(current($model->getErrors())[0]);
        }
    }
```

---

## api获取TOKENE和访问

    
**简要描述：** 

- 获取接口token

**请求URL：** 
http://api.d.v.w/v1/user/login-test
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|username |是  |string |用户名 &#124;  如 zkyucn 最好用手机号码 |
|password |是  |string | 密码  &#124; 访问 http://d.v.w/test/enc?pwd=123456 获取 |
|login_type     |否  |string | 类型  &#124; 如 password_login  |

 **返回示例**
-  注:取 "token_client" 作为token
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
-  注:取 "token_client" 作为token
 ``` 
http://api.v2.v.w/v1/user/token-test?access-token=28E4D51B3625E8C96506C4D845F823DE79C8B212B1705CD36B5BF2AF3CD19E690E3A276C0DCB47409C7C801A20E672025D0FFD24CDB193D766F49B35B898CC6CF84FE7E9EEC296761122021F78566FA3BA61754369E9AAF45391D469C8379200A9B2BFA82AD841C806A3B5190603D19042E04AC10560C0A48F70049C15F56859 
```  
 **备注** 

- 更多返回错误代码请看首页的错误代码描述
---
## api认证_zhou
>参考 [yii2项目实战-restful api之授权验证
](http://blog.csdn.net/lhorse003/article/details/62215672)  


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