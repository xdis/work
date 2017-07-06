#!/bin/bash

# 使用tortoiseGit 全名为 tg 弹出提交框 - 主要用于展示当前目录下有哪些文件要提交,与比较文件变化 

director=`pwd`

#乌龟的执行的bin目录 C:/Program Files/TortoiseGit/bin 加入系统的path里云
#使用
# tg.sh  默认为commit提交弹框
# tg.sh ci  //commit提交弹框
# tg.sh log //查看当前目录的日志列表

#查看当前目录的日志列表
if [ "$1" = 'log' ]; then
  TortoiseGitProc.exe /command:log
  exit
fi

#commit提交弹框
if [ "$1" = 'ci' ]; then
  TortoiseGitProc.exe /command:commit
  exit
fi

 TortoiseGitProc.exe /command:commit
