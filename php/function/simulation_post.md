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

##curl

### curl_post

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


## curl_参数封装_curl_setopt_array

```php

function curl_post($url, $post) {
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $post,
    );

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

$data = curl_post("http://www.xxx.com/post.php", array('name'=>'manongjc', 'email'=>'manongjc@gmail.com'));

var_dump($data);

```

## socket

## 访问socket存在慢
### socket_post

```php
/**
* Socket版本
* 使用方法：
* $post_string = "app=socket&version=beta";
* request_by_socket('facebook.cn','/restServer.php',$post_string);
*/
function request_by_socket($remote_server,$remote_path,$post_string,$port = 80,$timeout = 30){
   $socket = fsockopen($remote_server,$port,$errno,$errstr,$timeout);
   if (!$socket) die("$errstr($errno)");

   fwrite($socket,"POST $remote_path HTTP/1.1\r\n");
   fwrite($socket,"User-Agent: Socket Example\r\n");
   fwrite($socket,"HOST: $remote_server\r\n");
   fwrite($socket,"Content-type: application/x-www-form-urlencoded\r\n");
   fwrite($socket,"Content-length: ".strlen($post_string)."\r\n");
   fwrite($socket,"Accept:*/*\r\n");
   fwrite($socket,"\r\n");
   fwrite($socket,$post_string."\r\n");
   fwrite($socket,"\r\n");

   $header = "";
   while ($str = trim(fgets($socket,4096))) {
      $header.=$str;
   }
//var_dump($header);exit;
   $data = "";
   while (!feof($socket)) {
      $data .= fgets($socket,4096);
   }

   return $data;
}

 $postData = [
            'procduct_category_id' => '3',
            'name' => "手套4",
            'amount' => "3",
            'price' => "100",
            'status' => "1",
            'memo' => "456",
        ];

$_postData = http_build_query($postData);
$res = request_by_socket('www.a.com','/post.php',$_postData);

var_dump($res);

```

## 解决socket慢

### 优化解决socket访问慢

 [源码](simulation/socket3.php)  

```php
<?php
 $postData = [
            'procduct_category_id' => '3',
            'name' => "手套4",
            'amount' => "3",
            'price' => "100",
            'status' => "1",
            'memo' => "456",
        ];

$_postData = http_build_query($postData);


$timeout = 5;
$return = '';
$fp = fsockopen('www.a.com',80,$errno,$errstr,$timeout);


$request = "POST /post.php HTTP/1.1"."\r\n";;
$request .= "Host: www.a.com"."\r\n";
$request .= "Content-type: application/x-www-form-urlencoded\r\n";
$request .= "Content-Length: ".strlen($_postData)."\r\n";
$request .= "Postman-Token: 359d20ba-19a0-4ecc-0404-07c9e4ccc21b"."\r\n";
$request .= "Cache-Control: no-cache"."\r\n";
$request .= "Origin: chrome-extension://aicmkgpgakddgnaphhhpliifpcfhicfo"."\r\n";
$request .= "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36"."\r\n";
$request .= "Content-Type: multipart/form-data; boundary=----WebKitFormBoundaryXQGy95X9FXPQ5caf"."\r\n";
$request .= "Accept: */*"."\r\n";
$request .= "Accept-Encoding: gzip, deflate"."\r\n";
$request .= "Accept-Language: zh-CN,zh;q=0.8"."\r\n";
$request .= "\r\n"; //必须有这个,否则会超时
$request .= $_postData;

fwrite($fp,$request);

//1.解决socket访问慢
while(!feof($fp)){
 $header = @fgets($fp);
   $findstr = 'Content-Length:';
    if(strpos($header, $findstr) !== false){//获取内容长度
        $limit = intval(substr($header, strlen($findstr)));
    }
    if($header == "\r\n" ||  $header == "\n") {
        break;
    }
}

//2.解决socket访问慢
 $stop = false;
 //如果没有读到文件尾
while(!feof($fp) && !$stop) {
    //看连接时限是否=0或者大于8192  =》8192  else =》limit  所读字节数
    $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
    $return .= $data;
    if($limit) {
        $limit -= strlen($data);
        $stop = $limit <= 0;
    }
}

 fclose($fp);
echo $return;

```


### socket函数封装_提供post与get选择_支持cookie

 [源码](simulation/socket4.php)    


