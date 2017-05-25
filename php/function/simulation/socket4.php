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