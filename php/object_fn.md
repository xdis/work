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

>有些时候我们只在乎某个对象是不是属于某个类的继承  


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

### get_class_methods

>使用函数get_class_methods($obj)来获取一个类中所有方法的列表，注意$obj可以是类名，或是一个对象。


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
    echo '<pre>';
    print_r(get_class_methods($obj));
}

//输出：
Array
(
    [0] => test
    [1] => test2
)
```

### get_class_methods

>在PHP5中，对于一个类中的方法，无论是private、protected、public，method_exists()都返回true；


```php
if( in_array('test2', get_class_methods($obj)) ){
    echo $obj->test2();
}


```

### method_exists

> method_exists()函数的参数为一个对象/类名,和一个方法名，如果方法在类中存在则返回true；


```php
class One{
 public function test2()
 {
     return 'this is test2！--www.aipanshi.com';
 }
}
class OneClass extends One
{
 public function test()
 {
     return 'this is test！';
 }
    
 private function meth()
 {
     return 'this is test！--www.aipanshi.com';
 }
}
$classname='OneClass';
$obj = new $classname();
if( method_exists($obj, 'test2') ){
 echo $obj->test2();
}
//下面的调用meth方法会报错，因为meth是私有方法，不能再外面调用
if( method_exists($obj, 'meth') ){
 echo $obj->meth();
}
//**Fatal error**: Call to private method OneClass::meth() ...


```

### is_callable

>第一个参数，方法名，如果该方法存在且可被调用，则返回true。要检测类中的某个方法可否被调用，可以给函数传递一个数组:[对象/类名，方法名]  
>第二个参数，布尔类型。默认为 false；如果设置 true 的话，函数仅检查给定的方法或函数名称的语法是否正确，而不检查其是否真正存在。  


```php
if( is_callable([$obj, 'meth']) ){
  echo $obj->meth();
}else {
  echo 'meth因为是private，所以不能被调用--www.aipanshi.com';
}


```

### class_implements

>class_implements()使用一个类名或一个对象应用作为参数。返回一个有接口名构成 的数组。


```php
in_array('AInterface',class_implements('aObject'))

```

### class_exists
>动态实例化只有类名的对象 使用上  
>数接受类名的字符串。返回布尔类型值，存在true,反之false。 上一章的代码完善一下如下  


```php
$file = 'here/OneClass.php';
if( ! file_exists( $file ) ){
    throw new Exception( $file.'文件不存在' );
}
require_once 'here/OneClass.php';
$classname = 'here\OneClass';
if( ! class_exists( $classname ) ){
    throw new Exception( $classname.'class 不存在' );
}
$obj = new $classname();
var_dump($obj->test());

```


## autoload

### 发送微信号或发送短信例子
>假如我们我们定义了三种消息推送方式，当用户手机有关注我们微信公众号的时候，我们给用户推送微信模板消息； 假如用户没有关注我们公众号，我们就推送阿里大鱼短信，假如阿里大鱼推送失败就要切换56短信商来推送； 搁以前，我们实现是这样；  

**实例(具体实现省略)**

```php
//在/lib/message中定义类 Alidayu
class Alidayu{
	
}
//在/lib/message中定义类 Wechat，
class Wechat{

}
//在/lib/message中定义类 M56
class M56{

}

```

**传统**
```php
require_once '/lib/message/Alidayu.class.php';
require_once '/lib/message/Wechat.class.php';
require_once '/lib/message/M56.class.php';
if(绑定微信){
	$wechat = new wechat();
	....
}else{
	$dayu = new Alidayu();
	....
	if(大鱼发送失败){
		$m56 = new M56();
		....
	}
}

```

**使用autoload**


```php
function __autoload($class){
	$filePath = "/lib/message/{$class}.class.php";
	if (is_readable($filePath)) {
		require_once($filePath);
	}
}

```