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


## 配置_yii2_admin

```php
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

## 

```php

```


## 

```php

```