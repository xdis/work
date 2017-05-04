# 对象
## 常用函数

### get_class

```php
class OneClass 
{
    public function test()
    {
        return 'this is test！';
    }
}
$obj = new OneClass();
var_dump(get_class($obj));
//输出：OneClass
```

### instanceof

```php
class One{
    public function test2()
    {
        return 'this is test2！';
    }
}
class OneClass extends One
{
    public function test()
    {
        return 'this is test！';
    }
}
$classname='OneClass';
$obj = new $classname();
if($obj instanceof One){
    echo $obj->test2();
}
//输出：this is test2！
```