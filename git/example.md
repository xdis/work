#案例


##向多个仓库推送

- 一次性推送
-  选择性推送

###一次性推送
将远程仓库地址放入origin即可
1. 添加远程仓库
git remote set-url --add origin git@code.csdn.net:lhorse003/work.git

2.推送	
git push -f origin master (注：可以不加-f,如果出现有问题的时候，则加上)

###选择性推送
将远程仓库加入到指定的命名，如csdn的仓库的，命名为csdn
1.添加远程仓库
git remote add csdn git@code.csdn.net:lhorse003/work.git

2.推送
git push csdn master (注： 将master分支推送到远程仓库csdn)


---
