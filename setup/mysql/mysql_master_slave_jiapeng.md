#主从配置_jiapeng

>mysql版本 5.6.22
>服务器 192.168.1.3   192.168.1.5    

>主库备份的数据库有 
dijie-dream  
dijie-dream_admin  
dijie-dream_log  
dijie-dream_chat_log  

##总结如下
主库可以不设置binlog-do-db，可能默认是代表全部，只要要从库里设置replicate-do-db设置要同步的数据库即可


##主库配置
[配置文件](3.my.cnf)
```
#######Basic##############
[mysqld]
server-id = 1
port = 3306
user = mysql
datadir = /var/mysql/data
#basedir=/usr
socket=/var/mysql/data/mysql.sock
default-storage-engine=INNODB
wait_timeout=60
connect_timeout=20
character-set-server=utf8
skip-name-resolve
#interactive_timeout=100
back_log=512
myisam_recover

```


##从库配置
[配置文件](5.my.cnf)
```
#######Basic##############
[mysqld]
server-id = 16
port = 3306
user = mysql
datadir = /var/mysql/data
#basedir=/usr
socket=/var/mysql/data/mysql.sock
default-storage-engine=INNODB
wait_timeout=60
connect_timeout=20
character-set-server=utf8
skip-name-resolve
#interactive_timeout=100
back_log=512
myisam_recover

######replicate database##########
replicate-do-db=dijie-dream
replicate-do-db=dijie-dream_admin
replicate-do-db=dijie-dream_log
replicate-do-db=dijie-dream_chat_log
slave-skip-errors=1032


``` 