```php
<?php
/**
 * @param        $url  //网址 如 http://www.a.com/post.php
 * @param string $post  //POST传输专用
 * @param int    $limit //默认为0
 * @param string $cookie //cookie设置,默认为空
 * @param string $ip   //设置IP,如果访问的域名是IP,则填 ,默认为空
 * @param int    $timeout //超时时间,默认为15秒
 * @param bool   $block //是否是块 默认为true
 * @author cmk
 * @return string
 * 使用例子
 * dfopen('http://www.a.com/post.php?query',$_post);
 */
function dfopen($url, $post = '', $limit = 0, $cookie = '', $ip = '', $timeout = 15, $block = TRUE) {
    $return = '';
    $uri = parse_url($url);
    //var_dump($uri);exit;
    $host = $uri['host'];
    $path = $uri['path'] ? $uri['path'] . ($uri['query'] ? '?' . $uri['query'] : '') : '/';
    $port = !empty($uri['port']) ? $uri['port'] : '';
    if (!$port) {
        $port = ($uri['scheme'] == 'https') ? 80 : 80;
    }
    if ($post) {//post请求
        $post = http_build_query($post);
        $out = "POST $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        //$out .= "Referer: $boardurl\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= 'Content-Length: ' . strlen($post) . "\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cache-Control: no-cache\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
        $out .= $post;
    } else {//get请求
        $out = "GET $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        //$out .= "Referer: $boardurl\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
    }

    $fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
    if (!$fp) {
        return false;//note $errstr : $errno \r\n
    } else {
        //集阻塞/非阻塞模式流,$block==true则应用流模式
        stream_set_blocking($fp, $block);
        //设置流的超时时间
        stream_set_timeout($fp, $timeout);
        @fwrite($fp, $out);
        //从封装协议文件指针中取得报头／元数据
        $status = stream_get_meta_data($fp);

        //timed_out如果在上次调用 fread() 或者 fgets() 中等待数据时流超时了则为 TRUE,下面判断为流没有超时的情况
        if (!$status['timed_out']) {
            while (!feof($fp)) {
                $header = @fgets($fp);
                $findstr = 'Content-Length:';
                if (strpos($header, $findstr) !== false) {//获取内容长度
                    $limit = intval(substr($header, strlen($findstr)));
                }
                if ($header == "\r\n" || $header == "\n") {
                    break;
                }
            }
            $stop = false;

            //如果没有读到文件尾
            while (!feof($fp) && !$stop) {
                //看连接时限是否=0或者大于8192  =》8192  else =》limit  所读字节数
                $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                $return .= $data;
                if ($limit) {
                    $limit -= strlen($data);
                    $stop = $limit <= 0;
                }
            }
        }
        @fclose($fp);
        return $return;
    }
}

$postData = [
    'procduct_category_id' => '3',
    'name' => "手套4",
    'amount' => "3",
    'price' => "100",
    'status' => "1",
    'memo' => "456",
];

$postData2 = [
    'Procduct[procduct_category_id]' => '3',
    'Procduct[name]' => "手套4",
    'Procduct[amount]' => "3",
    'Procduct[price]' => "100",
    'Procduct[status]' => "1",
    'Procduct[memo]' => "456",
];

//普通的例子
//$arr = dfopen('http://www.a.com/post.php?query',$postData);

//带cookier_post的提交
$cookie = 'PHPSESSID=tfcvaa7k4q5p8bagls9o26ns84; _csrf=59c8dc255bfe44d7491b81a2f4f21eabb69e0f7377b3ee6554c3c2eb264f90c7a%3A2%3A%7Bi%3A0%3Bs%3A5%3A%22_csrf%22%3Bi%3A1%3Bs%3A32%3A%22v49du0rbi3V4bV57ALYXgrl2Uk_dIjI8%22%3B%7D; _identity=de2fee6d386abe54c94f12d247882ad20c5af5c51818d6203daf1eea33996b35a%3A2%3A%7Bi%3A0%3Bs%3A9%3A%22_identity%22%3Bi%3A1%3Bs%3A46%3A%22%5B1%2C%220LNlTfPHgBoQ_iwpMT0D2ke-vrvj2_gS%22%2C2592000%5D%22%3B%7D; PHPSESSID=tfcvaa7k4q5p8bagls9o26ns84; _csrf=5529d8e1aa0332ad1c437d1d2c69eef2f25ab1609a82ebfcbf464324a0e1b8a8a%3A2%3A%7Bi%3A0%3Bs%3A5%3A%22_csrf%22%3Bi%3A1%3Bs%3A32%3A%22-3sAcHU4eeIlJd3ifsHiGjznxcLtiE1q%22%3B%7D; _identity=de2fee6d386abe54c94f12d247882ad20c5af5c51818d6203daf1eea33996b35a%3A2%3A%7Bi%3A0%3Bs%3A9%3A%22_identity%22%3Bi%3A1%3Bs%3A46%3A%22%5B1%2C%220LNlTfPHgBoQ_iwpMT0D2ke-vrvj2_gS%22%2C2592000%5D%22%3B%7D';
$arr = dfopen('http://ysk.dev/admin/procduct/create?query',$postData2,0,$cookie);



echo $arr;
```


