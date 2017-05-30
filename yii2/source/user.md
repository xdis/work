# user

## 用户登陆

### login
**vendor/yiisoft/yii2/web/User.php**

```php
   public function login(IdentityInterface $identity, $duration = 0)
    {
        if ($this->beforeLogin($identity, false, $duration)) {
            $this->switchIdentity($identity, $duration);
            $id = $identity->getId();
            $ip = Yii::$app->getRequest()->getUserIP();
            if ($this->enableSession) {
                $log = "User '$id' logged in from $ip with duration $duration.";
            } else {
                $log = "User '$id' logged in from $ip. Session not enabled.";
            }
            Yii::info($log, __METHOD__);
            $this->afterLogin($identity, false, $duration);
        }

        return !$this->getIsGuest();
    }

  //登陆的核心操作
   public function switchIdentity($identity, $duration = 0)
    {
        $this->setIdentity($identity);

        if (!$this->enableSession) {
            return;
        }

        /* Ensure any existing identity cookies are removed. */
        if ($this->enableAutoLogin) {
            $this->removeIdentityCookie();
        }

        $session = Yii::$app->getSession();
        if (!YII_ENV_TEST) {
        $session->regenerateID(true);
    }
        $session->remove($this->idParam);
        $session->remove($this->authTimeoutParam);

        if ($identity) {
            $session->set($this->idParam, $identity->getId());
            if ($this->authTimeout !== null) {
                $session->set($this->authTimeoutParam, time() + $this->authTimeout);
            }
            if ($this->absoluteAuthTimeout !== null) {
                $session->set($this->absoluteAuthTimeoutParam, time() + $this->absoluteAuthTimeout);
            }
            if ($duration > 0 && $this->enableAutoLogin) {
                $this->sendIdentityCookie($identity, $duration);
            }
        }
    }

	//保存cookie: 1.设置多少秒  2.是否启动保存cookie
    protected function sendIdentityCookie($identity, $duration)
    {
        $cookie = new Cookie($this->identityCookie);
        $cookie->value = json_encode([
            $identity->getId(),
            $identity->getAuthKey(),
            $duration,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $cookie->expire = time() + $duration;
        Yii::$app->getResponse()->getCookies()->add($cookie);
    }

```



### 时序图分析
[时序图源文件](../uml/登陆-yii-web-user-login2.oom) 


## 获取身份信息


### 对应identityClass下companyInfo方法下的id

**company/config/web.php**
```php
//identityClass指定的model名
'user' => [
    'class'=>'company\components\CompanyUser',
    'identityClass' => 'common\models\User',
    'loginUrl'=>['sign-in/login'],
    'enableAutoLogin' => false,
    'as afterLogin' => 'common\behaviors\LoginTimestampBehavior'
],
```

**common/models/User.php**
```php
/**
 * 关联公司
 */
public function getCompanyInfo()
{
    return $this->hasOne(Company::className(), ['user_id' => 'id']);
}
```