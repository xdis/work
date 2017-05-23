# jsonp跨域


## b.com请求a.com
>http://www.b.com使用jsonp访问a.com数据

**/www.b.com/index.php**

```php
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="https://cdn.bootcss.com/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>

<script type="text/javascript">
    $(function () {
        //alert('aaaa');
        $.getJSON("http://www.a.com/test.php?aaa=111&bbb=222&jsoncallback=?", function (json) {
            alert(json.aaa);

            alert(json.bbb);
            alert('aaa');
        });
    })
</script>

<p>
    <input type="button" id="send" value="加载"/>
</p>

<div id="resText">
</div>
</body>
</html>
```

**/www.a.com/test.php**

```php
$aaa = $_GET["aaa"];
$bbb = $_GET["bbb"];
$jsonp = $_GET['jsoncallback'];
$data = json_encode(["aaa" => $aaa, "bbb" => $bbb]);
echo $jsonp . '(' . $data . ')';

```

### 结果输出
> http://www.b.com使用jsonp访问a.com数据  
>弹框输出 111  
>弹框输出 222  
>弹框输出 aaa  