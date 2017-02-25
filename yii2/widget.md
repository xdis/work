##widget简单例子
来源 [yii2项目实战-小部件widget的了解以及源码分析](http://www.manks.top/document/yii2-widget.html)

###1创建一个TestWidget
common/widgets/TestWidget.php
```php
namespace common\widgets;
use yii\base\Widget;

class TestWidget extends Widget {

    public function run(){
        echo "this is my test widget";
    }
}
```
###2view页调用
```php

# 输出结果 “this is my test widget”
echo \common\widgets\TestWidget::widget();
```

