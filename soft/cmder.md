#Cmder
来源：http://blog.csdn.net/lhorse003/article/details/60959504

##配置
###配置目录
![](cmder/config.png)

###方案1  
>下面的两个修改是不能执行linux命令的 

#### 参数1
```
 /icon "%CMDER_ROOT%\icons\cmder.ico"
```
#### 参数2
```
cmd /k "%ConEmuDir%\..\init.bat"  -new_console:d:E:\cmk\qian100\web\vding\vding_dev -cur_console:t:vding
```

###方案2 

#### 参数1  为空
```
完整的例子如下
```
"%ConEmuDrive%\Program Files\Git\git-cmd.exe" --no-cd --command=usr/bin/bash.exe -l -i  -new_console:d:E:\cmk\qian100\web\vding\vding_dev -cur_console:t:vding
```


###配置默认启动目录
![](cmder/config_start.png)

##常用快捷键

###标签切换
![](cmder/cmder_tab.png)