# 行为

# 类的混合
## 猫叫老鼠添加行为

### 给狗添加行为方法和行为变量

1. 新建狗行为  
common/behaviors/DogBehavior.php   
```php
namespace common\behaviors;

use yii\base\Behavior;

class DogBehavior extends Behavior {

    public $height;

    //狗添加吃的行为
    public function eat(){
        echo 'dog eat<br />';
    }
}
```

1.1 狗的类库绑定行为  
vendor/horse003/yii2-event-demo/src/Dog.php  
```php
namespace horse003\event;
use common\behaviors\DogBehavior;
use yii\base\Component;

class Dog extends Component {

    //添加狗的行为进行
    public function behaviors() {
        return [
          DogBehavior::className()
        ];
    }

    public function look() {
        echo 'i am looking!<br />';
    }
}
```

2.控制器  
backend/controllers/DemoEventController.php  
```php
    public function actionDog(){
        $dog = new Dog();
        //输出绑定的行为
        $dog->eat(); //dog eat
        //1.给行为的变量赋值
        $dog->height=50;
        //2.输出行为的变量
        echo $dog->height;
    }
```

3.访问地址   
http://ysk.dev/admin/demo-event/dog   
//输出  

dog eat
50

### 给狗添加事件与触发

1.行为里添加要绑定的触发的行为    
common/behaviors/DogBehavior.php   
```php
namespace common\behaviors;

use yii\base\Behavior;

class DogBehavior extends Behavior {

    public $height;

    //狗添加吃的行为
    public function eat(){
        echo 'dog eat<br />';
    }

    //添加行为的触发的方法
    public function events() {
       return [
           'wang'=>'shout'
       ];
    }

    //行为要触发的方法
    public function shout($event){
        echo 'wang wang wang<br />';
    }
}
```

2.控制器触发事件行为  
backend/controllers/DemoEventController.php  
```php
    public function actionDogEvent() {
        $dog = new Dog();
        //触发狗类的行为
        $dog->trigger('wang');
    }
```

3.访问输出  
http://ysk.dev/admin/demo-event/dog-event  
//输出  

wang wang wang

---
# 对象的混合

## 定义行为类
vendor/horse003/yii2-event-demo/src/Dog.php  
```php

namespace horse003\event;

use yii\base\Component;

class Dog extends Component {

    public function look() {
        echo 'i am looking!<br />';
    }
}
```

## 控制器绑定或解绑行为
backend/controllers/DemoEventController.php   
```php
    public function actionDogObject(){
        $dog = new Dog();
        $dogBehavior = new DogBehavior();
        //绑定行为
        $dog->attachBehavior('dogBeh',$dogBehavior);
        //删除行为
        $dog->detachBehaviors('dogBeh');
        echo $dog->eat();
    }
```
## 访问与输出  
http://ysk.dev/admin/demo-event/dog-object  

# 30分钟自动退出
>场景:用户登陆之后,30分钟之内无操作的话,则退出登陆

## 配置user
**admin\config\web.php**

```php
'components' => [
'user' => [
    'class' => admin\components\AdminUser::class,
    'identityClass' => admin\models\User::class,
    'loginUrl' => ['sign-in/login'],
    'enableAutoLogin' => true,    //1.配置是否启动
    'as afterLogin' => admin\behaviors\LoginTimestampBehavior::class, //2.调用的behavior
    'sessionTimeout' => 1800, //会话超时时间，单位秒
],
],
  'as beforeRequest' => admin\behaviors\SessionTimeoutBehavior::class,  //每次请求,都访问这个
```

## 调用behavior
**admin\behaviors\LoginTimestampBehavior.php**

```php
namespace admin\behaviors;

use common\models\Account;
use common\models\Company;
use common\models\UserCompany;
use yii\base\Behavior;
use yii\web\User;
use Yii;
use company\components\CompanyUser;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class LoginTimestampBehavior extends Behavior
{
    /**
     * @var string
     */
    public $attribute = 'logged_at';

    public $attributeIp = 'logged_ip';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            User::EVENT_AFTER_LOGIN => 'afterLogin'
        ];
    }

    /**
     * @param $event \yii\web\UserEvent
     */
    public function afterLogin($event)
    {
        $user = $event->identity;
        $last_login_time = $user->{$this->attribute};
        $attrIp = $this->attributeIp;
        $user->$attrIp = Yii::$app->getRequest()->getUserIP();
        $user->touch($this->attribute);
        //记录设备名称和设备型号
        $user->device_name = Yii::$app->getRequest()->post('device_name');
        $user->device_model = Yii::$app->getRequest()->post('device_model');
        //重置失败登录记数
        $user->failure_time = 0;

        $user->save(true, ['device_name', 'device_model', 'failure_time', 'last_company_id']);
        //3.启动检测
        if (Yii::$app->user->enableSession) {
            Yii::$app->user->setLastLoginTime($last_login_time); //经分析这个可以不要 //保存最后的登陆时间,
            Yii::$app->session->set('__user_last_activity', time()); //保存当前的时间
        }
    }
}

```

## 调用user的函数_保存当前的时间和登陆的时间保存进session
**admin\components\AdminUser.php**

```php
/**
 * Created by PhpStorm.
 * User: hacklog
 * Date: 12/20/16
 * Time: 11:13 AM
 */

namespace admin\components;

use common\models\Company;
use company\models\Account;
use yii\web\User;
use Yii;

class AdminUser extends User
{
    public $lastLoginTimeParam = '__last_login_time';

    public $sessionTimeout = 1800;

    public function loginByAccessToken($token, $type = null)
    {
        /* @var $class IdentityInterface */
        $class = $this->identityClass;
        $identity = $class::findIdentityByAccessToken($token, $type);
        if ($identity && $this->login($identity)) {
            return $identity;
        } else {
            return null;
        }
    }

    public function setLastLoginTime($lastLoginTime)
    {
        Yii::$app->getSession()->set($this->lastLoginTimeParam, $lastLoginTime);
    }

    public function getLastLoginTime()
    {
        return Yii::$app->getSession()->get($this->lastLoginTimeParam);
    }

    /**
     * 退出登录
     * @param bool $destroySession
     */
    public function logout($destroySession = true)
    {
        $sessKeys = [
            $this->lastLoginTimeParam,
        ];
        foreach ($sessKeys as $key) {
            Yii::$app->session->remove($key);
        }
        parent::logout($destroySession);
    }
}

```

## 每次请求都请求一下,是否已经超时
 
 **admin\config\web.php**
```php
 'as beforeRequest' => admin\behaviors\SessionTimeoutBehavior::class,  //每次请求,都访问这个
```

**admin\behaviors\SessionTimeoutBehavior.php**

```php
<?php

namespace admin\behaviors;

use yii\base\Application;
use yii\base\Behavior;
use yii\web\User;
use Yii;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class SessionTimeoutBehavior extends Behavior
{
    /**
     * @var string
     */
    public $attribute = 'logged_at';

    public $attributeIp = 'logged_ip';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Application::EVENT_BEFORE_REQUEST => 'checkSessionTimeout'
        ];
    }

    /**
     * @param $event \yii\web\UserEvent
     */
    public function checkSessionTimeout($event)
    {
        if (Yii::$app->user->enableSession) {
            $last_activity_time = Yii::$app->session->get('__user_last_activity');
            if (time() - $last_activity_time > Yii::$app->user->sessionTimeout) {
                Yii::$app->user->logout(true);
            }
            Yii::$app->session->set('__user_last_activity', time());
        }
    }
}

```

