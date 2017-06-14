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
