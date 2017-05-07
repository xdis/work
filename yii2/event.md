# 事件
## 简单例子
> 简单测试一下 事件是怎样宝义与触发  
> 来源:[yii2项目实战-事件的理解](http://www.manks.top/document/yii2-event.html)   

## 访问地址
http://ysk.dev/admin/demo-event/index    

## 输出
I'm a test event   

```php

namespace backend\controllers;


use Codeception\Module\FunctionalHelper;
use yii\web\Controller;

class DemoEventController extends Controller {
    
    const SEND_TEST = 'send_test';//1.定义要事件执行调用的方法名 on()和trigger(),会调用到

    public function init() {
        parent::init();
		//2.绑定事件
        $this->on(self::SEND_TEST,function (){
            echo "I'm a test event" ;
        });
    }

    public function actionIndex(){
		//3.触发事件 
        $this->trigger(self::SEND_TEST);
    }
}

```

## 知识补充
```php

// 调用当前类的onEventTest方法
$this->on(self::EVENT_TEST, [$this, 'onEventTest']);

// 调用backend\components\event\Event类的test方法
$this->on(self::EVENT_TEST, ['backend\components\event\Event', 'test']);
```

---

