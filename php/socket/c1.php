<?php
error_reporting(E_ALL);
set_time_limit(0);
echo "<h2>TCP/IP Connection</h2>\n";

$port = 15999;
$ip = "127.0.0.1";

/*
 * +-------------------------------
 * @socket连接整个过程
 * +-------------------------------
 * @socket_create
 * @socket_connect
 * @socket_write
 * @socket_read
 * @socket_close
 * +--------------------------------
 */

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket < 0) {
    echo "socket_create() failed: reason: " . socket_strerror($socket) . "\n";
} else {
    echo "OK.\n";
}

echo "试图连接 '$ip' 端口 '$port'...\n";
$result = socket_connect($socket, $ip, $port);
if ($result < 0) {
    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror($result) . "\n";
} else {
    echo "连接OK\n";
}

//  $in = "0466   <ap><TransCode>7506</TransCode><CorpNo>6637501020962908</CorpNo><OpNo>0001</OpNo><AuthNo>8B1DAF22CA3FB696D99BCCA5CCB363B6</AuthNo><ChannelType>0</ChannelType><ReqDate>20160301</ReqDate><ReqTime>165135</ReqTime><Sign></Sign><Version>    <CcVersion>2</CcVersion>    <CsVersion>0</CsVersion></Version><ReqSeqNo>160000914515</ReqSeqNo><Cme>    <CcIp>127.0.0.1</CcIp></Cme><Cmp>    <DbProv>15</DbProv>    <DbAccNo>350114010001187</DbAccNo>    <DbCur>14</DbCur></Cmp></ap>";
$str="<ap><FileFlag>0</FileFlag><ProductID>ICC</ProductID><ReqSeqNo>{ReqSeqNo}</ReqSeqNo><AuthNo></AuthNo><OpNo></OpNo><CorpNo></CorpNo><ChannelType>ERP</ChannelType><TransCode></TransCode><CCTransCode>IBAF04</CCTransCode><Cme><ReqSeqNo>{ReqSeqNo}</ReqSeqNo></Cme><MacAddress></MacAddress><FileComress>1</FileComress><Cmp><DbLogAccNo></DbLogAccNo><SumNum>1</SumNum><DbAccNo>157101040012079</DbAccNo><DbProv>15</DbProv><DbCur>01</DbCur><ContFlag>0</ContFlag><BatchFileName>SIGN17030700020093999900004.txt</BatchFileName></Cmp><Corp><DbAccName></DbAccName><NVoucherType>99029999</NVoucherType><NFAccNo>15121301941000317</NFAccNo></Corp><Amt>4.12</Amt></ap>";
$reqSeqNo=date('YmdHis').mt_rand(10000,99999);
$str=str_replace("{ReqSeqNo}",$reqSeqNo,$str);
$in="0".strlen($str);
$newStr= str_pad($in,7," ");
$newStr.=$str;
$in=$newStr;


$out = '';

if (! socket_write($socket, $in, strlen($in))) {
    echo "socket_write() failed: reason: " . socket_strerror($socket) . "\n";
} else {
    echo "发送到服务器信息成功！\n";
    echo "发送的内容为:<font color='red'>$in</font> <br>";
}

while ($out = socket_read($socket, 8192)) {
  //  echo "接收服务器回传信息成功！\n";
  	echo iconv("GB2312","UTF-8",$out);
}

echo "关闭SOCKET...\n";
socket_close($socket);
echo "关闭OK\n";