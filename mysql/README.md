# mysql

- 常用功能
	- 转换类型	
		- CONVERT(xxx,类型) 或 CAST(xxx AS 类型)
			- varchar转Int 	用 cast(a as signed) 
			- Int转为varcha  用 concat(8,’0′) 
			- 类型
				- 二进制,同带binary前缀的效果 : BINARY
				- 字符型,可带参数 : CHAR()
				- 日期 : DATE
				- 时间: TIME
				- 日期时间型 : DATETIME
				- 浮点数 : DECIMAL
				- 整数 : SIGNED
				- 无符号整数 : UNSIGNED
			- 举例
				- select cast(‘125e342.83’ as signed) as clm1		//转换正型



