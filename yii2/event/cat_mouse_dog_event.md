# 事件

## 猫叫老鼠跑的事件

## git仓库
[github](https://github.com/408824338/yii2-event-demo)  
[coding](https://coding.net/u/horse003/p/yii2-event-demo/git)  


## 访问地址 
http://ysk.dev/admin/demo-event/animal  

## 1.猫来了，老鼠就跑了   trigger()  on()  

### 猫 
vendor/horse003/yii2-event-demo/src/Cat.php  
```php
    public function shout() {
        echo 'miao maio miao<br />';
        $this->trigger('miao');
    }
```
### 老鼠
vendor/horse003/yii2-event-demo/src/Mourse.php  
```php
 class Mourse { 
     public function run(){
         echo 'mourse is runing<br />';
     }
 }
```
### 控制器
backend/controllers/DemoEventController.php  
```php
    public function actionAnimal(){
        $cat = new Cat();
        $mouse = new Mourse();
        $cat->on('miao',[$mouse,'run']);
        $cat->shout();
    }
```
### 输出
```php
 miao maio miao  
 mourse is runing  
```

## 2.加入事件传参数 

### 猫 
vendor/horse003/yii2-event-demo/src/Cat.php  
```php

    public function shout() {
        echo 'miao maio miao<br />';

        //1.加入一个事件，传话筒
        $me = new MyEvent();
        $me->message = 'hello my is event<br />';
        //2.发送事件
        $this->trigger('miao', $me);
    }
```
### 老鼠
vendor/horse003/yii2-event-demo/src/Mourse.php  
```php

 class Mourse {
    
     //3.$e 接收事件
     public function run($e){
         echo $e->message;
         echo 'mourse is runing<br />';
     }
 }
```
### 输出
```php
miao maio miao  
hello my is event  
mourse is runing  
```
## 3.加入dog角色  
猫叫，老鼠跑了，小狗在看

### dog
vendor/horse003/yii2-event-demo/src/Dog.php
```php
class Dog {
    public function look() {
        echo 'i am looking!<br />';
    }
}
```
### 控制器
backend/controllers/DemoEventController.php 
```php
    public function actionAnimal(){
        $cat = new Cat();
        $mouse = new Mourse();
        $dog = new Dog();
        $cat->on('miao',[$mouse,'run']);
        $cat->on('miao',[$dog,'look']);
        $cat->shout();

    }
```
### 输出
```php
    miao maio miao
    hello my is event
    mourse is runing
    i am looking!
    
```

##  4.取消狗在看的动作
```php
    public function actionAnimal(){
        $cat = new Cat();
        $mouse = new Mourse();
        $dog = new Dog();
        $cat->on('miao',[$mouse,'run']);
        $cat->on('miao',[$dog,'look']);
        $cat->off('miao',[$dog,'look']);
        $cat->shout();

    }
```
### 输出
```php
    miao maio miao
    hello my is event
    mourse is runing
```

## 5.实例化多一只猫对象，却只有上面的一只老鼠在跑(一个事件在跑)
```php
    public function actionAnimal(){
        $cat = new Cat();
        $cat2 = new Cat();  //实例化多一只猫
        $mouse = new Mourse();
        $dog = new Dog();
        $cat->on('miao',[$mouse,'run']);

        $cat->shout();
        $cat2->shout();  //第二只猫执行，但没有执行事件

    }
```
### 输出  
```php 
 miao maio miao
 hello my is event
 mourse is runing
 miao maio miao   
```

## 5.1 对5的改进，实例化多个猫对象，能否对应的事件，即所有的老鼠都在跑呢？
```php
    public function actionAnimal(){
        $cat = new Cat();
        $cat2 = new Cat();
        $mouse = new Mourse();
        $dog = new Dog();
       // $cat->on('miao',[$mouse,'run']);
        Event::on(cat::className(),'miao',[$mouse,'run']);
        $cat->shout();
        $cat2->shout();

    }
```
### 输出   
```php 
miao maio miao
hello my is event
mourse is runing
miao maio miao
hello my is event
mourse is runing
```

## 5.2 加入匿名函数，运行后触发,注意这个时候，里边的Event事件即失效了
```php
    public function actionAnimal() {
        $cat = new Cat();
        $cat2 = new Cat();
        $mouse = new Mourse();
        $dog = new Dog();

        Event::on(Cat::className(), 'miao', function(){
            echo 'miao event has triggered<br/>';
        });
        $cat->shout();
        $cat2->shout();

    }
```
### 输出   
```php
 miao maio miao
 miao event has triggered
 miao maio miao
 miao event has triggered   
    
```    

## 6 根据系统   $this->trigger(self::EVENT_AFTER_REQUEST),action输出后再输出
###系统
vendor/yiisoft/yii2/base/Application.php 
```php
(new yii\web\Application($config))->run();

 public function run()
    {
        try {

            $this->state = self::STATE_BEFORE_REQUEST;
            $this->trigger(self::EVENT_BEFORE_REQUEST);

            $this->state = self::STATE_HANDLING_REQUEST;
            $response = $this->handleRequest($this->getRequest());

            $this->state = self::STATE_AFTER_REQUEST;
            $this->trigger(self::EVENT_AFTER_REQUEST);

            $this->state = self::STATE_SENDING_RESPONSE;
            $response->send();

            $this->state = self::STATE_END;

            return $response->exitStatus;

        } catch (ExitException $e) {

            $this->end($e->statusCode, isset($response) ? $response : null);
            return $e->statusCode;

        }
    }
```  
### 控制器
backend/controllers/DemoEventController.php     
``` php
     public function actionAnimal() {
 
         \Yii::$app->on(\yii\base\Application::EVENT_AFTER_REQUEST,function (){
             echo 'event after request';
         });
 
         $cat = new Cat();
         $cat2 = new Cat();
         $mouse = new Mourse();
         $dog = new Dog();
 
         Event::on(Cat::className(), 'miao', function(){
             echo 'miao event has triggered<br/>';
         });
         $cat->shout();
         $cat2->shout();
     }
```
### 输出  
``` php       
  miao maio miao
  miao event has triggered
  miao maio miao
  miao event has triggered
  event after request      
``` 