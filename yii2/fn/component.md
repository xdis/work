# 组件

## 自定义组件

### 配置
common/config/base.php  
```php
'components' => [
...
    'mycomponent' => [
        'class' => 'common\components\MyComponent',
        'terry' => 'xxxx',
    ],
...
],
```

### 组件文件
common/components/MyComponent.php  

```php
namespace common\components;
use Yii;
use yii\base\Component;

class MyComponent  extends Component{

    public $terry;

    public function welcome(){
        echo $this->terry."Hello workld";
    }
}
//输出
```

### 控制器
```php

public function actionTest2(){
   echo Yii::$app->mycomponent->welcome();
}
//输出 xxxxHello workld
```

