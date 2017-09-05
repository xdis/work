# yii2 admin 分析

## yii2_admin_反射获取action

**vendor/mdmsoft/yii2-admin/models/Route.php**

```php
    /**
     * Get route of action
     * @param \yii\base\Controller $controller
     * @param array                $result all controller action.
     */
    protected function getActionRoutes($controller, &$result) {
        $token = "Get actions of controller '" . $controller->uniqueId . "'";
        Yii::beginProfile($token, __METHOD__);
        try {
            $prefix = '/' . $controller->uniqueId . '/';
            foreach ($controller->actions() as $id => $value) {
                $result[$prefix . $id] = $prefix . $id;
            }
            $class = new \ReflectionClass($controller);
            foreach ($class->getMethods() as $method) {
                $name = $method->getName();
                if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                    $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', substr($name, 6)));
                    $id = $prefix . ltrim(str_replace(' ', '-', $name), '-');
                    $result[$id] = $id;
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }
``` 


# db存放授权数据

## db配置
**backend/config/web.php**
```php
'components' => [
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
        'defaultRoles' => ['guest'],
    ],
]

'as access' => [
    'class' => 'mdm\admin\components\AccessControl',
    'allowActions' => [
        //这里是允许访问的action
        //controller/action
//         '*',
        '/admin/sign-in/login',
    ],
],
```

# 非DB_数组方式

## 非DB_配置

```php
'components' => [
    'authManager' => [
        'class' => 'yii\rbac\PhpManager',
        'defaultRoles' => ['guest'],
    ],
]

'as access' => [
    'class' => 'mdm\admin\components\AccessControl',
    'allowActions' => [
        //这里是允许访问的action
        //controller/action
//         '*',
        '/admin/sign-in/login',
    ],
],
```

## 使用数组写入规则

```php
<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;

class RbacController extends Controller
{
    // 添加权限
    public function actionInit2 ()
    {
        $auth = Yii::$app->authManager;

        // 添加权限, 注意斜杠不要反了
        $blogView = $auth->createPermission('/blog/view');
        $auth->add($blogView);
        $blogCreate = $auth->createPermission('/blog/create');
        $auth->add($blogCreate);
        $blogUpdate = $auth->createPermission('/blog/update');
        $auth->add($blogUpdate);
        $blogDelete = $auth->createPermission('/blog/delete');
        $auth->add($blogDelete);

        // 分配给我们已经添加过的"博客管理"权限
        $blogManage = $auth->getRole('博客管理');
        $auth->addChild($blogManage, $blogView);
        $auth->addChild($blogManage, $blogCreate);
        $auth->addChild($blogManage, $blogUpdate);
        $auth->addChild($blogManage, $blogDelete);
    }
}
```

## 源码分析 

**vendor/yiisoft/yii2/rbac/BaseManager.php**
```php
    public function createPermission($name)
    {
        $permission = new Permission();
        $permission->name = $name;
        return $permission;
    }

    /**
     * @inheritdoc
     */
    public function add($object)
    {
        if ($object instanceof Item) {
            if ($object->ruleName && $this->getRule($object->ruleName) === null) {
                $rule = \Yii::createObject($object->ruleName);
                $rule->name = $object->ruleName;
                $this->addRule($rule);
            }
            return $this->addItem($object);
        } elseif ($object instanceof Rule) {
            return $this->addRule($object);
        } else {
            throw new InvalidParamException('Adding unsupported object type.');
        }
    }    
     /**
     * @inheritdoc
     */
    public function getRole($name)
    {
        $item = $this->getItem($name);
        return $item instanceof Item && $item->type == Item::TYPE_ROLE ? $item : null;
    }   
```


**vendor/yiisoft/yii2/rbac/PhpManager.php**
```php
    /**
     * @inheritdoc
     */
    protected function addRule($rule)
    {
        $this->rules[$rule->name] = $rule;
        $this->saveRules();
        return true;
    }

  /**
     * @inheritdoc
     */
    public function addChild($parent, $child)
    {
        if (!isset($this->items[$parent->name], $this->items[$child->name])) {
            throw new InvalidParamException("Either '{$parent->name}' or '{$child->name}' does not exist.");
        }

        if ($parent->name === $child->name) {
            throw new InvalidParamException("Cannot add '{$parent->name} ' as a child of itself.");
        }
        if ($parent instanceof Permission && $child instanceof Role) {
            throw new InvalidParamException('Cannot add a role as a child of a permission.');
        }

        if ($this->detectLoop($parent, $child)) {
            throw new InvalidCallException("Cannot add '{$child->name}' as a child of '{$parent->name}'. A loop has been detected.");
        }
        if (isset($this->children[$parent->name][$child->name])) {
            throw new InvalidCallException("The item '{$parent->name}' already has a child '{$child->name}'.");
        }
        $this->children[$parent->name][$child->name] = $this->items[$child->name];
        $this->saveItems();

        return true;
    }    
```