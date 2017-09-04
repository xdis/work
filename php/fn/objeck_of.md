# 对象常用总结

## 静态变量存取_对象自身实例化
> 参考yii2 

**vendor/mdmsoft/yii2-admin/components/Configs.php**
```php
class Configs extends \yii\base\Object
{

    /**
     * @var self Instance of self
     */
    private static $_instance;

    /**
     * Create instance of self
     * @return static
     */
    public static function instance()
    {
        if (self::$_instance === null) {
            $type = ArrayHelper::getValue(Yii::$app->params, 'mdm.admin.configs', []);
            if (is_array($type) && !isset($type['class'])) {
                $type['class'] = static::className();
            }

            return self::$_instance = Yii::createObject($type);
        }

        return self::$_instance;
    }

    public static function __callStatic($name, $arguments)
    {
        $instance = static::instance();  //内部调用实例化
        if ($instance->hasProperty($name)) {
            return $instance->$name;
        } else {
            if (count($arguments)) {
                $instance->options[$name] = reset($arguments);
            } else {
                return array_key_exists($name, $instance->options) ? $instance->options[$name] : null;
            }
        }
    }    
}

//外部调用 
    public static function  checkRoute($route, $params = [], $user = null)
    {
        $config = Configs::instance();  //外部调用实例化
        $r = static::normalizeRoute($route);
        if ($config->onlyRegisteredRoute && !isset(static::getRegisteredRoutes()[$r])) {
            return true;
        }
    }

```