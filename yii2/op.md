# 常用
## 手动增加csrf

```php
<?php echo Html::hiddenInput(Yii::$app->getRequest()->csrfParam, Yii::$app->getRequest()->csrfToken);?>
```

## layout

### 根据参数选择不同layout,如充值有个人和企业统一入口,不同身份不同layout
company/modules/admin/controllers/AlipayController.php  
```php
if (Yii::$app->user->getIsPerson()) {
    //个人充值
    Yii::$app->name = '个人中心';
    $this->layout = '@company/modules/ucenter/views/layouts/main.php';
} else {
    //企业充值
    Yii::$app->name = '企业管理后台';
    $this->layout = '@company/views/layouts/main.php';
}
```