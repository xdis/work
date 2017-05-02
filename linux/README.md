
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