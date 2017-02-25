
- Yii2
	- 事件
		- [事件-邮件发送](事件-邮件发送.md)
		- [事件-简单例子](事件-简单例子.md)
	- widget
		- [widget简单例子](widget.md#widget简单例子)
			- [1创建一个TestWidget](widget.md#1创建一个TestWidget)
			- [2view页调用](widget.md#2view页调用)
		- widget仿WeUI上传插件 (自定义图片上传插件)
			- [Widgt主文件](widget.md#widgt主文件)
			- [Asset配置](widget.md#asset配置)
			- [视图调用](widget.md#视图调用)
	- AssetBundle
		- [AssetBundle简单使用](AssetBundle.md#AssetBundle简单使用)
			- [自定义css和js](AssetBundle.md#自定义css和js)
			- [js解决依赖关系](AssetBundle.md#js解决依赖关系)
			- [view层使用](AssetBundle.md#view层使用)
		- 
	- view
		- [时间-input输入框选择](view.md#输入框选择日期)
		- [radio自定义模板](radio自定义模板.md)
		- [时间字段年月日显示](view.md#时间字段年月日显示)
		- [时间区间范围的选择](view.md#时间区间范围的选择)
		- [lookup](lookup.md)
			- 配置
			- 使用
				- 1.数据入库
				- 2.view使用
		- [自定义列表页_按纽模板](view.md#自定义列表页_按纽模板) (如某个删除与编辑按纽不要，可加别的)
			- [自定义列表页_按纽模板_增加判断](view.md#自定义列表页_按纽模板_增加判断)
			- [自定义列表页_按纽模板_增加判断_小明](view.md#自定义列表页_按纽模板_增加判断_小明)
		- URL地址生成
			- [url::to](view.md#url_to)
			- [Html::a](view.md#html_a)
		- [列表页_自定义编号_删除](view.md#列表页_自定义编号_删除)
    - controller
	    - 接收数据与验证 (注:post提交)
		    - [接收post再验证_标准例子](post.md#接收post再验证_标准例子)（包括事务）
		    - [接收post再验证_例a](post.md#接收post再验证_例a) ($model->save())
		    - [接收post再验证_例b](post.md#接收post再验证_例b)  ($model->save(false))
		- yii2自带函数连接
			- [leftJoin](post.md#leftjoin)
			- [leftjoin_详细页](post.md#leftjoin_详细页)
			- [leftJoin+分页_wqw](post.md#leftjoin_分页_wqw)
			- [leftJoin+分页_接口_视图_搜索](post.md#leftJoin_分页_接口_视图_搜索)
		- 查询连环招
			- [利用模型get表关联查询_wqw](post.md#利用模型get表关联查询_wqw)
    - model
	    - [managesearch方法_个性化+自定义配置](model.md#managesearch方法_个性化+自定义配置)
		    - [使用子查询使用](model.md#使用子查询使用)
			- [列表页使用](model.md#列表页使用)
			- search关联用户表取username
				- [1.配置UserActivity模型](model.md#1配置useractivity模型)
				- [2.配置UserActivitySearch模型](model.md#2配置useractivity_search模型)
				- [3.列表页显示](model.md#3列表页显示)
			- search关联用户表取mobile以别名形式
				- [a.配置UserActivity模型](model.md#a配置useractivity模型)
				- [b.配置UserActivitySearch模型](model.md#b配置useractivity_search模型)
				- [c.列表页显示](model.md#c列表页显示)
			- 条件过滤_where
				- [where_普通使用](model.md#where_普通使用) (andwhere)
				- [where_大于小于](model.md#where_大于小于) (andFilterWhere)
				- [where_like查询](model.md#where_like) (andFilterWhere)
	- 配置
		- [路由设置-伪静态](content.md#路由设置-伪静态) 

- 疑问
	- [为什么日志不入数据库](question.md#为什么日志不入数据库)
	- 

- 案例 
	- [店铺](shop/README.md)
		- 核心技术
			- [1后端跨域解决方案](shop.md#1后端跨域解决方案)  
			- [2怎样在请求头加上Companyid和Ownerid两个参数](shop.md#2怎样在请求头加上Companyid和Ownerid两个参数)   
			- [3在请求时候_后端怎样获取Header里的两个参数](shop.md#3在请求时候_后端怎样获取Header里的两个参数)
			- [3_1php获取header的函数解析](shop.md#3_1php获取header的函数解析)
	- 短信模块分析
		- 
	- 权限分析_yii2_admin
	- App接口全局配置






