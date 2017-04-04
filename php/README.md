# PHp

- 干货
	- [符合PSR-1/PSR-2的PHP编程规范实例](standard.php)
- socket
	- 农行的demo
		- [socket整理](socket.md#socket整理) 
		- [查询](socket/s1.php) 
		- [分账](socket/c1.php)
- RestfulApi
	- 会飞的鱼Xia
		- [例子说明_会飞的鱼Xia](RestfulApi.md#例子说明_会飞的鱼Xia)
		- [RestfulApi初始化](https://github.com/408824338/RestfulApi_i/blob/master/restful/index.php)
		- [使用postman授权设置与使用](RestfulApi.md#使用postman授权设置与使用)
		- [代码中使用用户与密码来登陆](RestfulApi.md#代码中使用用户与密码来登陆)
		- 本例子中使用php://input来获取参数,我使用postman来模拟,获不取不到数据
		- 什么时候使用file_get_contents('php://input') 
	- [使用postman模拟参数php解析获取](shop.md#使用postman模拟参数php解析获取)   // PHP_AUTH_USER 和  PHP_AUTH_PW
- composer
	- 常用 
		- C:\Users\cmk\AppData\Local\Composer window路径 
		- composer clear-cache 清空缓存
		- composer update fihacklog/yii2-sms-module  单个更新
		- composer update ihacklog/yii2-sms-module  --prefer-source 更换仓库
	- 问题
		- 1.某个包修改了某个加载仓库路径,一直不是指定的仓库加载路径
		- 回答:
			- 1可以进入该目录里,然后执行git pull origin master,但如果将该目录删除,执行composer install还是行
			- 2.彻底的解决办法:将composer.lock删除,再执行composer install
		