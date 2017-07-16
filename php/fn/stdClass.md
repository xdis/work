# stdClass使用

## 认证宝stdClass使用

```php
header("content-type:text/html; charset=utf8");
// 将下面的接口地址修改为签订合同后提供的正式接口地址
// 注意，接口地址中的“IdentifierService.svc”部分, 字母 I 和 S 必须为大写
$client = new SoapClient ("http://servername:port/IdentifierService.svc?wsdl");

$r = new stdClass();
$r->IDNumber = "201016198010023265"; //要查询的身份证号码
$r->Name = "张三"; //要查询的姓名

$c = new stdClass();
$c->UserName = "problem"; // 将“user”修改为签订合同后提供的正式账号
$c->Password = "prob888"; // 将“pwd”修改为签订合同后提供的密码

$result_json = $client->SimpleCheckByJson(array("request"=>json_encode($r),
"cred"=>json_encode($c)))->SimpleCheckByJsonResult;
$result = json_decode($result_json);
if ($result->ResponseText == "成功"){
//认证结果（一致/不一致/库中无此号)
if ($result->Identifier->Result == "一致"){
    echo "身份信息一致";
}else{
    echo "身份信息不一致或库中无此号";
}
}
else{
echo "查询失败:", $result->ResponseText;
}
?>
```