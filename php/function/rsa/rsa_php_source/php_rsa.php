<?php 
$private_key = '-----BEGIN RSA PRIVATE KEY-----
MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBANFaqYx1syYwyBkc
35FW9VCRBTwQHFiWWyoaAMNARVO42VPoG+sipGIAkRvkveepu4OdAfaBk3m2DGhy
WqR5zxwIHjAPHV3hl/zePK32j1pczJe+qT8LLIV2kbvp4o0rwfJbe+upUNNQc3bE
Qrjm4WMBjEgAorM84opThjcTr6EzAgMBAAECgYEAxspF8J/aissLVf06tPFTYzsx
M0VBBUVQL3wdeUlQCx2dD7V4vR9Z480R/OCJCq765RBzSrTjalDZG1xqgX9Ajm7P
r+fvnMjYcbGKwSnhYmRtffJvN5V3pcvERXwHoVKGATkWD03a39D+uTQzIYCZRVlB
y5EgUP/qCNZ27Q5vIlECQQD4axF0SUPvlQQAmZ3qM+ayMZKZj5ARSDY8GFC2emKx
VUlY0q1gA9uQXbR4K5tIzHPQ5zY5tOEJf/qXyRGGYdDnAkEA175hTpoxZFg/lvw2
pBysrx95SQRZcrEYl3povVIT4GQjM41XlAGJ/Lpd2gF8mO2bXZqGzUXdD2S6/eGM
HGWH1QJBAO3geOJFlgxBQYfhkdnGwU45Mgxh8K7b2zNIhWF3aDIvXQD1HJgomYNw
d3PrLdNUFEMiZEZ18lfKeQgHvgRUflkCQGs1YPeFahv6OodWB/UfhrRziHq/XY+/
739+xcOmoNf2CwQYLbgP17kuB2tJJ9h64qTuICSrngGDReTZix2lWo0CQDOv3c5u
LWQpvn7f9A7DztjWiBbdlSEcUu+XvrrfM0TKjmADC/xr8/0HT4bUDmLuJt3RqJax
cBnNUYpTiF6ghX4=
-----END RSA PRIVATE KEY-----'; 
 
$public_key = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDRWqmMdbMmMMgZHN+RVvVQkQU8
EBxYllsqGgDDQEVTuNlT6BvrIqRiAJEb5L3nqbuDnQH2gZN5tgxoclqkec8cCB4w
Dx1d4Zf83jyt9o9aXMyXvqk/CyyFdpG76eKNK8HyW3vrqVDTUHN2xEK45uFjAYxI
AKKzPOKKU4Y3E6+hMwIDAQAB
-----END PUBLIC KEY-----'; 
 
//echo $private_key; 
$pi_key =  openssl_pkey_get_private($private_key,'12345asdfasdfasdfasfd6a');//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id 
$pu_key = openssl_pkey_get_public($public_key);//这个函数可用来判断公钥是否是可用的 
print_r($pi_key);echo "<br />"; 
print_r($pu_key);echo "<br />"; 
 
 
$data = "aassssasssddd111111111";//原始数据 
$encrypted = "";  
$decrypted = "";  
 
echo "source data:",$data."<br />"; 
 
echo "private key encrypt:"."<br />"; 
 
openssl_private_encrypt($data,$encrypted,$pi_key);//私钥加密 
$encrypted = base64_encode($encrypted);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
echo $encrypted."<br />"; 
 
echo "public key decrypt:"."<br />"; 
 
openssl_public_decrypt(base64_decode($encrypted),$decrypted,$pu_key);//私钥加密的内容通过公钥可用解密出来 
echo $decrypted."<br />"; 
 
echo "---------------------------------------<br />"; 
echo "public key encrypt:"."<br />"; 
 
openssl_public_encrypt($data,$encrypted,$pu_key);//公钥加密 
$encrypted = base64_encode($encrypted); 
echo $encrypted."<br />"; 
 
echo "private key decrypt:"."<br />"; 
openssl_private_decrypt(base64_decode($encrypted),$decrypted,$pi_key);//私钥解密 
echo $decrypted."<br />"; 