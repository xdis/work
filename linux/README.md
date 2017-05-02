
- 基本的操作

	- 用户
		- 添加用户
			- useradd xxx  //添加用户  
			- passwd xxx   //修改密码 
		- 删除用户
			- userdel -r xxx //删除用户
			- 或者改一下名字如 _xxx 
	- 权限
		-  chmod -R 755 xxx
	- tar cvzf xx.tar.gz ./xx  	//压缩 
	- for tar in *.tar.gz;  do tar zxvf $tar; done 	//批量解压
	- tar -zxvf xx.tar.gx 	//解压
	- cp -rfi ./* /usr/local/mysql/data/	//全文件夹与文件复制
	- 查看程序是否安装
		- rpm -q make
		- rpm -q gcc
		- rpm -q gcc-c++
	- 杀死进程
		- kill -s 9 `pgrep node`	//杀死进程有node相关的进程 
		- 批量删除进程
			- ps aux | grep 9003 //通常查询有 进程带有关键字 9003的有几十个进程，用下面的命令
			- for thepid in ` ps aux | grep 9003 | awk '{print $2}'` ; do  kill -9 $thepid; done
	- ifconfig eth0 192.168.10.2 netmask 255.255.255.0  //快速设置IP
	- 网关
		- route add default gw xx.xx.xx.xx  //设置网关
		- route -n  //查看网关信息
	 - 设置DNS
		- 
		``` 
			vi /etc/resolv.conf
			nameserver 202.96.134.133
		```
		- qian 设置 (在服务器里不能ping通)
		``` 
			vim /etc/resolv.conf
			nameserver 114.114.114.114
			nameserver 8.8.8.8
		```
	- scp
		-  yum install openssh-clients	//安装
		-  scp @xx:/var/lib/mysql/baidajob/boss_callout_plan.* ./	//远程获取
	- ps
		- ps axu|grep fdfs		//一般查询
		- ps –ef|grep httpd |wc -l 	//统计HTTPD进程数
	- pstree
		- pstree | grep indexer  //查询是否有进程
	- du
		- du --max-depth=1 -h  /*    //批量查询目录与文件大小 
	- lsof
		- lsof |grep delete    //命令可以查看已经删除的但是系统仍然在用的文件这些文件不会释放磁盘空间
	- ag xx  //快速搜索该目录下全文的字符串
- 案例
	- 防止恶性采集
		``` 
		1.top查看CPU的占用，如果20%左右正常，如果过高(在50%以上)肯定有问题。执行2步
		2.ps –ef|grep httpd |wc -l 统计HTTPD进程数，如果过多超过150，则为WEB过忙。执行第3步
		3.tail /var/log/httpd/xx/20120713 -s 5 -f |grep invite- 观察IP访问是否连续出现，如果是，执行第4步。
		4.统计该IP总访问数
		cat /var/log/httpd/xx/20120621  |grep invite- |grep 61.147.91.70  |wc -l
		6.执行iptables -I INPUT -s 61.147.91.70  -j DROP 封杀61.147.91.70访问。
		```
	- 邮件收不到 [httpsqs]
		```
		注：httpsqs 目前是安装在 119.147.213.167
		;消息队列处理
		resources.service.host = "119.147.213.167"
		resources.service.port = "9998"
		resources.service.charset = "UTF-8"
		resources.service.key = "baidajob.com"
		
		1.直接用outlook通过mail.baiadjob.com发邮件测试是否收到，如果收不到就是邮件服务器本身有问题，否则执行第2步。
		2.检查服务器
		/var/www/html/baidajob/application/logs/send_mail.txt文件时间是否最新，如果不是，则消息队列有问题，执行第3步。
		在linux(58.251.129.84)执行ps –ef|grep sqs是否还存用KILL掉进程
		执行：
		httpsqs -d -p 1218 -x /home/httpsqsdata/data0
		/home/wxj/sqs/h.sh
		
		应该运行如下  /home/httpsqs/shell/sqs.sh
		
		
		在后台执行 shell.sh 脚本 
		nohup /home/shell/xx.sh &
		```