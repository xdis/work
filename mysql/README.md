# mysql

- 常用功能
	- 转换类型	
		- CONVERT(xxx,类型) 或 CAST(xxx AS 类型)
			- cast(a as signed) 	//varchar转Int
			- concat(8,’0′) 		//Int转为varcha
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

	- 1=1妙用
		- AND IF(_which_day !='0000-00-00', dj_checklist.which_date = _which_day, '1=1') 

