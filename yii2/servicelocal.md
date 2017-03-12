#依赖注入
#服务器定位

##控制器配置调用

backend/controllers/ServiceLocalController.php  
```php

namespace backend\controllers;


use yii\base\Controller;
use yii\di\ServiceLocator;

class ServiceLocalController extends Controller {

    public function actionIndex() {
        ///男司机
	    //下面访问car类的时候,告诉他Driver是接口不是class
        \Yii::$container->set('backend\controllers\Driver','backend\controllers\ManDriver');
        $s1 = new ServiceLocator();
        $s1->set('car',['class'=>'backend\controllers\Car']);
        $car = $s1->get('car');
        $car->run();
        //女司机
        \Yii::$container->set('backend\controllers\Driver','backend\controllers\WomanDriver');
        $s1 = new ServiceLocator();
        $s1->set('car',['class'=>'backend\controllers\Car']);
        $car = $s1->get('car');
        $car->run();
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
http://ysk.dev/admin/service-local/index  
###输出  
i am an old man  
i am an woman driver  


##参数配置在文件并调用

###backend/config/web.php的配置 
```php 
 'components'=>[
   ...
	'car'=>[
          'class'=>'backend\controllers\Car',
      ],
  ...
]
```
###代码区
```php
namespace backend\controllers;


use yii\base\Controller;
use yii\di\ServiceLocator;

class ServiceLocalController extends Controller {

    public function actionIndex() {
        ///男司机
        \Yii::$container->set('backend\controllers\Driver','backend\controllers\ManDriver');
        \Yii::$app->car->run();

        //女司机
        \Yii::$container->set('backend\controllers\Driver','backend\controllers\WomanDriver');
        \Yii::$app->car->run();
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
注:这里的输出竟然是同样的结果,有点奇怪!   
i am an old man  
i am an old man  