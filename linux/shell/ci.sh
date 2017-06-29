#!/bin/bash

# 使用tortoisegit弹出提交框 - 主要用于展示当前目录下有哪些文件要提交,与比较文件变化 

director=`pwd`

#乌龟的执行的bin目录 C:/Program Files/TortoiseGit/bin 加入系统的path里云
#使用
# ci.sh  //commit提交弹框
# ci.sh log //查看当前目录的日志列表


if [ "$1" = 'log' ]; then
  TortoiseGitProc.exe /command:log
  exit
fi

TortoiseGitProc.exe /command:commit