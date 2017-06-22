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
	- 批量查询表的记录数量
		- use information_schema
```
SELECT CONCAT(
    'select "', 
    TABLE_name, 
    '", count(*) from ', 
    TABLE_SCHEMA, 
    '.',
    TABLE_name,
    ' union all'
) FROM TABLES 
WHERE TABLE_SCHEMA='数据库名';
```
	- 1=1妙用
		- AND IF(_which_day !='0000-00-00', dj_checklist.which_date = _which_day, '1=1') 
	- 动态执行sql
		```
		BEGIN
		 SET @sql = concat('select * from ', $tableName);	 
		 PREPARE stmt1 FROM @sql;
		 EXECUTE stmt1;
		 DEALLOCATE PREPARE stmt1;
		END;
		```
   - 子查询 [子查询的好处就是  如果子条件不符合，父记录。。也会存在（传统的情况下，父记录是不存在的）]

		```
		left join (SELECT Amount,CreateTime,LoanId FROM invest WHERE IsValid = 1 AND invest.TransferFlag != 3) AS invest 
		```
	- 找回密码
	  - 方法1
		  - 在 [mysqld]  加入 skip_grant_tables
		  - 重启,mysql -uroot -p //即可进入
	  - 方法2
		  -  mysqld_safe --skip-grant-tables&
	  - 方法3
		  - mysqld_safe –skip-grant-tables &
		  - mysqld_safe --skip-grant-tables >/dev/null 2>&1 &
	- 创建用户与授权
	  - insert into user(host,user,password) values('%','root',password('!123456'));  //创建一个帐号
	  - update mysql.user set password=password('123456') where User='root'; //root重置密码
	  - grant all privileges on *.* to root@"%";  //允许远程连接
	  - grant all privileges on *.* to 'bitnami'@'%' identified by 'a4f90127b5'; （bitnami 为用户名，a4f90127b5 为密码） 
	  - FLUSH PRIVILEGES; 
- 配置文件 my.cnf
	- 仅允许本地127.0.0.1连接
		- [mysqld] bind-address=127.0.0.1
	- 修改数据库,不使用密码
		- [mysqld] skip_grant_tables