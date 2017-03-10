#socket
##农行的demo
###socket整理
>0454  是从<ap>至</ap>的长度
```php
$in = "0454   <ap><TransCode>7506</TransCode>	<CorpNo>6637501020962908</CorpNo>	<OpNo>0001</OpNo>	<AuthNo>8B1DAF22CA3FB696D99BCCA5CCB363B6</AuthNo>	<ChannelType>0</ChannelType>	<ReqDate>20160301</ReqDate>	<ReqTime>165135</ReqTime>	<Sign></Sign>	<Version><CcVersion>2</CcVersion><CsVersion>0</CsVersion></Version>	<ReqSeqNo>160000914515</ReqSeqNo>	<Cme><CcIp>127.0.0.1</CcIp></Cme><Cmp><DbProv>15</DbProv>	<DbAccNo>350114010001187</DbAccNo>	<DbCur>14</DbCur></Cmp></ap>";
```

>通过拼装自种运算
>
```php
$str="<ap><FileFlag>0</FileFlag><ProductID>ICC</ProductID><ReqSeqNo>{ReqSeqNo}</ReqSeqNo><AuthNo></AuthNo><OpNo></OpNo><CorpNo></CorpNo><ChannelType>ERP</ChannelType><TransCode></TransCode><CCTransCode>IBAF04</CCTransCode><Cme><ReqSeqNo>{ReqSeqNo}</ReqSeqNo></Cme><MacAddress></MacAddress><FileComress>1</FileComress><Cmp><DbLogAccNo></DbLogAccNo><SumNum>1</SumNum><DbAccNo>157101040012079</DbAccNo><DbProv>15</DbProv><DbCur>01</DbCur><ContFlag>0</ContFlag><BatchFileName>SIGN17030700020093999900004.txt</BatchFileName></Cmp><Corp><DbAccName></DbAccName><NVoucherType>99029999</NVoucherType><NFAccNo>15121301941000317</NFAccNo></Corp><Amt>4.12</Amt></ap>";
$reqSeqNo=date('YmdHis').mt_rand(10000,99999);
$str=str_replace("{ReqSeqNo}",$reqSeqNo,$str);
$in="0".strlen($str);
$newStr= str_pad($in,7," ");
$newStr.=$str;
$in=$newStr;
```

---