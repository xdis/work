# H5店铺登陆

## 环境说明
>此登陆在于Company Module下子模块[company/modules/shop/controllers/LoginController.php]  
>分为密码登陆与短信[没有则注册]

## 文件对应的路径
[SmsForm](user/h5_login_w/SmsForm.php)  //子类 company/modules/shop/models/SmsForm.php  
[PwdForm](user/h5_login_w/PwdForm.php)  //子类 company/modules/shop/models/PwdForm.php  
[LoginForm](user/h5_login_w/LoginForm.php)  //login基类 company/modules/shop/models/LoginForm.php  
[LoginController](user/h5_login_w/LoginController.php)  //控制器 company/modules/shop/controllers/LoginController.php  
[User.php](user/h5_login_w/User.php) // 控制器  common/models/User.php  

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

## 密码登陆
[源码](user/h5_login_w/LoginController.php#L76-L103)  
company/modules/shop/controllers/LoginController.php  
```php
  /**
     * 密码登陆
     * @return multitype:unknown string
     */
    public function actionPwd() {
        if (!Yii::$app->shopUser->isGuest) {
            return $this->ajaxSuccess('已经登录');
        }
        $model = new PwdForm();
        $model->setScenario('pwd_login');
        $model->company_id = $this->store_company_id;
        $model->user_id = $this->store_owner_id;
        $model->username = Yii::$app->getRequest()->post('username');
        $model->password = Yii::$app->getRequest()->post('password');


        if ($model->validate() && $model->doLogin()) {
            return $this->ajaxSuccess('登录成功', '', [
                'is_shopkeeper' => \Yii::$app->getSession()->get('is_shopkeeper'),
                'is_passed_card' => $model->is_passed_card,
                'store_company_id' => Yii::$app->getSession()->get('store_company_id'),
                'store_owner_id' => Yii::$app->getSession()->get('store_owner_id'),
            ]);
        } else {
            $error = $model->getErrors();
            return $this->ajaxFail('登录失败 ' . current($error)[0]);
        }
    }
```

## 时序图
uml/1.3 H5店铺密码登陆.oom




## 短信登陆
[源码](user/h5_login_w/LoginController.php#L52-L74)  
company/modules/shop/controllers/LoginController.php  
```php
    /**
     * 短信验证码登陆
     * @return multitype:unknown string |Ambigous <string, string>
     */
    public function actionSms() {
        if (!Yii::$app->shopUser->isGuest) {
            return $this->ajaxSuccess('已经登录');
        }
        $model = new SmsForm();
        $model->setScenario('sms_login');
        $model->company_id = $this->store_company_id;
        $model->user_id = $this->store_owner_id;
        $model->mobile = Yii::$app->getRequest()->post('mobile');
        $model->sms_verify_code = Yii::$app->getRequest()->post('sms_verify_code');

        if ($model->validate() && $model->doLogin()) {
            return $this->ajaxSuccess('登录成功', '', [
                'is_shopkeeper' => \Yii::$app->getSession()->get('is_shopkeeper'),
                'is_passed_card' => $model->is_passed_card,
                'store_company_id' => Yii::$app->getSession()->get('store_company_id'),
                'store_owner_id' => Yii::$app->getSession()->get('store_owner_id'),
            ]);
        } else {
            $error = $model->getErrors();
            return $this->ajaxFail('登录失败 ' . current($error)[0]);
        }
    }
```


