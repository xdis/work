# 函数

---
## array_walk
使用用户自定义函数对数组中的每个元素做回调处理  
>bool array_walk ( array &$array , callable $callback [, mixed $userdata = NULL ] )  
>callback
典型情况下 callback 接受两个参数。array 参数的 "键值" 作为第一个，"键名" 作为第二个。  
>Note:
如果 callback 需要直接作用于数组中的值，则给 callback 的第一个参数指定为引用。这样任何对这些单元的改变也将会改变原始数组本身。


### 把数组(可多维)中值null转为空
```php
//把数组(可多维)中值null转为 ''
function convert_null_to_empty(&$arrdata){
    if (empty($arrdata)) {
        return '';
    }
    $configope =function(&$item,$key){
        if (is_array($item)) {
            convert_null_to_empty($item);
        }
        if(is_null($item)){
            $item ='';
        }
        return $item;
    };
    array_walk($arrdata, $configope);
    return $arrdata;
}
```

### 二维数组自定义键str转numberic]
```php
/**
 * [str2Numberic 二维数组自定义键str转numberic]
 * @param  [type]   $data            [二维数组]
 * @param  [type]   $transformColumn [自定义键数组]
 * @author paul
 * @time   20150416
 */
function str2Numberic($data, $transformColumn = []) {
	$rs = [];
	array_walk($data, function($item, $key) use(&$rs, $transformColumn){
		array_walk($item, function($iv, $ik) use(&$rs, $key, $transformColumn){
			$rs[$key][$ik] = !in_array($ik, $transformColumn) ? $iv : $iv + 0;
		});
	});
	return $rs;
}
```

### 等同于foreach
```php
$outputData = [];
array_walk($results, function ($item, $key) use (&$outputData) {
	$outputData[$key]['MobilePhone']    = $item['MobilePhone'];
    $outputData[$key]['RegName']        = $item['RegName'];
    $outputData[$key]['BankNames']      = $item['BankNames'];
    $outputData[$key]['BankBranchName'] = $item['BankBranchName'];
    $outputData[$key]['Holders']        = $item['Holders'];
    $outputData[$key]['BankNumber']     = "'".$item['BankNumber'];
    $outputData[$key]['CreateTime']     = $item['CreateTime'];
});
$columns = ['手机号码','银行名称','分行名称','开户人姓名','银行卡号','创建时间'];
$fileName = '银行卡数据列表';
A('Util')->export2csv($columns, $outputData, $fileName);

```

### 多个array_walk使用
```php
if (!empty($data)){
    $outputData = [];
    array_walk($data, function($item, $key) use(&$outputData){
        $outputData[$key]               = [];
        $outputData[$key]['RealName']   = $item['RealName'];
        $outputData[$key]['RegName']    = $item['RegName'];
        $outputData[$key]['Amount']     = $item['Amount'];
        $outputData[$key]['CreateTime'] = $item['CreateTime'];
        $outputData[$key]['type_show']  = C('invest_type.'.$item['Type']);
    });
    array_walk($outputData, function($item, $key) use($output){
        array_walk($item, function(&$cv) use(&$item){
            $cv =iconv("UTF-8", "GB2312", $cv);
        });
        fputcsv($output, $item);
    });
}
```

### 路由自定义组装
```php
    protected function freeRouteUrl($freeRoutes) {
    	if (empty($freeRoutes))
    		return false;
    	$connectstr = function (&$item) {
    		$item = 'api/' . strtolower($item);
    	};
    	array_walk($freeRoutes, $connectstr);
    	$currRoute = strtolower(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME);
    	if (!in_array($currRoute, $freeRoutes)) {
    		return false;
    	}
    	return true;
    }

```

### tp_清除缓存
```php
/* 如果有首页缓存,清除之前的首页缓存* */
$keys = $this->_cache->keys($this->prefix . '*');
if ($keys) {
    $cnt = 0;
    array_walk($keys, function ($item) use (&$cnt) {
                $cnt += $this->_cache->del($item);
            });
    if ($cnt != count($keys)) {
        exit('CLEAN_FAIL');
    }
}
```

---

## strncmp
> - int strncmp （string $str1 ，string $str2 ，int $len ）
> - strncmp - 二进制安全字符串比较前n个字符
> http://php.net/manual/en/function.strncmp.php

### IP星号匹配
```php
    /**
     * 判断匹配 如 192.168.1.2 是不是 192.168.* [*号之前的匹配] 
     * @param string $ip the IP address
     * @return bool whether the rule applies to the IP address
     */
    protected function matchIP($ip)
    {
        if (empty($this->ips)) {
            return true;
        }
        foreach ($this->ips as $rule) {
            if ($rule === '*' || $rule === $ip || (($pos = strpos($rule, '*')) !== false && !strncmp($ip, $rule, $pos))) {
                return true;
            }
        }

        return false;
    }

``` 

## array_map
> - array array_map ( callable $callback , array $array1 [, array $... ] )
> - array_map - 将回调应用于给定数组的元素
> http://php.net/manual/en/function.array-map.php


### 调用自定义函数

```php
function cube($n){
    return($n * $n * $n);
}

$a = array(1, 2, 3, 4, 5);
$b = array_map("cube", $a);
print_r($b);

//输出 
（
    [0] => 1
    [1] => 8
    [2] => 27
    [3] => 64
    [4] => 125
）
```

### 内置函数strtoupper

```php
    /**
     * @param string $verb the request method.
     * @return bool whether the rule applies to the request
     */
    protected function matchVerb($verb)
    {
        return empty($this->verbs) || in_array(strtoupper($verb), array_map('strtoupper', $this->verbs), true);
    }
```

## fnmatch

> - bool fnmatch （string $pattern ，string $string [，int $flags= 0 ]）
> - fnmatch - 匹配文件名与模式
> http://php.net/manual/en/function.fnmatch.php


### 正则匹配文件名

```php
if (fnmatch("*gr[ae]y", $color)) {
  echo "some form of gray ...";
}
```

### yii匹配例子

```php
    /**
     * Returns a value indicating whether the filter is active for the given action.
     * @param Action $action the action being filtered
     * @return bool whether the filter is active for the given action.
     */
    protected function isActive($action)
    {
        $id = $this->getActionId($action);

        if (empty($this->only)) {
            $onlyMatch = true;
        } else {
            $onlyMatch = false;
            foreach ($this->only as $pattern) {
                if (fnmatch($pattern, $id)) {
                    $onlyMatch = true;
                    break;
                }
            }
        }

        $exceptMatch = false;
        foreach ($this->except as $pattern) {
            if (fnmatch($pattern, $id)) {
                $exceptMatch = true;
                break;
            }
        }

        return !$exceptMatch && $onlyMatch;
    }
}
```
