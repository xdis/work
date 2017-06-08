# shell

## 让你的git pull之后自动 运行 yarn run build

### 调用函数代码

![](shell/git_push_after.png)
```
#! /bin/bash
#
# post-checkout hook that checks for changes to composer.lock, and fires a composer install if required.
# Copyright (C) 2017 HuangYeWuDeng <hacklog@80x86>
#
# Distributed under terms of the MIT license.
#
# git hook to run a command after `git pull` if a specified file was changed
# Run `chmod +x post-merge` to make it executable then put it into `.git/hooks/`.

changed_files="$(git diff-tree -r --name-only --no-commit-id ORIG_HEAD HEAD)"

check_run() {
	echo "$changed_files" | grep --quiet "$1" && eval "$2"
}

# you can change composer path and parameters as you need.
check_run vfet "cd vfet && yarn run build"

```

## 改造cp命令_文件不存在则创建_并且过滤文件名

```
#!/bin/bash

# 运行的例子
# 1 ../ccp.sh 'E:\cmk\qian100\web\vding\vding_dev\rest\versions\v1\controllers\UserController.php' rest/versions/v1/controllers/aaa.php
# 2 ./ccp.sh 'E:\cmk\qian100\web\vding\vding_dev\rest\versions\v1\controllers\UserController.php' rest/versions/v1/controllers/
# 3  ccp.sh /e/cmk/qian100/web/vding/vding_dev/common/models/User.php common/models/User.php

if [ ! -n "$1" ]; then
  echo "请输入参数1:源文件路径,如 ccp.sh 'c:\test\1.txt' /test/b/"
  exit
fi


if [ ! -n "$2" ]; then
  echo "请输入参数2:目的文件路径,如 ccp.sh 'c:\test\1.txt' /test/b/"
  exit
fi


isFile=`expr index $2 "."` 

if  [[ $isFile -gt 0 ]]; then   # 查找字符串里包括点(".") 有表示包括文件,则将文件包括点也去掉 
	
	#获取末尾'/'的位置
	strToCheck=$2
	charToSearch='/'
	let pos=`echo "$strToCheck" | awk -F ''$charToSearch'' '{printf "%d", length($0)-length($NF)}'`
    
     #截取开始到末尾出现的/的位置	
     filterResult=`expr substr "$2" 1  $pos` 
else
     filterResult=$2
fi

#echo $filterResult
if [ ! -d "$2" ]; then
  mkdir -p "$filterResult"
fi
cp -R "$1" "$filterResult"

```
