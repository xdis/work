
- 基本的操作
	- vi相关命令操作
		- : / test       //搜索内容
		- u //撤消
		- d //删除
		- yy 或 数字+y 如6yy //复制 及当前行开始复制6行 
		- p //粘贴
	- 删除文件
		- rm -fr *.*
		- find ./ -iname 'test-file-*' | xargs rm -rf //注：xargs是因为rm -rm 删除会显示列表页过长导致出现，才使用
	- curl [网络是否通]
		- curl www.qian100.com -v
		- curl www.qian100.com -vvv
	- 用户
		- 添加用户
			- useradd xxx  //添加用户  
			- passwd xxx   //修改密码 
		- 删除用户
			- userdel -r xxx //删除用户
			- 或者改一下名字如 _xxx 
	- 权限
		-  chown -R git:git
		-  chmod -R 755 xxx
	- tar cvzf xx.tar.gz ./xx  	//压缩备份 
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
		- route
		- route add default gw xx.xx.xx.xx  //设置网关
		- route -n  //查看网关信息
	- 设置DNS
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
	- python
		- python2.6升级到2.7
			- [更新系统和开发工具集](fn/python/2.6_t_2.7.md#更新系统和开发工具集)
			- [源码安装Python 2.7.x](fn/python/2.6_t_2.7.md#源码安装Python 2.7.x)
			- [建立软连接，使系统默认的 python指向 python2.7](fn/python/2.6_t_2.7.md#建立软连接，使系统默认的 python指向 python2.7)
			- [修复yum](fn/python/2.6_t_2.7.md#修复yum)
			- [安装setuptools](fn/python/2.6_t_2.7.md#安装setuptools)
			- [安装PIP](fn/python/2.6_t_2.7.md#安装PIP)
			- [安装pycurl报错](fn/python/2.6_t_2.7.md#安装pycurl报错)
			- [ERROR:root:code for hash md5 was not](fn/python/2.6_t_2.7.md#ERROR:root:code for hash md5 was not)
	- vim
		- nerdtree 显示目录树及文件
			- [官网](https://github.com/scrooloose/nerdtree) 
			- [安装pathogen](fn/vim/pathogen.md#安装pathogen)
			- 安装apt-vim
				- [官网](https://github.com/egalpin/apt-vim) 
				- 自动安装
					- curl -sL https://raw.githubusercontent.com/egalpin/apt-vim/master/install.sh | sh
			- 安装nerdtree
				- cd ~/.vim/bundle
				- apt-vim install -y https://github.com/scrooloose/nerdtree.git
			- 配置nerdtree
				- [配置文件](fn/vim/nerdtree.md)
				- [.vimrc文件](fn/vim/.vimrc)
				- [官网配置详解](https://github.com/scrooloose/nerdtree#faq) 
					- |map <C-n> :NERDTreeToggle<CR> 	//ctrl+n 切换是否显示nerdtree
					- |autocmd VimEnter * wincmd p 		//vim打开自己定位到代码
					- autocmd bufenter * if (winnr("$") == 1 && exists("b:NERDTree") && b:NERDTree.isTabTree()) | q | endif  //退出VIM.自动关闭nerdtree
			- 快捷键
				- ctrl + w + h    光标 focus 左侧树形目录
				- ctrl + w + l    光标 focus 右侧文件显示窗口
				- ctrl + w + w    光标自动在左右侧窗口切换 #！！！
				- ctrl + w + r    移动当前窗口的布局位置
	- scp
		-  yum install openssh-clients	//安装
		-  scp @xx:/var/lib/mysql/baidajob/boss_callout_plan.* ./	//远程获取
		-  从linux系统复制文件到windows系统
			-  scp /oracle/a.txt  administrator@192.168.3.181:/d:/
		-  将windows下的文件复制到linux系统中
			-  scp administrator@192.168.3.181:/d:/test/config.ips  /oracle
		-  使用指定端口
			-  scp -P 5803 root@192.168.1.3:/home/Python-2.7.8.tar /home
	-  rsync使用
		-  来自 gitSetup git/发布代码/Jenkins_配置
			-  rsync -avz --exclude=storage/web/source   /var/lib/jenkins/workspace/online_vding/  web1:/mnt/vdb1/2.vding.wang/ 
	-  ansible 
		- 来自 gitSetup git/发布代码/Jenkins_配置
			- ansible web -m shell -a 'cd /mnt/vdb1/2.vding.wang/vfet/;npm run build'
	- nfs 目录镜像挂载到别人服务器  来自 gitSetup nfs
	- 查看 
		- uname -a  //查看系统多少位 32位/64
		- ps
			- ps axu|grep fdfs		//一般查询
			- ps –ef|grep httpd |wc -l 	//统计HTTPD进程数
		- df -h 	//查看硬盘以G来看情况
		- free -g 	//查看内存使用状态
		- pstree
			- pstree | grep indexer  //查询是否有进程
		- du
			- du --max-depth=1 -h  /*    //批量查询目录与文件大小 
		- lsof
			- lsof |grep delete    //命令可以查看已经删除的但是系统仍然在用的文件这些文件不会释放磁盘空间
		- tail
			``` 
			如果你想查看文件的后10行，可以使用tail命令，如：
			tail 10 /etc/passwd
			tail -f 10 /var/log/messages
			参数-f使tail不停地去读最新的内容，这样有实时监视的效果
			```
		- 查看root的命令的历史数据
			``` 
			 cat /root/.bash_history
			 history | grep 'checksum'
			```
		- netstat
			- netstat -ntpl		//查询端口列表
			- netstat -tunpl
	- ssh
		- ssh 202.104.102.444 -p 5804  //远程登陆
	- 防火墙
		- 设置
			``` 
			 vim /etc/sysconfig/iptables
			/etc/init.d/iptables restart
			```
		- 查看
			- iptables -L -n
	- 更新linux时间
		- 方法1 [手动修改指定的时间]
			``` 
			ntpdate time.nist.gov
			date -s "2016-01-07 10:25:25"
			```
		- 方法2 [定时任务]
			- */15 * * * * ntpdate -u pool.ntp.org >> /var/log/ntpdate.log
		- 方法3 
			- */15 * * * * rdate -s stdtime.gov.hk
		- 方法4 [加朋]
			``` 
			 vim /etc/sysconfig/iptables
			/etc/init.d/iptables restart
	- 查找与搜索
		- find / -name my.cnf	//查找命令 find
		- ag xx  //快速搜索该目录下全文的字符串	```
			- 安装 [https://github.com/ggreer/the_silver_searcher]
				- centos安装
					- 1.安装关联
						``` 
						  yum -y groupinstall "Development Tools"
						  yum -y install pcre-devel xz-devel
						```
					- 下载包与安装
						``` 
						wget https://github.com/ggreer/the_silver_searcher/archive/master.zip
						mv master ag.zip
						unzip ag.zip
						cd the_silver_searcher-master/
						./build.sh
						make install
						```
				- windows安装
					- choco安装
						- choco install ag		//安装
						- choco upgrade ag 		//更新
						- ag xx ./*   //最后加上 ./*是比较完整,否则有些目录是不会搜索
	  - pt xx  //搜索神器 [windows下执行]
		  - choco install pt //安装
		  - pt xx ./* 或 ./    //最后加上 ./* 或 ./ 是比较完整,否则有些目录是不会搜索
		- cat /etc/redhat-release  //查看 linux 发布版本
	- choco安装
		- 以管理员权限打开cmd
			- @powershell -NoProfile -ExecutionPolicy Bypass -Command "iex ((new-object net.webclient).DownloadString('https://chocolatey.org/install.ps1'))" && SET PATH=%PATH%;%ALLUSERSPROFILE%\chocolatey\bin
			- 或 管理员权限的Powershell
				- iex ((new-object net.webclient).DownloadString('https://chocolatey.org/install.ps1'))
		- 常用命令
			- choco search xx
			- choco install xx
			- choco uninstall xx
			- choco upgrade xx 
	- yum
		- 更新系统和开发工具集
			- yum -y update
			- yum groupinstall "Development tools"
		- yum install bind-utils	//安装
		- 查看某个命令是属于哪个软件包
			- yum provides "*bin/nslookup"
			- yum provides "*nslookup"
		- 更换yum 163	[网易163镜像](http://mirrors.163.com/.help/centos.html)
			- 一般操作
				- mv /etc/yum.repos.d/CentOS-Base.repo /etc/yum.repos.d/CentOS-Base.repo.old
				- cd /etc/yum.repos.d/ 
				- wget wget spacer.gifhttp://mirrors.163.com/.help/CentOS6-Base-163.repo
				- mv CentOS6-Base-163.repo CentOS-Base.repo 
				- yum clean all & yum makecache
			-  **yum makecache报错** [CentOS-Base.repo最终文件](fn/yum/CentOS-Base.repo) [centos 6.8]  by 20170512 11:02
			```
				报错原因?
				http://mirrors.163.com/centos/6/os/x86_64/repodata/repomd.xml: [Errno 14] PYCURL ERROR 22 - "The requested URL returned error: 404 Not Found
			```
				- 解决方案:
				- cat /etc/redhat-release  //查看当前的centos版本是几 如 6.8
				- CentOS-Base.repo数据替换
					- mirrors.163.com 替换 vault.centos.org 	
					- $releasever 替换为 6.8
					- rpm -qi centos-release   //查看$releasever的值 
					-  yum clean all & yum makecache		
		-  xz 
			-  yum -y install xz xz-devel   //手动安装
			-  解压方法
				- xz -d ***.tar.xz
				- tar -xvf  ***.tar
		- pip 
			- centos 7
				-  yum -y install epel-release
				-  yum install python-pip
				-  pip install --upgrade pip
	- dig安装
		- linux
			- 方法1 [依赖工具安装]
				- yum install bind-utils	//Fedora / Centos
				- sudo apt-get install dnsutils		//Ubuntu
				- Debian
					- apt-get update
					- apt-get install dnsutils
			- 方法2 
				- 复杂 - 不建议
		- window [详情地址](http://blog.csdn.net/lhorse003/article/details/71629960)
			- 去镜像网站下载 ftp://ftp.nominum.com/pub/isc/bind9/
				- 选择版本 如 9.9.7 
				- 选择多少位 BIND9.9.7.x64.zip
			- 解压
				- 将所有的*.dll和dig.exe文件复制到C:\Windows\System32\
			- 使用Google Public DNS 
				- 修改该文件  C:\Windows\System32\drivers\etc\resolv.conf
				- 内容如下
					- nameserver 202.96.134.33
					-  nameserver 8.8.8.8
					-  nameserver 8.8.4.4
	- shell
		- window
			- start .  //命令行下打开当前目录 
		- 让你的git_pull之后自动运行命令 如yarn run build
			- [调用函数代码](fn/shell.md#调用函数代码)
		- [改造cp命令_文件不存在则创建_并且过滤文件名](fn/shell.md#改造cp命令_文件不存在则创建_并且过滤文件名)
		- [加入日志模块_锁模块_注释参考_模板shell](fn/shell.md#加入日志模块_锁模块_注释参考_模板shell) @赵班长
		- [自动化部署脚本](fn/shell.md#自动化部署脚本) @赵班长
	- 域名常见问题
		- DNS检测
			- 域名whois检测
				- https://www.whois.com/whois/vding.wang  
				- https://support.dnspod.cn/Tools/tools/
		- nslookup
		- dig使用
			- 测试该域名能否正常解析
				- dig vding.wang
				- dig @8.8.8.8 vding.wang  //使用8.8.8.8作为指定SERVER
					- [解析成功](fn/dig.md#解析成功)
					- [解析失败](fn/dig.md#解析失败)
			- 查询MX记录 MX（Mail Exchanger）记录查询
				- dig redhat.com  MX +noall +answer
				- dig -t MX redhat.com +noall +answer  		//后者`-t`代表查询类型，可以是`A`,`MX`,`NS`等,`+noall` 代表清除所有显示的选项
			- dig -t NS chenrongrong.info +noall +answer 		//查询域名服务器
			- dig -t ANY chenrongrong.info +answer		//查询所有DNS记录
				- [vding为例](fn/dig.md#vding为例)
				- [baidajob为例](fn/dig..md#baidajob为例)
			- dig -t NS chenrongrong.info +short		//简洁显示+short
			- dig -x 8.8.8.8 +short		//DNS反向解析dig -x
			- dig cname www.baidu.com +short		//显示域名的CNAME记录
			- dig -h 	//帮助
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
	- 程序后台运行并开机自动启动
		- 以"搭建IntelliJ IDEA license server"为例
			- 根据选择的执行文件编写启动脚本，选择运行平台对应的可执行文件
			```
			vim /usr/ideaActivation/start.sh
			# 文件内容：
			#! /bin/bash
			sudo /usr/ideaActivation/IntelliJIDEALicenseServer_linux_amd64 & bg
			```
			- 设置执行权限：
			```
			chmod a+x /usr/ideaActivation/start.sh
			```
			- 开机启动：
			```
			# 编辑开机启动文件
			vim /etc/rc.d/rc.local
			# 在文件尾加入启动脚本，开机自动启动：
			#!/bin/sh
			#secu_agent init monitor, install at Sat Mar 26 02:55:13 CST 2016
			/usr/ideaActivation/start.sh
			```