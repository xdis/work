# 仿照一个Module下两个登陆模块

## 访问方式
> http://i.ysk.dev/shop	//shopUser  
> http://i.ysk.dev    //user  
> [该整站的源代码](https://github.com/408824338/test-yii2/tree/master/company)
> [本仓库源代码整理](仿照一个Module下两个登陆模块_source/company)


## components_配置

```php
'components' => [

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

```



**company/modules/user/controllers/SignInController.php**

```php

    public function actionLogin()
    {
        $model = new LoginForm();
        if (Yii::$app->request->isAjax) {
            $model->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
             //  return $this->redirect('http://www.baidu.com');
               return $this->redirect('/user/default/index');
            // return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model
            ]);
        }
    }
```

**company/modules/shop/controllers/SignInController.php**

```php
   public function actionLogin()
    {
        $model = new LoginForm();
        if (Yii::$app->request->isAjax) {
            $model->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->shopLogin()) {
            return $this->redirect('/shop/default/index');
            //return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model
            ]);
        }
    }
```


**company/modules/user/models/LoginForm.php**

```php

  public function login()
    {
        if ($this->validate()) {
            if (Yii::$app->user->login($this->getUser(), $this->rememberMe ? Time::SECONDS_IN_A_MONTH : 0)) {
                return true;
            }
        }
        return false;
    }

    public function shopLogin()
    {
        if ($this->validate()) {
            if (Yii::$app->shopUser->login($this->getUser(), $this->rememberMe ? Time::SECONDS_IN_A_MONTH : 0)) {
                return true;
            }
        }
        return false;
    }
```
