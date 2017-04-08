# 依赖注入

>司机开车

## 案例1 普通的访问
>本例子中,太偶合了,如现在添加多一名女司机怎样?  
>即下面的里,添加一个接口的方案,解决这个问题    

backend/controllers/DiController.php   

```php

<?php
namespace backend\controllers;
use yii\base\Controller;
use yii\di\Container;

class DiController extends Controller {

    public function actionIndex() {
        //1定义一个容器
        $container = new Container();
        //2调用类
        $car = $container->get('backend\controllers\Car');
        //3执行
        $car->run();

    }
}

//一名男师傅
class ManDriver {
    public function driver() {
        echo 'i am an old man';
    }
}

class Car {
    private $_driver = null;
    //注: $driver须传入对应的类的名称  ManDriver,否则会报错
    public function __construct(ManDriver $driver) {
        $this->_driver = $driver;
    }

    public function run() {
        $this->_driver->driver();
    }
}

```
## 访问与输出 
http://ysk.dev/admin/di/index  
### 输出 
i am an old man

---
## 案例2 以接口的访问
backend/controllers/DiController.php  
```php

<?php
namespace backend\controllers;
use yii\base\Controller;
use yii\di\Container;

class DiController extends Controller {

    public function actionIndex() {
        $container = new Container();
        //4.第3步里,因为是以接口为定义类的接口,则这里要set一下,否则会以类的class来实例化,导致出错
        $container->set('backend\controllers\Driver','backend\controllers\ManDriver');
        $car = $container->get('backend\controllers\Car');
        $car->run();

    }
}

//1.定义接口
interface Driver{
    public function driver();
}
//2.实现接口
class ManDriver implements Driver {
    public function driver() {
        echo 'i am an old man';
    }
}

class Car {
    private $_driver = null;
    //3.注: $driver须传入对应的类的名称  Driver,否则会报错
    public function __construct(Driver $driver) {
        $this->_driver = $driver;
    }

    public function run() {
        $this->_driver->driver();
    }
}

```
##访问与输出 
http://ysk.dev/admin/di/index  
###输出 
i am an old man

##案例3 以接口方式,添加多一个女司机
```php
namespace backend\controllers;


use yii\base\Controller;
use yii\di\Container;

class DiController extends Controller {

    public function actionIndex() {
        $container = new Container();

		//男司机  
        $container->set('backend\controllers\Driver','backend\controllers\ManDriver');
        $man_car = $container->get('backend\controllers\Car');
        $man_car->run();

		//女司机  
        $container->set('backend\controllers\Driver','backend\controllers\WomanDriver');
        $woman_car = $container->get('backend\controllers\Car');
        $woman_car->run();

    }
}

interface Driver{
    public function driver();
}

class ManDriver implements Driver {
    public function driver() {
        echo 'i am an old man<br />';
    }
}

class WomanDriver implements Driver{
    public function driver() {
        echo 'i am an woman driver<br />';
    }
}

class Car {
    private $_driver = null;

    public function __construct(Driver $driver) {
        $this->_driver = $driver;
    }

    public function run() {
        $this->_driver->driver();
    }
}

```
##访问与输出 
http://ysk.dev/admin/di/index  
###输出 
i am an old man  
i am an woman driver  

---




