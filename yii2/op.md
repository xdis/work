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

### controller传值给layout
**在控制器中这样写**
```php
$this->view->params['customParam'] = 'customValue';
```

**在视图中这样调用**
```php
echo $this->params['customParam'];
```

## 发布线上
### 缓存清空
```php
//方法一:清空表结构缓存的方法
 
//flush all the schema cache
Yii::$app->db->schema->refresh();
 
//clear the particular table schema cache
Yii::$app->db->schema->refreshTableSchema($tableName);
 
 
//方法二:清空所有的缓存--不仅仅是mysql表结构
Yii::$app->cache->flush();
 
 
//方法三:使用 yii命令行的方式commond清除缓存
cache/flush                Flushes given cache components.
cache/flush-all            Flushes all caches registered in the system.
cache/flush-schema         Clears DB schema cache for a given connection component.
cache/index (default)      Lists the caches that can be flushed.
 
//执行 
./yii cache/flush-all
```