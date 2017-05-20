# Closure
>匿名函数，也叫闭包函数(closures)，允许临时创建一个没有指定名称的函数。

##__invoke魔幻方法
```php
class Callme {
    public function __invoke($phone_num) {
        echo "Hello: $phone_num";
    }
}
 
$call = new Callme();
$call(13810688888); // "Hello: 13810688888

```


## 匿名函数的实现
```php
$func = function() {
    echo "Hello, anonymous function";
}
 
echo gettype($func);    // object
echo get_class($func);  // Closure

```

## use

### 在普通函数中当做参数传入也可以被返回

#### 在函数里定义一个匿名函数_并且调用它
```php
function printStr() {
    $func = function( $str ) {
        echo $str;
    };
    $func( 'some string' );
}

printStr();
```

#### 在函数中把匿名函数返回_并且调用它
```php
function getPrintStrFunc() {
    $func = function( $str ) {
        echo $str;
    };
    return $func;
}


$printStrFunc = getPrintStrFunc();
$printStrFunc( 'some string' );


```

#### 把匿名函数当做参数传递_并且调用它
```php
function callFunc( $func ) {
    $func( 'some string' );
}

$printStrFunc = function( $str ) {
    echo $str;
};
callFunc( $printStrFunc );

```

#### 直接将匿名函数进行传递
```php
callFunc( function( $str ) {
    echo $str;
} );
```



### 普通use使用

```php
$name = 'TIPI Team';
$func = function() use($name) {
    echo "Hello, $name";
}
 
$func(); // Hello TIPI Team

```

### use使用其外部作用域的变量
```php
function getCounter() {
    $i = 0;
    return function() use(&$i) { // 这里如果使用引用传入变量: use(&$i)
        echo ++$i;
    };
}
 
$counter = getCounter();
$counter(); // 1
$counter(); // 2
```

### yii2关联查询用use加载外界参数
```php
$province_id=15;
$customers=Parks::find()->where(['id'=>2])
          ->width(['house'=>function($query) use($province_id){ //使用use调用外部的变量
           $query->andWhere(['province_id'=>$province_id]);
          }
          ])->asArray()->all();
```