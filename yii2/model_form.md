# model_form
## 登陆_SignupForm
http://ysk.dev/user/sign-in/login  

[SignupForm代码](model/SignupForm.php)  
```php
  /**
     * @return string|Response
     */
    public function actionSignup()
    {
        $model = new SignupForm(); //定义的Form
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->signup();
            if ($user) {
                if ($model->shouldBeActivated()) {
                    Yii::$app->getSession()->setFlash('alert', [
                        'body' => Yii::t(
                            'frontend',
                            'Your account has been successfully created. Check your email for further instructions.'
                        ),
                        'options' => ['class'=>'alert-success']
                    ]);
                } else {
                    Yii::$app->getUser()->login($user);
                }
                return $this->goHome();
            }
        }

        return $this->render('signup', [
            'model' => $model
        ]);
    }
```

