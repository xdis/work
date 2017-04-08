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
