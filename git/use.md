##配置

## 一般使用流程
git add .  
git ci -m '备注'  
git pull --rebase origin master  
git push origin master  

## 开发环境与测试环境的切换
git co dev
git pull --rebase origin master

git co test
git pull origin master



##对上次的提交的commit的描述进行修改
```
git commit --amend
```

##获取最新的分支列表
```
git fetch origin
```
##创建分支
//在开发分支(dev),创建一个子分类 feature
```
#git checkout dev
#git checkout -b feature  //创建分支
```



