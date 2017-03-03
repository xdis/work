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

$in = "0466   <ap><TransCode>7506</TransCode><CorpNo>6637501020962908</CorpNo><OpNo>0001</OpNo><AuthNo>8B1DAF22CA3FB696D99BCCA5CCB363B6</AuthNo><ChannelType>0</ChannelType><ReqDate>20160301</ReqDate><ReqTime>165135</ReqTime><Sign></Sign><Version>    <CcVersion>2</CcVersion>    <CsVersion>0</CsVersion></Version><ReqSeqNo>160000914515</ReqSeqNo><Cme>    <CcIp>127.0.0.1</CcIp></Cme><Cmp>    <DbProv>15</DbProv>    <DbAccNo>350114010001187</DbAccNo>    <DbCur>14</DbCur></Cmp></ap>";

$out = '';

if (! socket_write($socket, $in, strlen($in))) {
    echo "socket_write() failed: reason: " . socket_strerror($socket) . "\n";
} else {
    echo "发送到服务器信息成功！\n";
    echo "发送的内容为:<font color='red'>$in</font> <br>";
}

while ($out = socket_read($socket, 8192)) {
    echo "接收服务器回传信息成功！\n";
    echo "接受的内容为:", $out;
}

echo "关闭SOCKET...\n";
socket_close($socket);
echo "关闭OK\n";