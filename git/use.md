##配置

## 一般使用流程
- a
	- b
	- c


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


