#案例

##转移别人的bit仓库到我自己
```
git clone --bare git://github.com/username/project.git
cd project.git
git push --mirror git@gitcafe.com/username/newproject.git
cd ..
rm -rf project.git
git clone git@gitcafe.com/username/newproject.git

```

##将当前的目录上传至github
```
echo "# test-design" >> README.md
git init
git add README.md
git commit -m "first commit"
git remote add origin git@github.com:408824338/test-design.git
git push -u origin master

如果发现add的orgin错的话
先输入：$ git remote rm origin
再输入：$ git remote add origin git@github.com:heshaui/pdfjsDemo.git
```

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
git push csdn master (注： 将master分支推送到远程仓库csdn的master分支  
git push [alias] [branch]，就会将你的 [branch] 分支推送成为 [alias] 远端上的 [branch] 分支。)

---
##本地仓库里加入多个远程仓库_fetch_commit到本地仓库
>场景：仓库A，仓库B，想要利用仓库A的数据


### 进入仓库B,添加远程仓库A，仓库名为test7
```
git remote add test7 ssh://git@222.111.222.7:5807/git-test.git

```

### 查看远程仓库情况
```
git remote -v

```

### 查看远程仓库的分支情况
```
git remote show test7

>>输出 dev 和 master 分支
  Remote branches:
    dev    tracked
    master tracked

```

### 下载远程仓库的数据

```
git fetch test7

```

### 查看远程仓库的log情况，如想查test7分支dev的情况

```
git log test7/log

```

### 使用test7分支dev的commit
```
git cherry-pick commit

```

### 添加与推送
```
git add .  
git pull --rebase origin dev  
git push origin dev

```

---

