
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