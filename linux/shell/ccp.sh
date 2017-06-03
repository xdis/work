#!/bin/bash

isFile=`expr index $2 "."` 

s1CheckChar=`expr index $1 "\"`;

if  [[ $isFile -gt 0 ]]; then 

    s1FilterResult = ${$1/"\"/"/"}
else

   s1FilterResult = $1	
fi

echo s1FilterResult

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

echo $filterResult

if [ ! -d "$2" ]; then
  mkdir -p "$filterResult"
fi

cp -R "$1" "$2"
