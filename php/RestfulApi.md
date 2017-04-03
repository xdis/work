# 例子说明_会飞的鱼Xia
>本例了主要两个模块,一个是用户一个文章的增删改查

##文章的增删改查_定义请求源
/RestfulApi_i/blob/master/restful/index.php  
```php
	/**
	 * 请求用户资源
	 *
	 * @throws     Exception  (description)
	 *
	 * @return     <type>     ( description_of_the_return_value )
	 */
	private function _handleUser(){
		if($this->_requestMethod != 'POST'){
			throw new Exception("请求方法不被允许", 405);
		}
		$body = $this->_getBodyParams();
		if(empty($body['username'])){
			throw new Exception("用户名不能为空", 400);
		}
		if(empty($body['password'])){
			throw new Exception("密码不能为空", 400);
		}
		return $this->_user->register($body['username'],$body['password']);
	}
```

##文章的增删改查_定义请求源
/RestfulApi_i/blob/master/restful/index.php  
```php
	/**
	 * 请求文章资源
	 */
	private function _handleArticle(){
		switch ($this->_requestMethod){
			case 'POST':
				return $this->_handleArticleCreate();
			case 'PUT':
				return $this->_handleArticleEdit();
			case 'DELETE':
				return $this->_handleArticleDelete();
			case 'GET':
				if(empty($this->_id)){
					return $this->_handleArticleList();
				}else{
					return $this->_handleArticleView();
				}
			default:
				throw new Exception("请求方法不被允许", 405);	
		}
	}
```




## 使用postman授权设置与使用

![](RestfulApi/postman_authorization_user_pass.png)

```php
// 在后端里输出
var_dump($_SERVER);exit;
```

## 代码中使用用户与密码来登陆
```php
	/**
	 * 创建文章
	 *
	 * @throws     Exception  (description)
	 *
	 * @return     <type>     ( description_of_the_return_value )
	 */
	private function _handleArticleCreate(){
		$body = $this->_getBodyParams();
		...
		$user = $this->_userLogin($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
		...

	}
```