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

## 自带事件_model层

### 保存之前的事件示例
```php
public function beforeSave($insert)
{
    if (parent::beforeSave($insert)) {
        // 插入新数据判断订单号是否存在
        if (!Order::findModel(['trade_no' => $this->order_trade_no])) {
            throw new Exception("订单号不存在");
        }
        return true;
    } else {
        return false;
    }
}
```

### 保存之后的事件示例	
```php
public function afterSave($insert, $changedAttributes)
{
    parent::afterSave($insert, $changedAttributes);
    if ($insert) {
        // 插入新数据之后修改订单状态
        Order::updateAll(['shipping_status' => Order::SHIPPING_STATUS1, 'shipping_at' => time()], ['trade_no' => $this->order_trade_no]);
    }
}
```	
### 删除之后的事件示例
```php
public function afterDelete()
{
    parent::afterDelete();
}
```

### 事件怎么保证数据事务呢
```php
public function transactions()
{
    return [
        self::SCENARIO_DEFAULT => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE
        // self::SCENARIO_DEFAULT => self::OP_INSERT
    ];
}
```

## 自带事件_controller层

### 每次请求之前操作示例
```php
/**
 * @param \yii\base\Action $action
 * @return bool
 * @throws \yii\web\BadRequestHttpException
 */
public function beforeAction($action)
{
    if (parent::beforeAction($action)) {
        $this->request = Yii::$app->request;
        Yii::info($this->request->absoluteUrl, '请求地址');
        Yii::info($this->request->rawBody, '请求数据');
        return true;
    } else {
        return false;
    }
}
```

### 每次请求之后操作示例
```php
/**
 * @param \yii\base\Action $action
 * @param mixed $result
 * @return array|mixed
 * @throws BusinessException
 */
public function afterAction($action, $result)
{
    Yii::info(\yii\helpers\Json::encode($result), '请求返回结果');
    return $result;
}
```
---

## 触发saving事件_同时保存日志和缓存

>事件的话，也就是观察者模式，触发一个事件后通知所有观察这个事件的观察者，观察者可以是多个。比如当保存一个 Model 的时候，触发 saving 事件，这个时候就可以编写一个保存日志和一个更新缓存的观察者，以后只要保存 Model ，就可以把日志记录下来，同时更新缓存  

```php
<?php

abstract class Model
{

  private $event;

  public function __construct(Event $event)
  {
    $this->event = $event;
  }

  public function save()
  {
    $this->event->notify('saving', $this);
  }

}

class User extends Model
{
}

class Blog extends Model
{
}

class Event
{
  private $events;

  public function addObserver($name, IObserver $observer){
    $this->events[$name][] = $observer;
  }

  public function notify($name, $model){
    echo get_class($model) . PHP_EOL;
    foreach ($this->events[$name] as $observer) {
      $observer->doSomething($model);
    }
  }
}

interface IObserver{
  public function doSomething(Model $model);
}

class LogObserver implements IObserver{
  public function doSomething(Model $model){
    echo 'Log...' . PHP_EOL;
  }
}

class CacheObserver implements IObserver{
  public function doSomething(Model $model){
    echo 'Cache...' . PHP_EOL;
  }
}


$event = new Event();
$event->addObserver('saving', new LogObserver());
$event->addObserver('saving', new CacheObserver());

$user = new User($event);
$user->save();
$blog = new Blog($event);
$blog->save();

//输出
User
Log...
Cache...
Blog
Log...
Cache...
```