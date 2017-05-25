# 模拟表单提交

##操作准备_服务端post操作数据
>www.a.com   
>post.php和conn.php文件代码  

**conn.php**
```php
$host = 'localhost';
$database = '';
$username = 'root';
$password = '123456';

// 创建对象并打开连接，最后一个参数是选择的数据库名称
$mysqli = new mysqli($host, $username, $password, $database);

$mysqli->set_charset("utf8");//或者 $mysqli->query("set names 'utf8'")

if (mysqli_connect_errno()) {
    // 诊断连接错误
    die("could not connect to the database.\n" . mysqli_connect_error());
}

$selectedDb = $mysqli->select_db($database);//选择数据库
if (!$selectedDb) {
    die("could not to the database\n" . mysql_error());
}
```

**www.a.com/post.php**
```php
require_once 'conn.php';

$sql = "select *  from procduct";
$result = $mysqli->query($sql);

//var_dump($_POST);exit;
//插入数据
if($_POST){

    $procduct_category_id = $_POST['procduct_category_id'];
    $name = $_POST['name'];
    $amount = $_POST['amount'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $memo = $_POST['memo'];

    $sql="insert into procduct (procduct_category_id,name,amount,price,status,memo) values ({$procduct_category_id},'{$name}',{$amount},{$price},{$status},'{$memo}')";

    //echo $sql;exit;
  $res =   $mysqli->query($sql);

  if($res){
      echo '插入成功';
  }else{
      echo '插入失败';
  }
}

 echo 'fail';
 $mysqli->close();
```

## file_get_contents或fopen_post

```php
 $postData = [
            'procduct_category_id' => '3',
            'name' => "手套4",
            'amount' => "3",
            'price' => "100",
            'status' => "1",
            'memo' => "456",
        ];
 $_postData = http_build_query($postData);
$url = 'www.a.com/post.php';
    $ops = [
        'http' => [
            'method' => 'POST',
            'header' => "Host:www.a.com\r\n" .
                "Content-type:application/x-www-form-urlencoded\r\n" .
                "Content-length:" . strlen($_postData)."\r\n",
            'content' => $_postData,
        ],
    ];

$context = stream_context_create($ops);


//方法1
    $fp = file_get_contents("http://www.a.com/post.php", false, $context);
    //dp($fp);
 //方法二  
  // $fp = fopen("http://www.a.com/post.php", 'r', false, $context);
   // fclose($fp);

```

## curl_post

```php
 $postData = [
    'procduct_category_id' => '3',
    'name' => "手套4",
    'amount' => "3",
    'price' => "100",
    'status' => "1",
    'memo' => "456",
];
$url = 'www.a.com/post.php';

//初始化一个curl会话
$ch = curl_init();


//设置提交的网址
curl_setopt($ch, CURLOPT_URL, $url);



//设置数据提交方式
curl_setopt($ch, CURLOPT_POST, 1);

//设置数据提交方式
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

//设置cookie
//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);

//设置来源 
// curl_setopt($ch, CURLOPT_REFERER, 'http://ysk.dev/admin/procduct/create');

//提交成功之后,把数据返回为字符串
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$output = curl_exec($ch);
curl_close($ch);

echo $output;

```


## file_get_contents或fopen

```php


```