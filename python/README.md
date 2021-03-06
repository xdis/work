# python
>版本是 Python3  
>学习 http://www.runoob.com/python3  

## 基础
- 条件控制
	- [if/elif](fn/base.md#if/elif) 
	- [try/except/while](fn/base.md#try/except/while)  
- while循环
	- [while_计算总和](fn/base.md#while_计算总和) 
	- [rang计算总和](fn/base.md#rang计算总和) 
	- [无限循环](fn/base.md#无限循环) 
	- [while循环使用else语句](fn/base.md#while循环使用else语句) 
- for
	- [循环输出数组](fn/base.md#循环输出数组)
	- [循环使用break](fn/base.md#循环使用break)
- break和continue
	- [break和continue语句及循环中的else子句](fn/base.md#break和continue语句及循环中的else子句)
	- [continue语句](fn/base.md#continue语句)
- 函数
	- range  遍历数字序列
		- [生成数列](fn/base.md#生成数列) range(5)
		- [指定区间的值](fn/base.md#指定区间的值) range(5,9)
		- [步长](fn/base.md#步长) range(0, 10, 3)
		- [结合range()和len()函数以遍历一个序列的索引](fn/base.md#结合range()和len()函数以遍历一个序列的索引)
	- enumerate 
		- [for i,j](fn/base.md#for i,j)
- 迭代器 iter 和 next 
	- [逐条输出](fn/base.md#逐条输出)
	- [for遍历](fn/base.md#for遍历)
	- [使用sys模块,使用next()](fn/base.md#使用sys模块,使用next())
- 生成器
	-  [yield实现斐波那契数列](fn/base.md#yield实现斐波那契数列)
- 匿名函数
	-  [lambda创建匿名函数](fn/base.md#lambda创建匿名函数)
- 变量作用域
	- global和nonlocal关键字 #当内部作用域想修改外部作用域的变量时
		- [global_demo](fn/base.md#global_demo)
		- [nonlocal_demo](fn/base.md#nonlocal_demo)
- 类对象
	- 类的专有方法
		- `__init__` : 构造函数，在生成对象时调用
		- `__del__` : 析构函数，释放对象时使用
		- `__repr__` : 打印，转换
		- `__setitem__` : 按照索引赋值
		- `__getitem__`: 按照索引获取值
		- `__len__`: 获得长度
		- `_cmp__`: 比较运算
		- `__call__`: 函数调用
		- `__add__`: 加运算
		- `__sub__`: 减运算
		- `__mul__`: 乘运算
		- `__div__`: 除运算
		- `__mod__`: 求余运算
		- `__pow__`: 称方
	- 私有变量前面加__ 	如	__secretCount = 0
	- 私有方法前面加__	如  def __foo(self): 
	- [访问类的属性和方法](fn/base.md#访问类的属性和方法)
	- [构造函数__init__](fn/base.md#构造函数__init__)
	- [self代表类的实例_而非类](fn/base.md#self代表类的实例_而非类)
	- [类的方法_def和self](fn/base.md#类的方法_def和self)
	- [继承](fn/base.md#继承)
	- [多继承](fn/base.md#多继承)
	- [方法重写](fn/base.md#方法重写)
	- [运算符重载](fn/base.md#运算符重载)
- Python3标准库
	- [操作系统接口](fn/base.md#操作系统接口)
		- os.getcwd()
		- 进入目录 os.chdir('/server/accesslogs')
		- 创建目录  os.system('mkdir today')
- fn
	- python自动化运维篇
		- [ansible](fn/auto_op.md#ansible) [自动化管理IT资源工具]
			- [ansible安装](fn/auto_op.md#ansible安装)
			- [Ansible配置文件路径](fn/auto_op.md#Ansible配置文件路径)
			- [Ansible配置文件获取](fn/auto_op.md#Ansible配置文件获取)
			- 实战操作 (至少须要有2个服务器)
				- [ansible新手上路](fn/auto_op.md#ansible新手上路)
		- [saltstack](fn/auto_op.md#saltstack)
			- [saltstack安装](fn/auto_op.md#saltstack安装)
			- [SaltStack启动](fn/auto_op.md#SaltStack启动)
			- [SaltStack测试](fn/auto_op.md#SaltStack测试)
		- [nagios](fn/auto_op.md#nagios)
			- [nagios安装](fn/auto_op.md#nagios安装)
			- [nagios配置文件](fn/auto_op.md#nagios配置文件)
			- [Nagios主配置文件](fn/auto_op.md#Nagios主配置文件)
			- [nagios安装使用](fn/auto_op.md#nagios安装使用)

