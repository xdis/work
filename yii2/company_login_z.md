# 企业登陆_z
## 配置文件

```php
 'user' => [
    'class'=>'company\components\CompanyUser', //Yii::$app->user 所指的类
    'identityClass' => 'common\models\User', //认证类
    'loginUrl'=>['sign-in/login'],
    'enableAutoLogin' => false,
    'as afterLogin' => 'common\behaviors\LoginTimestampBehavior'
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
### 说明

'company\components\CompanyUser' 继承 'yii\web\User'  

--- 

## SignInController_控制器
[源码](user/company_login_z/SignInController.php#L146-L205)  
company/controllers/SignInController.php  
```php

 public function actionLogin() {
        if (Yii::$app->request->isGet) {
            $_rtn_url = Yii::$app->request->get('rtn_url');
            Yii::$app->session->set('rtn_url', $_rtn_url);
        }

        $this->layout = 'base';
        $returnUrl = Yii::$app->getUrlManager()->createUrl(['index/index']);
        $model = new LoginForm();
        $post = Yii::$app->getRequest()->post($model->formName());
        if (!Yii::$app->user->isGuest && !isset($post['company_id'])) {
            return $this->ajaxSuccess('已经登录', $returnUrl);
        }
        if (Yii::$app->getRequest()->isGet) {
            return $this->render('login', [
                'model' => $model,
            ]);
        }

        //第二步,如果已经登陆过,则走这个,否则就走下去,走第一步
        if (Yii::$app->getRequest()->isPost && Yii::$app->user->isPreLoginPassed()) {
            $model->setScenario('company_login');
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                Yii::$app->session->remove('login_user_ids');
                //跳转至上次登陆页
                $this->rtnUrl();
                $returnUrl = $this->theReturnUrl();
                return $this->ajaxSuccess('登录成功', $returnUrl);
            } else {
                $error = $model->getErrors();
                return $this->ajaxFail('登录失败 ' . current($error)[0]);
            }
        }
        $post = Yii::$app->getRequest()->post();
        if (!isset($post['LoginForm']['loginType'])) {
            return $this->ajaxFail('参数错误!');
        }

        //判断是否短信登陆,选择场景
        if ('sms_login' == $post['LoginForm']['loginType']) {
            $model->setScenario('sms_login');
        } else {
            $model->setScenario('login');
        }

        //第一步开始
        try {
            if ($model->load(Yii::$app->request->post()) && $model->preLogin()) {
                $returnUrl = $returnUrl = $this->theReturnUrl();
                //跳转至上次登陆页
                $this->rtnUrl();
                return $this->ajaxSuccess('验证成功', $returnUrl);
            } else {
                $error = $model->getErrors();
                return $this->ajaxFail('登录失败 ' . current($error)[0]);
            }
        } catch (Exception $e) {
            return $this->ajaxFail('登录失败 ' . $e->getMessage());
        }
    }
```

---

## LoginForm
[源码](user/company_login_z/LoginForm.php)  
company/models/LoginForm.php  



## CompanyUser
[源码](user/company_login_z/CompanyUser.php)  
company/components/CompanyUser.php  



## User
[源码](user/company_login_z/User.php)  
common/models/User.php    


