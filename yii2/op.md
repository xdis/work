# 常用
## 手动增加csrf

```php
<?php echo Html::hiddenInput(Yii::$app->getRequest()->csrfParam, Yii::$app->getRequest()->csrfToken);?>
```