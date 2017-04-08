# 权限

## 基本使用
```php
//common/models/User.php  
$auth = Yii::$app->authManager;
$auth->assign($auth->getRole(User::ROLE_USER), $this->getId());
```