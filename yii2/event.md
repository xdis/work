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
