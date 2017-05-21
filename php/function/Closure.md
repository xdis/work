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

## 实例
>use $name[只复制变量一份,仅闭包内部有效]  
>use &$name[绑定上下变量关系]  

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

### 购物车实例
```php
// 一个基本的购物车，包括一些已经添加的商品和每种商品的数量。
// 其中有一个方法用来计算购物车中所有商品的总价格，该方法使
// 用了一个 closure 作为回调函数。
class Cart
{
    const PRICE_BUTTER  = 1.00;
    const PRICE_MILK    = 3.00;
    const PRICE_EGGS    = 6.95;

    protected   $products = array();
    
    public function add($product, $quantity)
    {
        $this->products[$product] = $quantity;
    }
    
    public function getQuantity($product)
    {
        return isset($this->products[$product]) ? $this->products[$product] :
               FALSE;
    }
    
    public function getTotal($tax)
    {
        $total = 0.00;
        
        $callback =
            function ($quantity, $product) use ($tax, &$total)
            {
                $pricePerItem = constant(__CLASS__ . "::PRICE_" .
                    strtoupper($product));
                $total += ($pricePerItem * $quantity) * ($tax + 1.0);
            };
        
        array_walk($this->products, $callback);
        return round($total, 2);;
    }
}

$my_cart = new Cart;

// 往购物车里添加条目
$my_cart->add('butter', 1);
$my_cart->add('milk', 3);
$my_cart->add('eggs', 6);

// 打出出总价格，其中有 5% 的销售税.
print $my_cart->getTotal(0.05) . "\n";
// 最后结果是 54.29
```
