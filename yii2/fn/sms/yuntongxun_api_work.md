# 云通讯接口要求
**http://www.yuntongxun.com/doc/rest/restabout/3_1_1_1.html**


## Rest 介绍
 
- 云通讯平台REST 接口为开发者提供主账户信息查询、创建子账户、获取子账户、模板短信、外呼通知、语音验证码、IVR外呼、会议管理和座席管理等功能。
- 当您第一次使用云通讯平台REST API时，您需要在首页注册账号，我们会给您自动分配一个主账户及默认应用，您可以通过默认应用或创建新应用后，调用接口或使用在线功能生成多个子账户，子账户可以让您轻松的管理客户信息以及控制客户使用情况。欲了解子账户更多信息，请参阅创建子账户。
- 由于API是基于REST原则上的，所以它很容易编写和测试应用程序。您可以使用浏览器访问URL，也可以使用几乎任何客户端在任何编程语言与REST API进行交互。


## 云通讯平台 REST Web Service 接口
### 1、Base URL
文档中所有被引用的地址都有如下Base URL：
https://app.cloopen.com:8883/2013-12-26
注意： 为了确保数据隐私，云通讯平台的REST API是通过HTTPS方式请求。

### 2、统一请求包头
业务URL格式：Base URL与业务URL相拼接为完整请求URL
主帐号鉴权：

```
/Accounts/{accountSid}/{func}/{funcdes}?sig={SigParameter}
```
					
### 子帐号鉴权：

```
/SubAccounts/{subAccountSid}/{func}/{funcdes}?sig={SigParameter}
```
					
HTTP标准包头字段（必填）：  
Accept:application/xml;  
Content-Type:application/xml;charset=utf-8;  
Content-Length:256;  
Authorization:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX 
					

**属性说明**

|属性|类型|约束|说明|
|:----|:---|:----- |-----|
|accountSid	|String	|必选|主账户Id。由32个英文字母和阿拉伯数字组成的主账户唯一标识符|
|subAccountSid|	String|	必选|	子账户Id。由32个英文字母和阿拉伯数字组成的子账户唯一标识符|
|SigParameter|	String	|必选	|验证参数，请求URL必须带有此参数，生成规则详见下方说明|
|Accept|	String	|必选	|客户端响应接收数据格式：application/xml、application/json|
|Content-Type|	String|	必选	|类型：application/xml;charset=utf-8、application/json;charset=utf-8|
|Authorization|	String|	必选|	验证信息，生成规则详见下方说明|
|func	|String	|可选	|业务功能，根据业务的需要添加|
|funcdes	|String	|可选	|业务操作，业务功能的各类具体操作分支|

说明   
1. Base URL后跟验证级别   
• Accounts：主帐号鉴权，云通讯平台会对请求中的主帐号和主帐号Token进行验证；   
• SubAccounts：子帐号鉴权，云通讯平台会对请求中的子帐号和子帐号Token进行验证。   
2. SigParameter是REST API 验证参数   
主帐号鉴权：   
• URL后必须带有sig参数，例如：sig=AAABBBCCCDDDEEEFFFGGG。   
• 使用MD5加密（主帐号Id + 主帐号授权令牌 +时间戳）。其中主帐号Id和主帐号授权令牌分别对应管理控制台中的ACCOUNT SID和AUTH TOKEN。   
• 时间戳是当前系统时间，格式"yyyyMMddHHmmss"。时间戳有效时间为24小时，如：20140416142030   
• SigParameter参数需要大写   
子帐号鉴权：   
• URL后必须带有sig参数，例如：sig=AAABBBCCCDDDEEEFFFGGG。   
• 使用MD5加密（子帐号Id + 子帐号授权令牌 +时间戳）。其中子帐号Id和子帐号授权令牌可通过创建子帐号接口得到。   
• 时间戳是当前系统时间，格式"yyyyMMddHHmmss"。时间戳有效时间为24小时，如：20140416142030   
• SigParameter参数需要大写   
3. Authorization是包头验证信息   
• 使用Base64编码（账户Id + 冒号 + 时间戳）其中账户Id根据url的验证级别对应主账户或子账户   
• 冒号为英文冒号   
• 时间戳是当前系统时间，格式"yyyyMMddHHmmss"，需与SigParameter中时间戳相同。   
4. func描述业务功能，funcdes描述业务功能的具体操作   
例如：/ivr/createconf   


3、数据报文格式
云通讯平台REST接口支持两种主流的报文格式：XML和JSON。通过请求包头的字段Content-Type及Accept，即可决定请求包体和响应包体的格式，如：Content-Type:application/xml;charset=utf-8;Accept:application/xml; 表示请求类型格式是XML，要求服务器响应的包体类型也是XML；Content-Type:application/json;charset=utf-8;Accept:application/json; 表示请求类型格式是JSON，要求服务器响应类型也是JSON；  

4、关于REST (REpresentational State Transfer)   
我们的云通讯平台REST API设计的方式非常友好，让你使用起来简单明了，来源于Wikipedia，REST的支持者认为，Web大范围的扩展和增长直接导致了REST设计原则的产生，以下为REST设计原则：   
	1.应用场景和功能都被分为不同的资源   
	2.每一个资源通过一个全局的资源标识以超链接的方式被访问   
	3.所有资源通过共享标准的接口实现客户侧和资源之间的场景转换，资源包括如下两项：   
		• 已经被定义好的一套有约束的操作集。   
		• 一套有约束的内容类别集和可选的命令支持码。   
REST 协议特点：   
• 客户侧请求服务器模式   
• 状态无关   
• 缓存机制   
• 层次结构   
REST的客户端服务器分离理念大大简化了组件执行，降低了语义间连接的复杂度，改进了性能调整的有效性，增加了服务器组件的可扩展性。分层的结构化系统约束允许在不改变接口的情况下引入中间代理、网关、防火墙等多种接入点，并与之通讯，并且通过可扩展性，以共享内存的方式改进系统性能，有助于信息传递。   
通过对消息的约束，把REST的中间处理变为一个独立的自我描述：无状态交互，使用标准的方法和媒体类型。通过这种方式使语义信息，交互数据，响应信息能够明确的显示其使用缓存的能力。   