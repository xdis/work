# 案例

## 安阳与微叮操作流程
>安阳与微叮开发和测试都在8服务器，正式都是4服务器   
>8服务器，安阳只有anyang(正式)和anyang_test(测试)  [注：master废弃]  
>8服务器，微叮有 dev(开发) test(测试)  bata(二测)  master(正式)  
>开发人员仅在微叮上开发  
>注：微叮和安阳其实是一套系统，只不过后面分离出去了  

### 微叮测试commit推送到安阳的测试环境及发布线上  
```
//微叮开发测试仓库
git co dev
git pull --rebase origin dev
git log //获取要提送commit

git co test
git pull origin test
git cp commit  //cherry-pick
git push origin test

//到安阳测试仓库
git fetch vding  //远程仓库
git log vding/test  //获取微叮那边要提交的commit

git co anyang-test  //切换安阳测试分支
git pull --rebase origin anyang-test
git cp commit
git push origin anyang-test  //终于提交到安阳的测试分支

git co anyang		//切换anyang
git pull origin anyang
git cp commit
git push origin anyang //推送到分支

#在线上的仓库里创建一个[标签]定位一下,好让后面回退做出退路
git pull prod anyang  //推送之前,将线上的数据拉下来先
git push prod anyang  //推送到正式

【注：如果量大的话，可以使用merge】

```

**git push prod anyang出现的问题,使用 git pull prod anyang即可**
```
xxx@xxx MINGW64 ding (anyang)
$ git push prod anyang
To ssh://**:5804/anyang-vding.git
 ! [rejected]          anyang -> anyang (non-fast-forward)
error: failed to push some refs to 'ssh://**:5804/anyang-vding.git'
hint: Updates were rejected because the tip of your current branch is behind
hint: its remote counterpart. Integrate the remote changes (e.g.
hint: 'git pull ...') before pushing again.
hint: See the 'Note about fast-forwards' in 'git push --help' for details.

```




## 转移别人的bit仓库到我自己
```
git clone --bare git://github.com/username/project.git
cd project.git
git push --mirror git@gitcafe.com/username/newproject.git
cd ..
rm -rf project.git
git clone git@gitcafe.com/username/newproject.git

```

## 将当前的目录上传至github
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

## 向多个仓库推送

### 一次性推送  
将远程仓库地址放入origin即可  
1. 添加远程仓库  
git remote set-url --add origin git@code.csdn.net:lhorse003/work.git  

2.推送	
git push -f origin master (注：可以不加-f,如果出现有问题的时候，则加上)  

### 选择性推送  
将远程仓库加入到指定的命名，如csdn的仓库的，命名为csdn  
1.添加远程仓库  
git remote add csdn git@code.csdn.net:lhorse003/work.git  

2.推送  
git push csdn master (注： 将master分支推送到远程仓库csdn的master分支  
git push [alias] [branch]，就会将你的 [branch] 分支推送成为 [alias] 远端上的 [branch] 分支。)  

---
## 本地仓库里加入多个远程仓库_fetch_commit到本地仓库
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

### 发布到线上及使用标签回滚

```
//本地
git tag -a fn_line -m '线路发布之前的标签'
git push origin master_20170515_1455
git push origin master


//发现提前的东西有问题,回退到指定标签
git reset --hard fn_line
git push -f origin master //如果报错的话，使用 -f

```