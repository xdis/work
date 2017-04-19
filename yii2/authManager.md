# 权限

## 基本使用
```php
//common/models/User.php  
$auth = Yii::$app->authManager;
$auth->assign($auth->getRole(User::ROLE_USER), $this->getId());
```

## 使用权限

### vding使用权限

```
大家在开发的时候，尽量使用一个方法即可，别分解出好几个方法
比如：列表页
（1）：渲染模版
（2）：加载列表数据

使用一个方法就包含这两个功能，不要分解成两个方法，否则在权限添加这块，都不知道怎么添加，如果两个都添加，会给用户造成困扰与困惑

```
// 不需要校验权限的方法就写在这里
![](function/auth/vding/filter_auth.png)