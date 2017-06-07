<?php
//账号 15014600030 密码：123456、
error_reporting(0);

//使用公钥加密
function public_encode($pwd) {
   // $publicstr = file_get_contents('./rest_api_public_key.pem');

	$publicstr = file_get_contents('./p2p20140616.cer');
    $publickey = openssl_pkey_get_public($publicstr); // 读取公钥
	
    $r = openssl_public_encrypt($pwd, $encrypted, $publickey);
    if ($r) {
        return $encrypted;
    }
    return false;
}
//使用公钥解密
function public_decode($data) {
    $publicstr = file_get_contents('./p2p20140616.cer');
    openssl_public_decrypt($data,$decrypted,$publicstr);//私钥加密
    if ($decrypted) {
        return $decrypted;
    }
    return false;
}

//使用私钥解密
function rsa_decode($data) {
	$private_key = file_get_contents('./p2p20140616.pem');
    openssl_private_decrypt($data,$decrypted,$private_key);//私钥加密
    var_dump($decrypted);exit;
    if ($decrypted) {
        return $decrypted;
    }
    return false;
}


function curl_post_contents($url, $postField, $timeout = 30)
{
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postField );
	curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout );
	if (isset ( $_SERVER ['HTTP_USER_AGENT'] ))
		curl_setopt ( $ch, CURLOPT_USERAGENT, $_SERVER ['HTTP_USER_AGENT'] );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$r = curl_exec ( $ch );
	curl_close ( $ch );
	return $r;
}
	//bin2hex

//
/**
 *
 * 使用公钥加密密码
 *
 */
$pwd = bin2hex(public_encode('123456'));

//$url = "http://api.d.vding.wang/v1/service/user/login";
$url = "http://api.vding.dev/v1/service/user/login";
$postField=array(
    "username" =>"15014600030",
    "password" =>$pwd,
    "login_type" =>'password_login'
);
$output=curl_post_contents($url, $postField);

$_output = json_decode($output,true);

//服务器使用私钥加密的token
$private_encode_token = $_output['data']['token'];


//1.使用公钥解密token
$_private_encode_token =   hex2bin(strtolower($private_encode_token));
$_private_encode_token_res = public_decode($_private_encode_token);


//2.使用公销加密 token
//客户端加密方式： 转换为大写(转换为十六进制ASCII字符(rsa 公钥加密('plain text')))
$public_token = strtoupper(bin2hex(public_encode($_private_encode_token_res)));

echo $public_token;exit;




//私销解密
/*
$_pwd = hex2bin(strtolower($pwd));
 $result=  rsa_decode($_pwd);

var_dump($result) ;

exit;

*/










print_r($output);
?>