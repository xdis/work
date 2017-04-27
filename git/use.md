## 配置

## 分支
### 分支创建策略
http://www.cnblogs.com/likwo/p/3179651.html   
分支策略：git上始终保持两个分支，master分支与develop分支。master分支主要用于发布时使用，而develop分支主要用于开发使用。  

创建master的分支develop   
git checkout -b develop master   

切换到master分支   
git checkout master    

合并develop分支到master   
git merge --no-ff develop  //使用-no-ff   
 

除了以上两个常驻分支外，我们还可以适当分支出三种分支：功能分支、预发布分支、修补分支，这三种分支使用完后也该删除，保持两个常驻分支。   

功能分支：该分支从develop中分支出来，开发完成后再合并入develop，名字采用feature-* 的形式命名。  
创建功能分支：  
　　git checkout -b feature-x develop  
开发完成后，合并到develop分支：  
　　git checkout develop  
　　git merge --no-ff feature-x  
最后删除分支:  
　　git branch -d feature-x  


预发布分支：正是版本发布前，既合并到master分支前，因此预发布分支是从develop分支出来的，预发布后，必修合并进develop和master。命名采用release-*的形式。  
创建一个预发布分支：  
　　git checkout -b release-* develop  
确认版本没有问题后，合并到master分支：  
　　git checkout master   
      git merge --no-ff release-*  
对合并生成的新节点，做一个标签：  
　　git tag -a 1.2  
再合并到develop分支:  
　　git checkout decelop  
　　git merge --no-ff release-*  
最后删除分支: 
　　git branch -d release-*  



修补分支：主要用于修改bug的分支，从master分支分出来，修补后，在合并进master和develop分支。命名采用fixbug-*形式。 
创建一个修补分支： 
　　git checkout -b fixbug-* master 
修补结束后,合并到master分支: 
　　git checkout master 
　　git merge --no-ff fixbug-* 
　　git tag -a 0.1.1 
再合并到develop分支: 
　　git checkout develop 
　　git merge --no-ff fixbug-* 
最后删除分支: 
　　git branch -d fixbug-* 

---

### 合并
#### 合并例子
```
//子分支feature开发完毕,合并到dev分支
#git checkout dev
#git merge feature  合并分支 将feature合并到dev去
git merge -no-ff feature  // 使用no-ff来合并，好处有节点，可以随时回滚
```



## 一般使用流程
git add .  
git ci -m '备注'  
git pull --rebase origin master  
git push origin master  

## 开发环境与测试环境的切换
### 开发环境
git co dev  
git pull --rebase origin master //使用rebase模式  
git push origin dev  
git log  

### 测试环境
git co test  
git cp commit  
git pull origin test  
git push origin test  


## 创建分支
在开发分支(dev),创建一个子分类 feature  
```
#git checkout dev
#git checkout -b feature  //创建分支
```

## 回滚

>有三个方式   --hard  --soft --mix（默认）  

--hard 改变引向指向 替换工作区  替换暂存区
--soft 仅改变引向指向
--mix  改变引向指向 替换暂存区

$ git reset HEAD^
$ git reset --mixed HEAD^
$ git reset --hard HEAD^

### 回滚到指定版
```
git reset --hard e377f60e28c8b84158
```
### 文件回滚到指定版本	

```
git log MainActivity.java
$ git reset a4e215234aa4927c85693dca7b68e9976948a35e MainActivity.java

```
### 撤销未提交的修改的文件	

```
git checkout file

```
### 时光机穿越	

```
#git reflog //查询历史记录
$git reset –hard HEAD@{1} //即可恢复到reset之前的commit上。} //想恢复

```

## git_rebase	

### 跟上游分支同步
```
http://blog.chinaunix.net/uid-27714502-id-3436696.html

导入主分支的指定分支去 而且要求此动作不影响master主分支的开发，也 就是说要暗中完成
如果主master里有些文件更新了，如开发分支是没有的，这个时候使用  

#git checkout dev //切换到dev分支
#git rebase master //将主分支的内容导入过来，

```
### 高级使用
http://www.cppblog.com/deercoder/archive/2011/11/13/160007.aspx  

git stash: 备份当前的工作区的内容，从最近的一次提交中读取相关内容，让工作区保证和上次提交的内容一致。同时，将当前的工作区内容保存到Git栈中。  
git stash pop: 从Git栈中读取最近一次保存的内容，恢复工作区的相关内容。由于可能存在多个Stash的内容，所以用栈来管理，pop会从最近的一个stash中读取内容并恢复。  
git stash list: 显示Git栈内的所有备份，可以利用这个列表来决定从那个地方恢复。  
git stash clear: 清空Git栈。此时使用gitg等图形化工具会发现，原来stash的哪些节点都消失了。 

@marco  
stash的话一定是基于某个版本的，而且将stash从栈内拿出来，一定是需要还原到对应的版本。通过git stash list可以清晰的看到每个stash对应的版本，如：  
stash@{0}: WIP on master: e95dc37 Require administrator authority...   
stash@{1}: WIP on md_develop: fcaa18f Add an object to recieve md...   
比如你需要恢复stash@{1}这时候你需要做的是：   
git checkout fcaa18f   
git stash apply stash@{1}  
尽量不使用类似git stash pop的方法。如果你当前fcaa18f所在的分支有继续往前推进的话，建议在fcaa18f上建立新的分支，然后继续stash@{1}的修改，并将stash@{1}的内容提交到新的分支上: 
git branch new_develop  
git checkout new_develop  
git add FILES  
git commit  
后续再通过cherry-pick的方式，将修改的内容应用到其他需要这个修改的分支之上。  

---

## 冲突	
### rebase模式
1.手动把文件合并 
2.先git --rebase --continue 再不行执行git rebase --skip


### merge模式
1.手动把文件合并 
2.git commit 

其它  
git merge --abort  
git merge --continue  